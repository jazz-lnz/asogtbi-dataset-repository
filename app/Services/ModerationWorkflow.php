<?php

namespace App\Services;

use App\Models\DatasetModel;
use App\Models\ReviewModel;
use CodeIgniter\Database\BaseConnection;
use RuntimeException;

class ModerationWorkflow
{
    public const ROLE_USER = 'user';
    public const ROLE_ETHICS = 'ethics_reviewer';
    public const ROLE_TECHNICAL = 'technical_reviewer';
    public const ROLE_ADMIN = 'repository_administrator';

    public function __construct(private ?BaseConnection $db = null)
    {
        $this->db ??= db_connect();
    }

    public static function checklist(string $stage): array
    {
        $lists = [
            ReviewModel::STAGE_TECHNICAL => [
                'archive_readable' => 'The protected ZIP can be downloaded and opened.',
                'metadata_complete' => 'Required metadata is complete and consistent.',
                'documentation_complete' => 'Documentation is sufficient for repository users.',
                'formats_match' => 'Declared formats match the submitted package.',
                'files_suitable' => 'Files are usable and suitable for publication.',
            ],
            ReviewModel::STAGE_ETHICS => [
                'consent_clearance' => 'Consent and ethics clearance are documented or not applicable.',
                'anonymization' => 'Personal or sensitive data is appropriately anonymized.',
                'sensitive_data' => 'Sensitive-data handling and safeguards are adequate.',
                'source_legitimacy' => 'The source and proposed use are legitimate and documented.',
                'access_classification' => 'The requested access classification is appropriate.',
            ],
        ];
        if (! isset($lists[$stage])) {
            throw new RuntimeException('Unknown review stage.');
        }

        return $lists[$stage];
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, array{result: string, note: string}>
     */
    public static function normalizeChecklist(string $stage, array $input): array
    {
        $normalized = [];
        foreach (array_keys(self::checklist($stage)) as $key) {
            $value = $input[$key] ?? null;
            if (is_array($value)) {
                $result = (string) ($value['result'] ?? 'not_reviewed');
                $note = trim((string) ($value['note'] ?? ''));
            } else {
                $result = ! empty($value) ? 'confirmed' : 'not_reviewed';
                $note = '';
            }

            if (! in_array($result, ['confirmed', 'issue', 'not_reviewed'], true)) {
                $result = 'not_reviewed';
            }

            $normalized[$key] = ['result' => $result, 'note' => $note];
        }

        return $normalized;
    }

    /**
     * @param array<string, mixed>|string|null $stored
     * @return array<string, array{result: string, note: string}>
     */
    public static function decodeChecklist(string $stage, array|string|null $stored): array
    {
        if (is_string($stored) && $stored !== '') {
            $decoded = json_decode($stored, true);
            $stored = is_array($decoded) ? $decoded : [];
        }

        return self::normalizeChecklist($stage, is_array($stored) ? $stored : []);
    }

    /**
     * @param array<string, array{result: string, note: string}> $checklist
     * @return array{reviewed: int, confirmed: int, issues: int, total: int}
     */
    public static function checklistProgress(array $checklist): array
    {
        $reviewed = 0;
        $confirmed = 0;
        $issues = 0;
        foreach ($checklist as $answer) {
            if (($answer['result'] ?? 'not_reviewed') !== 'not_reviewed') {
                $reviewed++;
            }
            if (($answer['result'] ?? '') === 'confirmed') {
                $confirmed++;
            }
            if (($answer['result'] ?? '') === 'issue') {
                $issues++;
            }
        }

        return ['reviewed' => $reviewed, 'confirmed' => $confirmed, 'issues' => $issues, 'total' => count($checklist)];
    }

    /**
     * Rank eligible reviewers by current workload, then by how long they have
     * gone without an assignment. The account ID makes final ties deterministic.
     *
     * @param array<int, array<string, mixed>> $reviewers
     * @return array<int, array<string, mixed>>
     */
    public static function rankReviewerWorkloads(array $reviewers): array
    {
        usort($reviewers, static function (array $left, array $right): int {
            $loadComparison = ((int) ($left['active_count'] ?? 0)) <=> ((int) ($right['active_count'] ?? 0));
            if ($loadComparison !== 0) {
                return $loadComparison;
            }

            $leftLastAssigned = trim((string) ($left['last_assigned_at'] ?? ''));
            $rightLastAssigned = trim((string) ($right['last_assigned_at'] ?? ''));
            if ($leftLastAssigned !== $rightLastAssigned) {
                if ($leftLastAssigned === '') {
                    return -1;
                }
                if ($rightLastAssigned === '') {
                    return 1;
                }

                return strcmp($leftLastAssigned, $rightLastAssigned);
            }

            return ((int) ($left['id'] ?? 0)) <=> ((int) ($right['id'] ?? 0));
        });

        return $reviewers;
    }

    /**
     * @return array{review_id: int, reviewer_id: int, reviewer_name: string}|null
     */
    public function autoAssign(int $datasetId, string $stage, int $actorId, string $ip): ?array
    {
        $this->assertReviewStage($stage);

        return $this->transaction(fn (): ?array => $this->autoAssignWithinTransaction($datasetId, $stage, $actorId, $ip));
    }

    public function autoAssignPending(int $actorId, string $ip, ?string $stage = null): int
    {
        $stages = $stage === null
            ? [ReviewModel::STAGE_TECHNICAL, ReviewModel::STAGE_ETHICS]
            : [$stage];
        $assigned = 0;

        foreach ($stages as $reviewStage) {
            $this->assertReviewStage($reviewStage);
            $expectedStatus = $this->expectedStatusForStage($reviewStage);
            $datasets = $this->db->table('datasets')
                ->select('id')
                ->where('status', $expectedStatus)
                ->orderBy('updated_at', 'ASC')
                ->orderBy('id', 'ASC')
                ->get()
                ->getResultArray();

            foreach ($datasets as $dataset) {
                if ($this->autoAssign((int) $dataset['id'], $reviewStage, $actorId, $ip) !== null) {
                    $assigned++;
                }
            }
        }

        return $assigned;
    }

    public function assign(int $datasetId, string $stage, int $reviewerId, int $adminId, string $ip): int
    {
        if (! in_array($stage, [ReviewModel::STAGE_ETHICS, ReviewModel::STAGE_TECHNICAL], true)) {
            throw new RuntimeException('Invalid review stage.');
        }
        $expected = $stage === ReviewModel::STAGE_ETHICS ? DatasetModel::STATUS_PENDING_ETHICS : DatasetModel::STATUS_PENDING_TECHNICAL;
        $role = $stage === ReviewModel::STAGE_ETHICS ? self::ROLE_ETHICS : self::ROLE_TECHNICAL;

        return $this->transaction(function () use ($datasetId, $stage, $reviewerId, $adminId, $ip, $expected, $role): int {
            $dataset = $this->dataset($datasetId);
            if ($dataset['status'] !== $expected) {
                throw new RuntimeException('This dataset is not ready for the selected review stage.');
            }
            $this->requireRole($adminId, self::ROLE_ADMIN);
            $this->requireRole($reviewerId, $role);
            $active = $this->db->table('reviews')->where(['dataset_id' => $datasetId, 'status' => ReviewModel::STATUS_ASSIGNED])->orderBy('id', 'DESC')->get()->getRowArray();
            if (is_array($active)) {
                throw new RuntimeException('This dataset already has an active review. Resolve or reassign it before creating another assignment.');
            } else {
                $last = $this->db->table('reviews')->selectMax('review_round')->where(['dataset_id' => $datasetId, 'stage' => $stage])->get()->getRowArray();
                $round = max(1, ((int) ($last['review_round'] ?? 0)) + 1);
            }
            $version = $this->db->table('dataset_versions')->where('dataset_id', $datasetId)->orderBy('id', 'DESC')->get()->getRowArray();
            $now = date('Y-m-d H:i:s');
            $this->db->table('reviews')->insert(['dataset_id' => $datasetId, 'dataset_version_id' => $version['id'] ?? null, 'stage' => $stage, 'review_round' => $round, 'reviewer_id' => $reviewerId, 'assigned_by' => $adminId, 'assignment_method' => ReviewModel::ASSIGNMENT_MANUAL, 'status' => ReviewModel::STATUS_ASSIGNED, 'assigned_at' => $now, 'created_at' => $now, 'updated_at' => $now]);
            $id = (int) $this->db->insertID();
            $this->notify($reviewerId, 'review_assigned', 'Review assigned', 'A dataset has been assigned to your ' . $stage . ' review queue.', '/review/' . $stage . '/' . $id);
            $this->audit($adminId, 'review_assigned', 'review', $id, 'Assigned ' . $stage . ' review for dataset #' . $datasetId . '.', $ip);

            return $id;
        });
    }

    public function reassign(int $datasetId, string $stage, int $reviewerId, int $adminId, string $reason, string $ip): int
    {
        if (trim($reason) === '') {
            throw new RuntimeException('A reassignment reason is required.');
        }
        if (! in_array($stage, [ReviewModel::STAGE_ETHICS, ReviewModel::STAGE_TECHNICAL], true)) {
            throw new RuntimeException('Invalid review stage.');
        }

        $role = $stage === ReviewModel::STAGE_ETHICS ? self::ROLE_ETHICS : self::ROLE_TECHNICAL;

        return $this->transaction(function () use ($datasetId, $stage, $reviewerId, $adminId, $reason, $ip, $role): int {
            $this->requireRole($adminId, self::ROLE_ADMIN);
            $this->requireRole($reviewerId, $role);
            $active = $this->db->table('reviews')
                ->where(['dataset_id' => $datasetId, 'stage' => $stage, 'status' => ReviewModel::STATUS_ASSIGNED])
                ->orderBy('id', 'DESC')
                ->get()
                ->getRowArray();
            if (! is_array($active)) {
                throw new RuntimeException('There is no active review to reassign.');
            }
            if ((int) $active['reviewer_id'] === $reviewerId) {
                throw new RuntimeException('Select a different reviewer for reassignment.');
            }

            $dataset = $this->dataset($datasetId);
            $expected = $stage === ReviewModel::STAGE_ETHICS ? DatasetModel::STATUS_PENDING_ETHICS : DatasetModel::STATUS_PENDING_TECHNICAL;
            if ($dataset['status'] !== $expected) {
                throw new RuntimeException('This dataset is no longer in the selected review stage.');
            }

            $now = date('Y-m-d H:i:s');
            $this->db->table('reviews')->where('id', $active['id'])->update([
                'status' => ReviewModel::STATUS_REASSIGNED,
                'reassignment_reason' => trim($reason),
                'decided_at' => $now,
                'updated_at' => $now,
            ]);
            $this->db->table('reviews')->insert([
                'dataset_id' => $datasetId,
                'dataset_version_id' => $active['dataset_version_id'] ?? null,
                'stage' => $stage,
                'review_round' => (int) $active['review_round'],
                'reviewer_id' => $reviewerId,
                'assigned_by' => $adminId,
                'assignment_method' => ReviewModel::ASSIGNMENT_MANUAL,
                'status' => ReviewModel::STATUS_ASSIGNED,
                'assigned_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $newReviewId = (int) $this->db->insertID();

            $this->notify((int) $active['reviewer_id'], 'review_reassigned', 'Review reassigned', 'The ' . $stage . ' review for "' . $dataset['title'] . '" was reassigned. Reason: ' . trim($reason), '/review/' . $stage);
            $this->notify($reviewerId, 'review_assigned', 'Review assigned', 'The dataset "' . $dataset['title'] . '" has been assigned to your ' . $stage . ' review queue.', '/review/' . $stage . '/' . $newReviewId);
            $this->audit($adminId, 'review_reassigned', 'review', $newReviewId, 'Reassigned ' . $stage . ' review for dataset #' . $datasetId . '. Reason: ' . trim($reason), $ip);

            return $newReviewId;
        });
    }

    public function saveDraft(int $reviewId, int $reviewerId, array $checklist, string $comments, string $ip): void
    {
        $this->transaction(function () use ($reviewId, $reviewerId, $checklist, $comments, $ip): void {
            $review = $this->activeAssignedReview($reviewId, $reviewerId);
            $stage = (string) $review['stage'];
            $dataset = $this->dataset((int) $review['dataset_id']);
            $expected = $stage === ReviewModel::STAGE_ETHICS ? DatasetModel::STATUS_PENDING_ETHICS : DatasetModel::STATUS_PENDING_TECHNICAL;
            if ($dataset['status'] !== $expected) {
                throw new RuntimeException('The dataset workflow changed before this draft was saved.');
            }
            $answers = self::normalizeChecklist($stage, $checklist);
            $now = date('Y-m-d H:i:s');
            $this->db->table('reviews')->where('id', $reviewId)->update([
                'checklist' => json_encode($answers),
                'comments' => trim($comments),
                'draft_saved_at' => $now,
                'updated_at' => $now,
            ]);
            $this->audit($reviewerId, 'review_draft_saved', 'review', $reviewId, ucfirst($stage) . ' review draft saved.', $ip);
        });
    }

    public function decide(int $reviewId, int $reviewerId, string $decision, array $checklist, string $comments, string $ip): void
    {
        if (! in_array($decision, [ReviewModel::STATUS_APPROVED, ReviewModel::STATUS_REJECTED, ReviewModel::STATUS_REVISION], true)) {
            throw new RuntimeException('Invalid review decision.');
        }
        if ($decision !== ReviewModel::STATUS_APPROVED && trim($comments) === '') {
            throw new RuntimeException('Comments are required for rejection or revision requests.');
        }
        $this->transaction(function () use ($reviewId, $reviewerId, $decision, $checklist, $comments, $ip): void {
            $review = $this->activeAssignedReview($reviewId, $reviewerId);
            $stage = (string) $review['stage'];
            $this->requireRole($reviewerId, $stage === ReviewModel::STAGE_ETHICS ? self::ROLE_ETHICS : self::ROLE_TECHNICAL);
            $dataset = $this->dataset((int) $review['dataset_id']);
            $expected = $stage === ReviewModel::STAGE_ETHICS ? DatasetModel::STATUS_PENDING_ETHICS : DatasetModel::STATUS_PENDING_TECHNICAL;
            if ($dataset['status'] !== $expected) {
                throw new RuntimeException('The dataset workflow changed before this decision was submitted.');
            }
            $answers = self::normalizeChecklist($stage, $checklist);
            $progress = self::checklistProgress($answers);
            if ($decision === ReviewModel::STATUS_APPROVED && $progress['confirmed'] !== $progress['total']) {
                throw new RuntimeException('Every checklist item must be confirmed before approval.');
            }
            foreach ($answers as $answer) {
                if ($answer['result'] === 'issue' && $answer['note'] === '') {
                    throw new RuntimeException('Every checklist issue must include a finding note.');
                }
            }
            $next = $decision === ReviewModel::STATUS_REJECTED
                ? DatasetModel::STATUS_REJECTED
                : ($decision === ReviewModel::STATUS_REVISION
                    ? ($stage === ReviewModel::STAGE_ETHICS ? DatasetModel::STATUS_ETHICS_REVISION : DatasetModel::STATUS_TECHNICAL_REVISION)
                    : ($stage === ReviewModel::STAGE_TECHNICAL ? DatasetModel::STATUS_PENDING_ETHICS : DatasetModel::STATUS_AWAITING_PUBLICATION));
            $now = date('Y-m-d H:i:s');
            $this->db->table('reviews')->where('id', $reviewId)->update(['status' => $decision, 'checklist' => json_encode($answers), 'comments' => trim($comments), 'draft_saved_at' => null, 'decided_at' => $now, 'updated_at' => $now]);
            $this->db->table('datasets')->where('id', $dataset['id'])->update(['status' => $next, 'approved_by' => null, 'approved_at' => null, 'updated_at' => $now]);
            $automaticAssignment = $stage === ReviewModel::STAGE_TECHNICAL && $decision === ReviewModel::STATUS_APPROVED
                ? $this->autoAssignWithinTransaction((int) $dataset['id'], ReviewModel::STAGE_ETHICS, $reviewerId, $ip)
                : null;
            $message = 'Your dataset "' . $dataset['title'] . '" is now ' . DatasetModel::statusLabel($next) . '.' . (trim($comments) ? ' Reviewer comments: ' . trim($comments) : '');
            $contributorLink = in_array($next, [DatasetModel::STATUS_TECHNICAL_REVISION, DatasetModel::STATUS_ETHICS_REVISION], true)
                ? '/datasets/' . $dataset['id'] . '/edit'
                : '/datasets/' . $dataset['id'];
            $this->notify((int) $dataset['contributor_id'], 'review_result', ucfirst($stage) . ' review completed', $message, $contributorLink);
            $administratorMessage = $decision === ReviewModel::STATUS_APPROVED
                ? ($stage === ReviewModel::STAGE_TECHNICAL
                    ? ($automaticAssignment !== null
                        ? 'The dataset "' . $dataset['title'] . '" was automatically assigned to ' . $automaticAssignment['reviewer_name'] . ' for ethics review.'
                        : 'The dataset "' . $dataset['title'] . '" passed technical review, but no active ethics reviewer is available.')
                    : 'The dataset "' . $dataset['title'] . '" is ready for final publication.')
                : ucfirst($stage) . ' review for "' . $dataset['title'] . '" ended with ' . str_replace('_', ' ', $decision) . '.';
            $assignmentFailureAlreadyNotified = $stage === ReviewModel::STAGE_TECHNICAL
                && $decision === ReviewModel::STATUS_APPROVED
                && $automaticAssignment === null;
            if (! $assignmentFailureAlreadyNotified) {
                $this->notifyAdministrators(
                    $stage === ReviewModel::STAGE_TECHNICAL && $decision === ReviewModel::STATUS_APPROVED
                        ? 'Ethics review assigned'
                        : ($stage === ReviewModel::STAGE_ETHICS && $decision === ReviewModel::STATUS_APPROVED ? 'Dataset ready to publish' : ucfirst($stage) . ' review completed'),
                    $administratorMessage,
                    '/admin/datasets/' . $dataset['id']
                );
            }
            $this->audit($reviewerId, 'review_' . $decision, 'review', $reviewId, ucfirst($stage) . ' decision for dataset #' . $dataset['id'] . '.', $ip);
        });
    }

    public function publish(int $datasetId, int $adminId, string $accessType, string $ip): void
    {
        if (! array_key_exists($accessType, DatasetModel::accessOptions())) {
            throw new RuntimeException('Invalid dataset access type.');
        }
        $this->transaction(function () use ($datasetId, $adminId, $accessType, $ip): void {
            $this->requireRole($adminId, self::ROLE_ADMIN);
            $dataset = $this->dataset($datasetId);
            if ($dataset['status'] !== DatasetModel::STATUS_AWAITING_PUBLICATION) {
                throw new RuntimeException('Only datasets approved by both review stages can be published.');
            }
            $now = date('Y-m-d H:i:s');
            $this->db->table('datasets')->where('id', $datasetId)->update(['status' => DatasetModel::STATUS_PUBLISHED, 'access_type' => $accessType, 'approved_by' => $adminId, 'approved_at' => $now, 'archived_at' => null, 'archived_from_status' => null, 'updated_at' => $now]);
            $this->notify((int) $dataset['contributor_id'], 'dataset_published', 'Dataset published', 'Your dataset "' . $dataset['title'] . '" is now published.', '/datasets/' . $datasetId);
            $this->audit($adminId, 'dataset_published', 'dataset', $datasetId, 'Published with access type ' . $accessType . '.', $ip);
        });
    }

    public function setArchived(int $datasetId, int $adminId, bool $restore, string $reason, string $ip): void
    {
        if (! $restore && trim($reason) === '') {
            throw new RuntimeException('An archive reason is required.');
        }

        $this->transaction(function () use ($datasetId, $adminId, $restore, $reason, $ip): void {
            $this->requireRole($adminId, self::ROLE_ADMIN);
            $dataset = $this->dataset($datasetId);
            if ($restore && $dataset['status'] !== DatasetModel::STATUS_ARCHIVED) {
                throw new RuntimeException('Only archived datasets can be restored.');
            }
            if (! $restore && $dataset['status'] === DatasetModel::STATUS_ARCHIVED) {
                throw new RuntimeException('This dataset is already archived.');
            }
            if (! $restore) {
                $activeReview = $this->db->table('reviews')->where(['dataset_id' => $datasetId, 'status' => ReviewModel::STATUS_ASSIGNED])->get()->getRowArray();
                if (is_array($activeReview)) {
                    throw new RuntimeException('Reassign or complete the active review before archiving this dataset.');
                }
            }
            $data = $restore
                ? ['status' => (string) ($dataset['archived_from_status'] ?: DatasetModel::STATUS_PENDING_ETHICS), 'archived_at' => null, 'archived_from_status' => null]
                : ['status' => DatasetModel::STATUS_ARCHIVED, 'archived_at' => date('Y-m-d H:i:s'), 'archived_from_status' => $dataset['status']];
            $data['updated_at'] = date('Y-m-d H:i:s');
            $this->db->table('datasets')->where('id', $datasetId)->update($data);
            if ($restore && in_array($data['status'], [DatasetModel::STATUS_PENDING_TECHNICAL, DatasetModel::STATUS_PENDING_ETHICS], true)) {
                $stage = $data['status'] === DatasetModel::STATUS_PENDING_ETHICS
                    ? ReviewModel::STAGE_ETHICS
                    : ReviewModel::STAGE_TECHNICAL;
                $this->autoAssignWithinTransaction($datasetId, $stage, $adminId, $ip);
            }
            $details = $restore
                ? 'Dataset restored to its previous lifecycle state.'
                : 'Dataset archived. Reason: ' . trim($reason);
            $this->audit($adminId, $restore ? 'dataset_restore' : 'dataset_archive', 'dataset', $datasetId, $details, $ip);
        });
    }

    /**
     * @return array{review_id: int, reviewer_id: int, reviewer_name: string}|null
     */
    private function autoAssignWithinTransaction(int $datasetId, string $stage, int $actorId, string $ip): ?array
    {
        $dataset = $this->dataset($datasetId);
        if ($dataset['status'] !== $this->expectedStatusForStage($stage)) {
            return null;
        }

        $active = $this->db->table('reviews')
            ->where([
                'dataset_id' => $datasetId,
                'status' => ReviewModel::STATUS_ASSIGNED,
            ])
            ->get()
            ->getRowArray();
        if (is_array($active)) {
            return null;
        }

        $reviewers = $this->eligibleReviewerWorkloads($this->requiredRoleForStage($stage));
        if ($reviewers === []) {
            $stageLabel = $stage === ReviewModel::STAGE_ETHICS ? 'ethics' : 'technical';
            $this->notifyAdministrators(
                ucfirst($stageLabel) . ' reviewer unavailable',
                'The dataset "' . $dataset['title'] . '" is waiting for ' . $stageLabel . ' review because no eligible active reviewer is available.',
                '/admin/datasets/' . $datasetId
            );
            $this->audit(
                $actorId,
                'review_auto_assignment_deferred',
                'dataset',
                $datasetId,
                'Automatic ' . $stageLabel . ' assignment was deferred because no eligible active reviewer was available.',
                $ip
            );

            return null;
        }

        $reviewer = $reviewers[0];
        $last = $this->db->table('reviews')
            ->selectMax('review_round')
            ->where(['dataset_id' => $datasetId, 'stage' => $stage])
            ->get()
            ->getRowArray();
        $version = $this->db->table('dataset_versions')
            ->where('dataset_id', $datasetId)
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();
        $now = date('Y-m-d H:i:s');
        $this->db->table('reviews')->insert([
            'dataset_id' => $datasetId,
            'dataset_version_id' => $version['id'] ?? null,
            'stage' => $stage,
            'review_round' => max(1, ((int) ($last['review_round'] ?? 0)) + 1),
            'reviewer_id' => (int) $reviewer['id'],
            'assigned_by' => $actorId,
            'assignment_method' => ReviewModel::ASSIGNMENT_AUTOMATIC,
            'status' => ReviewModel::STATUS_ASSIGNED,
            'assigned_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $reviewId = (int) $this->db->insertID();
        $this->notify(
            (int) $reviewer['id'],
            'review_assigned',
            'Review automatically assigned',
            'The dataset "' . $dataset['title'] . '" was added to your ' . $stage . ' review queue through balanced distribution.',
            '/review/' . $stage . '/' . $reviewId
        );
        $this->audit(
            $actorId,
            'review_auto_assigned',
            'review',
            $reviewId,
            'Automatically assigned ' . $stage . ' review for dataset #' . $datasetId . ' to user #' . $reviewer['id'] . ' using least-active-load distribution.',
            $ip
        );

        return [
            'review_id' => $reviewId,
            'reviewer_id' => (int) $reviewer['id'],
            'reviewer_name' => (string) $reviewer['name'],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function eligibleReviewerWorkloads(string $role): array
    {
        $reviewers = $this->db->table('users')
            ->select('users.id, users.name, users.email')
            ->join('user_roles', 'user_roles.user_id = users.id')
            ->join('roles', 'roles.id = user_roles.role_id')
            ->where(['roles.name' => $role, 'users.status' => 'active'])
            ->orderBy('users.id', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($reviewers as &$reviewer) {
            $reviewer['active_count'] = $this->db->table('reviews')
                ->where([
                    'reviewer_id' => (int) $reviewer['id'],
                    'status' => ReviewModel::STATUS_ASSIGNED,
                ])
                ->countAllResults();
            $lastAssignment = $this->db->table('reviews')
                ->selectMax('assigned_at')
                ->where('reviewer_id', (int) $reviewer['id'])
                ->get()
                ->getRowArray();
            $reviewer['last_assigned_at'] = $lastAssignment['assigned_at'] ?? null;
        }
        unset($reviewer);

        return self::rankReviewerWorkloads($reviewers);
    }

    private function assertReviewStage(string $stage): void
    {
        if (! in_array($stage, [ReviewModel::STAGE_ETHICS, ReviewModel::STAGE_TECHNICAL], true)) {
            throw new RuntimeException('Invalid review stage.');
        }
    }

    private function expectedStatusForStage(string $stage): string
    {
        return $stage === ReviewModel::STAGE_ETHICS
            ? DatasetModel::STATUS_PENDING_ETHICS
            : DatasetModel::STATUS_PENDING_TECHNICAL;
    }

    private function requiredRoleForStage(string $stage): string
    {
        return $stage === ReviewModel::STAGE_ETHICS
            ? self::ROLE_ETHICS
            : self::ROLE_TECHNICAL;
    }

    private function transaction(callable $operation): mixed
    {
        $this->db->transBegin();
        try {
            $result = $operation();
            if (! $this->db->transStatus()) {
                throw new RuntimeException('The moderation workflow could not be saved.');
            }
            $this->db->transCommit();

            return $result;
        } catch (\Throwable $exception) {
            $this->db->transRollback();
            throw $exception;
        }
    }

    private function dataset(int $id): array
    {
        $row = $this->db->table('datasets')->where('id', $id)->get()->getRowArray();
        if (! is_array($row)) {
            throw new RuntimeException('Dataset not found.');
        }

        return $row;
    }

    private function activeAssignedReview(int $reviewId, int $reviewerId): array
    {
        $review = $this->db->table('reviews')->where('id', $reviewId)->get()->getRowArray();
        if (! is_array($review) || $review['status'] !== ReviewModel::STATUS_ASSIGNED || (int) $review['reviewer_id'] !== $reviewerId) {
            throw new RuntimeException('This review is not assigned to the current reviewer.');
        }

        $this->requireRole($reviewerId, $review['stage'] === ReviewModel::STAGE_ETHICS ? self::ROLE_ETHICS : self::ROLE_TECHNICAL);

        return $review;
    }

    private function requireRole(int $userId, string $role): void
    {
        $row = $this->db->table('user_roles')
            ->join('roles', 'roles.id = user_roles.role_id')
            ->join('users', 'users.id = user_roles.user_id')
            ->where(['user_roles.user_id' => $userId, 'roles.name' => $role, 'users.status' => 'active'])
            ->get()
            ->getRowArray();
        if (! is_array($row)) {
            throw new RuntimeException('The selected account does not have the required role.');
        }
    }

    private function notify(int $userId, string $type, string $title, string $message, ?string $link): void
    {
        $now = date('Y-m-d H:i:s');
        $this->db->table('notifications')->insert(['user_id' => $userId, 'type' => $type, 'title' => $title, 'message' => $message, 'link' => $link, 'created_at' => $now, 'updated_at' => $now]);
    }

    private function notifyAdministrators(string $title, string $message, string $link): void
    {
        $administrators = $this->db->table('users')
            ->select('users.id')
            ->join('user_roles', 'user_roles.user_id = users.id')
            ->join('roles', 'roles.id = user_roles.role_id')
            ->where(['roles.name' => self::ROLE_ADMIN, 'users.status' => 'active'])
            ->get()
            ->getResultArray();
        foreach ($administrators as $administrator) {
            $this->notify((int) $administrator['id'], 'workflow_attention', $title, $message, $link);
        }
    }

    private function audit(int $userId, string $action, string $entityType, int $entityId, string $details, string $ip): void
    {
        $this->db->table('audit_logs')->insert(['user_id' => $userId, 'action' => $action, 'entity_type' => $entityType, 'entity_id' => $entityId, 'details' => $details, 'ip_address' => $ip, 'created_at' => date('Y-m-d H:i:s')]);
    }
}
