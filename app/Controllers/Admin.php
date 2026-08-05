<?php

namespace App\Controllers;

use App\Models\DatasetModel;
use App\Models\DatasetFileModel;
use App\Models\DatasetVersionModel;
use App\Models\ReviewModel;
use App\Models\UserModel;
use App\Services\ModerationWorkflow;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;

class Admin extends BaseController
{
    public function index(): string
    {
        $db = db_connect();
        $activeReviews = $db->table('reviews')->where('status', ReviewModel::STATUS_ASSIGNED);
        $metrics = [
            'technical_unassigned' => $this->unassignedCount(DatasetModel::STATUS_PENDING_TECHNICAL),
            'technical_active' => (clone $activeReviews)->where('stage', ReviewModel::STAGE_TECHNICAL)->countAllResults(),
            'ethics_unassigned' => $this->unassignedCount(DatasetModel::STATUS_PENDING_ETHICS),
            'ethics_active' => (clone $activeReviews)->where('stage', ReviewModel::STAGE_ETHICS)->countAllResults(),
            'awaiting_publication' => model(DatasetModel::class)->where('status', DatasetModel::STATUS_AWAITING_PUBLICATION)->countAllResults(),
            'aging' => model(ReviewModel::class)->where('status', ReviewModel::STATUS_ASSIGNED)->where('assigned_at <=', date('Y-m-d H:i:s', strtotime('-3 days')))->countAllResults(),
            'revision' => model(DatasetModel::class)->whereIn('status', [DatasetModel::STATUS_TECHNICAL_REVISION, DatasetModel::STATUS_ETHICS_REVISION])->countAllResults(),
        ];

        return view('admin/index', [
            'title' => 'Repository Administration',
            'metrics' => $metrics,
            'attention' => $this->attentionQueue(),
        ]);
    }

    public function datasets(): string
    {
        $stage = trim((string) ($this->request->getGet('stage') ?? 'technical_review'));
        $validStages = ['technical_assignment', 'technical_review', 'ethics_assignment', 'ethics_review', 'publication', 'revision', 'published'];
        if (! in_array($stage, $validStages, true)) {
            $stage = 'technical_review';
        }
        $search = trim((string) ($this->request->getGet('q') ?? ''));
        $reviewerId = (int) ($this->request->getGet('reviewer_id') ?? 0);
        $age = trim((string) ($this->request->getGet('age') ?? ''));
        $access = trim((string) ($this->request->getGet('access') ?? ''));
        $dataType = trim((string) ($this->request->getGet('data_type') ?? ''));

        $datasetModel = model(DatasetModel::class);
        $query = $datasetModel
            ->select('datasets.*, contributors.name AS contributor_name, active_reviews.id AS active_review_id, active_reviews.stage AS active_review_stage, active_reviews.reviewer_id AS active_reviewer_id, active_reviews.assigned_at AS active_assigned_at, active_reviewers.name AS active_reviewer_name')
            ->join('users contributors', 'contributors.id = datasets.contributor_id', 'left')
            ->join('reviews active_reviews', "active_reviews.dataset_id = datasets.id AND active_reviews.status = '" . ReviewModel::STATUS_ASSIGNED . "'", 'left')
            ->join('users active_reviewers', 'active_reviewers.id = active_reviews.reviewer_id', 'left');

        $this->applyModerationStage($query, $stage);
        if ($search !== '') {
            $query->groupStart()
                ->like('datasets.title', $search)
                ->orLike('contributors.name', $search)
                ->orLike('datasets.category', $search)
                ->orLike('datasets.content_formats', $search)
                ->orLike('datasets.tags', $search)
                ->groupEnd();
        }
        if ($reviewerId > 0) {
            $query->where('active_reviews.reviewer_id', $reviewerId);
        }
        if ($access !== '' && array_key_exists($access, DatasetModel::accessOptions())) {
            $query->where('datasets.access_type', $access);
        }
        if ($dataType !== '') {
            $query->where('datasets.data_type', $dataType);
        }
        $cutoff = $this->ageCutoff($age);
        if ($cutoff !== null) {
            $query->where('COALESCE(active_reviews.assigned_at, datasets.updated_at) <=', $cutoff);
        }

        $datasets = $query->orderBy('COALESCE(active_reviews.assigned_at, datasets.updated_at)', 'ASC', false)->paginate(12);
        $datasetIds = array_values(array_map(static fn (array $dataset): int => (int) $dataset['id'], $datasets));
        $reviews = $datasetIds === [] ? [] : model(ReviewModel::class)
            ->select('reviews.*, users.name AS reviewer_name')
            ->join('users', 'users.id = reviews.reviewer_id', 'left')
            ->whereIn('reviews.dataset_id', $datasetIds)
            ->orderBy('reviews.id', 'DESC')
            ->findAll();
        $byDataset = [];
        foreach ($reviews as $review) {
            $byDataset[(int) $review['dataset_id']][] = $review;
        }
        foreach ($datasets as &$dataset) {
            $dataset['stage_age'] = $this->ageLabel((string) ($dataset['active_assigned_at'] ?: $dataset['updated_at']));
            $datasetReviews = $byDataset[(int) $dataset['id']] ?? [];
            $dataset['latest_review'] = $datasetReviews[0] ?? null;
        }
        unset($dataset);

        $technicalReviewers = $this->usersWithRole(ModerationWorkflow::ROLE_TECHNICAL);
        $ethicsReviewers = $this->usersWithRole(ModerationWorkflow::ROLE_ETHICS);
        $allReviewers = [];
        foreach (array_merge($technicalReviewers, $ethicsReviewers) as $reviewer) {
            $allReviewers[(int) $reviewer['id']] = $reviewer;
        }

        return view('admin/datasets', [
            'title' => 'Dataset Moderation',
            'datasets' => $datasets,
            'reviewsByDataset' => $byDataset,
            'statusLabels' => DatasetModel::statusLabels(),
            'accessOptions' => DatasetModel::accessOptions(),
            'ethicsReviewers' => $ethicsReviewers,
            'technicalReviewers' => $technicalReviewers,
            'allReviewers' => array_values($allReviewers),
            'pager' => $datasetModel->pager,
            'selectedStage' => $stage,
            'search' => $search,
            'selectedReviewerId' => $reviewerId,
            'selectedAge' => $age,
            'selectedAccess' => $access,
            'selectedDataType' => $dataType,
            'stageCounts' => $this->moderationStageCounts(),
        ]);
    }

    public function dataset(int $datasetId): string
    {
        $dataset = model(DatasetModel::class)
            ->select('datasets.*, users.name AS contributor_name, users.email AS contributor_email')
            ->join('users', 'users.id = datasets.contributor_id', 'left')
            ->where('datasets.id', $datasetId)
            ->first();

        if (! is_array($dataset)) {
            throw PageNotFoundException::forPageNotFound();
        }

        $reviews = model(ReviewModel::class)
            ->select('reviews.*, users.name AS reviewer_name, assigners.name AS assigner_name')
            ->join('users', 'users.id = reviews.reviewer_id', 'left')
            ->join('users assigners', 'assigners.id = reviews.assigned_by', 'left')
            ->where('reviews.dataset_id', $datasetId)
            ->orderBy('reviews.id', 'DESC')
            ->findAll();

        return view('admin/dataset', [
            'title' => $dataset['title'],
            'dataset' => $dataset,
            'statusLabel' => DatasetModel::statusLabel((string) ($dataset['status'] ?? '')),
            'accessLabel' => DatasetModel::accessLabel((string) ($dataset['access_type'] ?? '')),
            'latestFile' => model(DatasetFileModel::class)->where('dataset_id', $datasetId)->orderBy('created_at', 'DESC')->first(),
            'versions' => model(DatasetVersionModel::class)->where('dataset_id', $datasetId)->orderBy('id', 'DESC')->findAll(),
            'reviews' => $reviews,
            'technicalApproval' => $this->latestDecision($reviews, ReviewModel::STAGE_TECHNICAL, ReviewModel::STATUS_APPROVED),
            'ethicsApproval' => $this->latestDecision($reviews, ReviewModel::STAGE_ETHICS, ReviewModel::STATUS_APPROVED),
            'activeReview' => $this->activeReview($reviews),
            'accessOptions' => DatasetModel::accessOptions(),
            'technicalReviewers' => $this->usersWithRole(ModerationWorkflow::ROLE_TECHNICAL),
            'ethicsReviewers' => $this->usersWithRole(ModerationWorkflow::ROLE_ETHICS),
            'auditLogs' => db_connect()->table('audit_logs')->select('audit_logs.*, users.name AS user_name')->join('users', 'users.id = audit_logs.user_id', 'left')->where(['audit_logs.entity_type' => 'dataset', 'audit_logs.entity_id' => $datasetId])->orderBy('audit_logs.id', 'DESC')->limit(20)->get()->getResultArray(),
        ]);
    }

    public function assign(int $datasetId)
    {
        try {
            (new ModerationWorkflow())->assign($datasetId, trim((string) $this->request->getPost('stage')), (int) $this->request->getPost('reviewer_id'), (int) $this->currentUserId(), $this->request->getIPAddress());
        } catch (RuntimeException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->back()->with('info', 'Reviewer assignment saved.');
    }

    public function reassign(int $datasetId)
    {
        try {
            (new ModerationWorkflow())->reassign(
                $datasetId,
                trim((string) $this->request->getPost('stage')),
                (int) $this->request->getPost('reviewer_id'),
                (int) $this->currentUserId(),
                trim((string) $this->request->getPost('reason')),
                $this->request->getIPAddress()
            );
        } catch (RuntimeException $exception) {
            return redirect()->back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->back()->with('info', 'Review reassigned. The previous reviewer no longer has decision access.');
    }

    public function publish(int $datasetId)
    {
        if ($this->request->getPost('publication_confirmed') !== '1') {
            return redirect()->back()->withInput()->with('error', 'Confirm that both review approvals and the final access classification were checked.');
        }
        try {
            (new ModerationWorkflow())->publish($datasetId, (int) $this->currentUserId(), trim((string) $this->request->getPost('access_type')), $this->request->getIPAddress());
        } catch (RuntimeException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->back()->with('info', 'Dataset published successfully.');
    }

    public function archive(int $datasetId)
    {
        return $this->setArchived($datasetId, false);
    }

    public function restore(int $datasetId)
    {
        return $this->setArchived($datasetId, true);
    }

    public function users(): string
    {
        $assignments = db_connect()->table('user_roles')->select('user_roles.user_id, roles.name')->join('roles', 'roles.id = user_roles.role_id')->get()->getResultArray();
        $userRoles = [];
        foreach ($assignments as $assignment) {
            $userRoles[(int) $assignment['user_id']][] = $assignment['name'];
        }

        return view('admin/users', [
            'title' => 'Users and Roles', 'users' => model(UserModel::class)->orderBy('name', 'ASC')->findAll(),
            'userRoles' => $userRoles, 'roles' => db_connect()->table('roles')->orderBy('name')->get()->getResultArray(),
            'currentUserId' => (int) $this->currentUserId(),
        ]);
    }

    public function updateUser(int $userId)
    {
        $user = model(UserModel::class)->find($userId);
        if (! is_array($user)) {
            throw PageNotFoundException::forPageNotFound();
        }
        $status = $this->request->getPost('status') === 'active' ? 'active' : 'inactive';
        $allowed = [ModerationWorkflow::ROLE_USER, ModerationWorkflow::ROLE_ETHICS, ModerationWorkflow::ROLE_TECHNICAL, ModerationWorkflow::ROLE_ADMIN];
        $roles = array_values(array_unique(array_intersect((array) $this->request->getPost('roles'), $allowed)));
        if ($roles === []) {
            return redirect()->back()->with('error', 'Every account must retain at least one role.');
        }
        $removesAdmin = $status !== 'active' || ! in_array(ModerationWorkflow::ROLE_ADMIN, $roles, true);
        if ($userId === (int) $this->currentUserId() && $removesAdmin) {
            return redirect()->back()->with('error', 'You cannot deactivate yourself or remove your own administrator role.');
        }
        if ($removesAdmin && $this->isLastActiveAdministrator($userId)) {
            return redirect()->back()->with('error', 'The final active administrator cannot be removed.');
        }
        $activeAssignments = model(ReviewModel::class)
            ->where(['reviewer_id' => $userId, 'status' => ReviewModel::STATUS_ASSIGNED])
            ->findAll();
        foreach ($activeAssignments as $assignment) {
            $requiredRole = $assignment['stage'] === ReviewModel::STAGE_ETHICS
                ? ModerationWorkflow::ROLE_ETHICS
                : ModerationWorkflow::ROLE_TECHNICAL;
            if ($status !== 'active' || ! in_array($requiredRole, $roles, true)) {
                return redirect()->back()->with('error', 'Reassign this reviewer\'s active work before deactivating the account or removing its reviewer role.');
            }
        }

        $db = db_connect();
        $db->transStart();
        $db->table('users')->where('id', $userId)->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
        $db->table('user_roles')->where('user_id', $userId)->delete();
        foreach ($db->table('roles')->whereIn('name', $roles)->get()->getResultArray() as $role) {
            $db->table('user_roles')->insert(['user_id' => $userId, 'role_id' => (int) $role['id']]);
        }
        $db->transComplete();
        if (! $db->transStatus()) {
            return redirect()->back()->with('error', 'User access could not be updated.');
        }
        $this->recordAudit('user_roles_updated', 'user', $userId, 'Account status and roles updated by repository administrator.');
        $distributed = $status === 'active' ? $this->distributePendingReviews($roles) : 0;

        $message = 'User access updated.';
        if ($distributed > 0) {
            $message .= ' ' . $distributed . ' waiting review' . ($distributed === 1 ? ' was' : 's were') . ' automatically distributed.';
        }

        return redirect()->back()->with('info', $message);
    }

    public function createUser()
    {
        $allowed = [ModerationWorkflow::ROLE_USER, ModerationWorkflow::ROLE_ETHICS, ModerationWorkflow::ROLE_TECHNICAL, ModerationWorkflow::ROLE_ADMIN];
        $roles = array_values(array_unique(array_intersect((array) $this->request->getPost('roles'), $allowed)));

        if ($roles === []) {
            $roles = [ModerationWorkflow::ROLE_USER];
        }

        $rules = [
            'name' => 'required|min_length[2]|max_length[150]',
            'email' => 'required|valid_email|max_length[190]|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'status' => 'permit_empty|in_list[active,inactive]',
        ];

        if (! $this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('validation', $this->validator->getErrors())
                ->with('error', implode(' ', $this->validator->getErrors()));
        }

        $db = db_connect();
        $now = date('Y-m-d H:i:s');
        $status = $this->request->getPost('status') === 'inactive' ? 'inactive' : 'active';
        $email = strtolower(trim((string) $this->request->getPost('email')));

        $db->transStart();
        $db->table('users')->insert([
            'name' => trim((string) $this->request->getPost('name')),
            'email' => $email,
            'password_hash' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'auth_provider' => 'local',
            'status' => $status,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $userId = (int) $db->insertID();

        $roleRows = $db->table('roles')->whereIn('name', $roles)->get()->getResultArray();
        foreach ($roleRows as $role) {
            $db->table('user_roles')->insert(['user_id' => $userId, 'role_id' => (int) $role['id']]);
        }
        $db->transComplete();

        if (! $db->transStatus() || $userId <= 0) {
            return redirect()->back()->withInput()->with('error', 'Password account could not be created.');
        }

        $this->recordAudit('password_account_created', 'user', $userId, 'Repository administrator issued password login credentials.');
        $distributed = $status === 'active' ? $this->distributePendingReviews($roles) : 0;
        $message = 'Password account created. Share the issued email and temporary password securely.';
        if ($distributed > 0) {
            $message .= ' ' . $distributed . ' waiting review' . ($distributed === 1 ? ' was' : 's were') . ' automatically distributed.';
        }

        return redirect()
            ->to('/admin/users')
            ->with('info', $message);
    }

    public function auditLogs(): string
    {
        $search = trim((string) $this->request->getGet('q'));
        $query = db_connect()->table('audit_logs')->select('audit_logs.*, users.name AS user_name')->join('users', 'users.id = audit_logs.user_id', 'left');
        if ($search !== '') {
            $query->groupStart()->like('audit_logs.action', $search)->orLike('audit_logs.details', $search)->orLike('users.name', $search)->groupEnd();
        }

        return view('admin/audit_logs', ['title' => 'Audit Logs', 'logs' => $query->orderBy('audit_logs.created_at', 'DESC')->limit(250)->get()->getResultArray(), 'search' => $search]);
    }

    private function setArchived(int $datasetId, bool $restore)
    {
        try {
            (new ModerationWorkflow())->setArchived(
                $datasetId,
                (int) $this->currentUserId(),
                $restore,
                trim((string) $this->request->getPost('reason')),
                $this->request->getIPAddress()
            );
        } catch (RuntimeException $exception) {
            return redirect()->back()->with('error', $exception->getMessage());
        }

        return redirect()->back()->with('info', $restore ? 'Dataset restored.' : 'Dataset archived.');
    }

    private function usersWithRole(string $role): array
    {
        $users = db_connect()->table('users')->select('users.id, users.name, users.email')->join('user_roles', 'user_roles.user_id = users.id')->join('roles', 'roles.id = user_roles.role_id')->where(['roles.name' => $role, 'users.status' => 'active'])->orderBy('users.name')->get()->getResultArray();
        foreach ($users as &$user) {
            $active = model(ReviewModel::class)->where(['reviewer_id' => (int) $user['id'], 'status' => ReviewModel::STATUS_ASSIGNED]);
            $user['active_count'] = $active->countAllResults();
            $oldest = model(ReviewModel::class)->where(['reviewer_id' => (int) $user['id'], 'status' => ReviewModel::STATUS_ASSIGNED])->orderBy('assigned_at', 'ASC')->first();
            $user['oldest_assignment'] = is_array($oldest) ? $this->ageLabel((string) $oldest['assigned_at']) : 'No active assignments';
            $user['role'] = $role;
        }
        unset($user);

        return $users;
    }

    /**
     * @param array<int, string> $roles
     */
    private function distributePendingReviews(array $roles): int
    {
        $workflow = new ModerationWorkflow();
        $distributed = 0;
        $actorId = (int) $this->currentUserId();
        $ip = $this->request->getIPAddress();

        if (in_array(ModerationWorkflow::ROLE_TECHNICAL, $roles, true)) {
            $distributed += $workflow->autoAssignPending($actorId, $ip, ReviewModel::STAGE_TECHNICAL);
        }
        if (in_array(ModerationWorkflow::ROLE_ETHICS, $roles, true)) {
            $distributed += $workflow->autoAssignPending($actorId, $ip, ReviewModel::STAGE_ETHICS);
        }

        return $distributed;
    }

    private function isLastActiveAdministrator(int $userId): bool
    {
        $hasRole = db_connect()->table('user_roles')->join('roles', 'roles.id = user_roles.role_id')->where(['user_roles.user_id' => $userId, 'roles.name' => ModerationWorkflow::ROLE_ADMIN])->countAllResults() > 0;
        $count = db_connect()->table('users')->join('user_roles', 'user_roles.user_id = users.id')->join('roles', 'roles.id = user_roles.role_id')->where(['roles.name' => ModerationWorkflow::ROLE_ADMIN, 'users.status' => 'active'])->countAllResults();

        return $hasRole && $count <= 1;
    }

    private function unassignedCount(string $datasetStatus): int
    {
        return (int) db_connect()->table('datasets')
            ->where('datasets.status', $datasetStatus)
            ->where("NOT EXISTS (SELECT 1 FROM reviews WHERE reviews.dataset_id = datasets.id AND reviews.status = " . db_connect()->escape(ReviewModel::STATUS_ASSIGNED) . ")", null, false)
            ->countAllResults();
    }

    private function attentionQueue(): array
    {
        $datasets = model(DatasetModel::class)
            ->select('datasets.*, users.name AS contributor_name')
            ->join('users', 'users.id = datasets.contributor_id', 'left')
            ->whereIn('datasets.status', [DatasetModel::STATUS_PENDING_TECHNICAL, DatasetModel::STATUS_PENDING_ETHICS, DatasetModel::STATUS_AWAITING_PUBLICATION])
            ->orderBy('datasets.updated_at', 'ASC')
            ->findAll(8);
        foreach ($datasets as &$dataset) {
            $dataset['age_label'] = $this->ageLabel((string) $dataset['updated_at']);
        }
        unset($dataset);

        return $datasets;
    }

    private function applyModerationStage(object $query, string $stage): void
    {
        match ($stage) {
            'technical_assignment' => $query->where('datasets.status', DatasetModel::STATUS_PENDING_TECHNICAL)->where('active_reviews.id', null),
            'technical_review' => $query->where('datasets.status', DatasetModel::STATUS_PENDING_TECHNICAL)->where('active_reviews.stage', ReviewModel::STAGE_TECHNICAL),
            'ethics_assignment' => $query->where('datasets.status', DatasetModel::STATUS_PENDING_ETHICS)->where('active_reviews.id', null),
            'ethics_review' => $query->where('datasets.status', DatasetModel::STATUS_PENDING_ETHICS)->where('active_reviews.stage', ReviewModel::STAGE_ETHICS),
            'publication' => $query->where('datasets.status', DatasetModel::STATUS_AWAITING_PUBLICATION),
            'revision' => $query->whereIn('datasets.status', [DatasetModel::STATUS_TECHNICAL_REVISION, DatasetModel::STATUS_ETHICS_REVISION, DatasetModel::STATUS_REJECTED]),
            'published' => $query->whereIn('datasets.status', [DatasetModel::STATUS_PUBLISHED, DatasetModel::STATUS_ARCHIVED]),
            default => null,
        };
    }

    private function moderationStageCounts(): array
    {
        return [
            'technical_assignment' => $this->unassignedCount(DatasetModel::STATUS_PENDING_TECHNICAL),
            'technical_review' => model(ReviewModel::class)->where(['stage' => ReviewModel::STAGE_TECHNICAL, 'status' => ReviewModel::STATUS_ASSIGNED])->countAllResults(),
            'ethics_assignment' => $this->unassignedCount(DatasetModel::STATUS_PENDING_ETHICS),
            'ethics_review' => model(ReviewModel::class)->where(['stage' => ReviewModel::STAGE_ETHICS, 'status' => ReviewModel::STATUS_ASSIGNED])->countAllResults(),
            'publication' => model(DatasetModel::class)->where('status', DatasetModel::STATUS_AWAITING_PUBLICATION)->countAllResults(),
            'revision' => model(DatasetModel::class)->whereIn('status', [DatasetModel::STATUS_TECHNICAL_REVISION, DatasetModel::STATUS_ETHICS_REVISION, DatasetModel::STATUS_REJECTED])->countAllResults(),
            'published' => model(DatasetModel::class)->whereIn('status', [DatasetModel::STATUS_PUBLISHED, DatasetModel::STATUS_ARCHIVED])->countAllResults(),
        ];
    }

    private function ageCutoff(string $age): ?string
    {
        return in_array($age, ['3', '7', '14'], true) ? date('Y-m-d H:i:s', strtotime('-' . $age . ' days')) : null;
    }

    private function ageLabel(string $date): string
    {
        $timestamp = strtotime($date);
        $days = $timestamp ? max(0, (int) floor((time() - $timestamp) / 86400)) : 0;

        return $days < 1 ? 'Today' : $days . ' day' . ($days === 1 ? '' : 's');
    }

    private function latestDecision(array $reviews, string $stage, string $status): ?array
    {
        foreach ($reviews as $review) {
            if (($review['stage'] ?? '') === $stage && ($review['status'] ?? '') === $status) {
                return $review;
            }
        }

        return null;
    }

    private function activeReview(array $reviews): ?array
    {
        foreach ($reviews as $review) {
            if (($review['status'] ?? '') === ReviewModel::STATUS_ASSIGNED) {
                return $review;
            }
        }

        return null;
    }
}
