<?php

namespace App\Controllers;

use App\Models\DatasetFileModel;
use App\Models\DatasetModel;
use App\Models\DatasetVersionModel;
use App\Models\ReviewModel;
use App\Services\ModerationWorkflow;
use CodeIgniter\Exceptions\PageNotFoundException;
use RuntimeException;

class Reviews extends BaseController
{
    public function index(string $stage): string
    {
        $this->assertStage($stage);
        $tab = trim((string) ($this->request->getGet('tab') ?? 'action'));
        if (! in_array($tab, ['action', 'completed', 'all'], true)) {
            $tab = 'action';
        }
        $search = trim((string) ($this->request->getGet('q') ?? ''));
        $age = trim((string) ($this->request->getGet('age') ?? ''));
        $access = trim((string) ($this->request->getGet('access') ?? ''));
        $dataType = trim((string) ($this->request->getGet('data_type') ?? ''));
        $sort = trim((string) ($this->request->getGet('sort') ?? 'oldest'));

        $reviewModel = model(ReviewModel::class);
        $query = $reviewModel
            ->select('reviews.*, datasets.title, datasets.description, datasets.status AS dataset_status, datasets.version AS dataset_version, datasets.access_type, datasets.data_type, datasets.file_format, datasets.content_formats, datasets.category, datasets.cover_image, datasets.anonymized, datasets.source_type, users.name AS contributor_name')
            ->join('datasets', 'datasets.id = reviews.dataset_id')
            ->join('users', 'users.id = datasets.contributor_id', 'left')
            ->where(['reviews.stage' => $stage, 'reviews.reviewer_id' => (int) $this->currentUserId()])
            ->where('reviews.status !=', ReviewModel::STATUS_REASSIGNED);

        if ($tab === 'action') {
            $query->where('reviews.status', ReviewModel::STATUS_ASSIGNED);
        } elseif ($tab === 'completed') {
            $query->whereIn('reviews.status', [ReviewModel::STATUS_APPROVED, ReviewModel::STATUS_REVISION, ReviewModel::STATUS_REJECTED]);
        }
        if ($search !== '') {
            $query->groupStart()
                ->like('datasets.title', $search)
                ->orLike('users.name', $search)
                ->orLike('datasets.category', $search)
                ->orLike('datasets.content_formats', $search)
                ->groupEnd();
        }
        if ($access !== '' && array_key_exists($access, DatasetModel::accessOptions())) {
            $query->where('datasets.access_type', $access);
        }
        if ($dataType !== '') {
            $query->where('datasets.data_type', $dataType);
        }
        $cutoff = $this->ageCutoff($age);
        if ($cutoff !== null) {
            $query->where('reviews.assigned_at <=', $cutoff);
        }

        match ($sort) {
            'newest' => $query->orderBy('reviews.assigned_at', 'DESC'),
            'title' => $query->orderBy('datasets.title', 'ASC'),
            'contributor' => $query->orderBy('users.name', 'ASC'),
            default => $query->orderBy('reviews.assigned_at', 'ASC'),
        };

        $reviews = $query->paginate(12);
        foreach ($reviews as &$review) {
            $review['latest_file'] = model(DatasetFileModel::class)->where('dataset_id', (int) $review['dataset_id'])->orderBy('id', 'DESC')->first();
            $answers = ModerationWorkflow::decodeChecklist($stage, $review['checklist'] ?? null);
            $review['progress'] = ModerationWorkflow::checklistProgress($answers);
            $review['age_label'] = $this->ageLabel((string) $review['assigned_at']);
            $review['age_days'] = $this->ageDays((string) $review['assigned_at']);
        }
        unset($review);

        return view('reviews/index', [
            'title' => ucfirst($stage) . ' Review Queue',
            'stage' => $stage,
            'reviews' => $reviews,
            'pager' => $reviewModel->pager,
            'tab' => $tab,
            'search' => $search,
            'selectedAge' => $age,
            'selectedAccess' => $access,
            'selectedDataType' => $dataType,
            'selectedSort' => $sort,
            'accessOptions' => DatasetModel::accessOptions(),
            'metrics' => $this->reviewMetrics($stage),
        ]);
    }

    public function show(string $stage, int $reviewId): string
    {
        $review = $this->assignedReview($stage, $reviewId);
        $dataset = db_connect()->table('datasets')->select('datasets.*, users.name AS contributor_name, users.email AS contributor_email')->join('users', 'users.id = datasets.contributor_id', 'left')->where('datasets.id', $review['dataset_id'])->get()->getRowArray();
        if (! is_array($dataset)) {
            throw PageNotFoundException::forPageNotFound();
        }

        $answers = ModerationWorkflow::decodeChecklist($stage, old('checklist') ?: ($review['checklist'] ?? null));
        $previousTechnical = $stage === ReviewModel::STAGE_ETHICS
            ? model(ReviewModel::class)
                ->select('reviews.*, users.name AS reviewer_name')
                ->join('users', 'users.id = reviews.reviewer_id', 'left')
                ->where(['reviews.dataset_id' => $dataset['id'], 'reviews.stage' => ReviewModel::STAGE_TECHNICAL, 'reviews.status' => ReviewModel::STATUS_APPROVED])
                ->orderBy('reviews.id', 'DESC')
                ->first()
            : null;

        return view('reviews/show', [
            'title' => ucfirst($stage) . ' Review', 'stage' => $stage, 'review' => $review,
            'dataset' => $dataset, 'checklist' => ModerationWorkflow::checklist($stage),
            'answers' => $answers,
            'progress' => ModerationWorkflow::checklistProgress($answers),
            'ageLabel' => $this->ageLabel((string) $review['assigned_at']),
            'previousTechnical' => $previousTechnical,
            'latestFile' => model(DatasetFileModel::class)->where('dataset_id', $dataset['id'])->orderBy('id', 'DESC')->first(),
            'versions' => model(DatasetVersionModel::class)->where('dataset_id', $dataset['id'])->orderBy('id', 'DESC')->findAll(),
            'history' => model(ReviewModel::class)->select('reviews.*, users.name AS reviewer_name')->join('users', 'users.id = reviews.reviewer_id', 'left')->where('dataset_id', $dataset['id'])->orderBy('reviews.id', 'DESC')->findAll(),
        ]);
    }

    public function draft(string $stage, int $reviewId)
    {
        $this->assertStage($stage);
        try {
            (new ModerationWorkflow())->saveDraft(
                $reviewId,
                (int) $this->currentUserId(),
                (array) $this->request->getPost('checklist'),
                trim((string) $this->request->getPost('comments')),
                $this->request->getIPAddress()
            );
        } catch (RuntimeException $exception) {
            return redirect()->back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->back()->with('info', 'Review draft saved. The dataset remains assigned to you.');
    }

    public function decide(string $stage, int $reviewId)
    {
        $this->assertStage($stage);
        try {
            (new ModerationWorkflow())->decide($reviewId, (int) $this->currentUserId(), trim((string) $this->request->getPost('decision')), (array) $this->request->getPost('checklist'), trim((string) $this->request->getPost('comments')), $this->request->getIPAddress());
        } catch (RuntimeException $exception) {
            return redirect()->back()->withInput()->with('error', $exception->getMessage());
        }

        return redirect()->to('/review/' . $stage)->with('info', ucfirst($stage) . ' decision recorded and the workflow was advanced safely.');
    }

    public function download(string $stage, int $reviewId)
    {
        $review = $this->assignedReview($stage, $reviewId);
        $file = model(DatasetFileModel::class)->where('dataset_id', $review['dataset_id'])->orderBy('id', 'DESC')->first();
        if (! is_array($file)) {
            return redirect()->back()->with('error', 'No dataset file is available for this review.');
        }
        $path = WRITEPATH . str_replace('/', DIRECTORY_SEPARATOR, (string) $file['file_path']);
        if (! is_file($path)) {
            return redirect()->back()->with('error', 'The protected file is missing from storage.');
        }
        $this->recordAudit('review_file_download', 'review', $reviewId, 'Reviewer downloaded the protected submission file.');

        return $this->response->download($path, null)->setFileName((string) $file['original_name']);
    }

    private function assignedReview(string $stage, int $reviewId): array
    {
        $this->assertStage($stage);
        $review = model(ReviewModel::class)->where(['id' => $reviewId, 'stage' => $stage, 'reviewer_id' => (int) $this->currentUserId()])->where('status !=', ReviewModel::STATUS_REASSIGNED)->first();
        if (! is_array($review)) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $review;
    }

    private function assertStage(string $stage): void
    {
        if (! in_array($stage, [ReviewModel::STAGE_ETHICS, ReviewModel::STAGE_TECHNICAL], true)) {
            throw PageNotFoundException::forPageNotFound();
        }
        $role = $stage === ReviewModel::STAGE_ETHICS ? ModerationWorkflow::ROLE_ETHICS : ModerationWorkflow::ROLE_TECHNICAL;
        if (! $this->hasRole($role)) {
            throw PageNotFoundException::forPageNotFound();
        }
    }

    /**
     * @return array{assigned: int, aging: int, completed_week: int, revisions: int}
     */
    private function reviewMetrics(string $stage): array
    {
        $base = ['stage' => $stage, 'reviewer_id' => (int) $this->currentUserId()];
        $week = date('Y-m-d H:i:s', strtotime('-7 days'));

        return [
            'assigned' => model(ReviewModel::class)->where($base)->where('status', ReviewModel::STATUS_ASSIGNED)->countAllResults(),
            'aging' => model(ReviewModel::class)->where($base)->where('status', ReviewModel::STATUS_ASSIGNED)->where('assigned_at <=', date('Y-m-d H:i:s', strtotime('-3 days')))->countAllResults(),
            'completed_week' => model(ReviewModel::class)->where($base)->whereIn('status', [ReviewModel::STATUS_APPROVED, ReviewModel::STATUS_REVISION, ReviewModel::STATUS_REJECTED])->where('decided_at >=', $week)->countAllResults(),
            'revisions' => model(ReviewModel::class)->where($base)->where('status', ReviewModel::STATUS_REVISION)->countAllResults(),
        ];
    }

    private function ageCutoff(string $age): ?string
    {
        return match ($age) {
            '3' => date('Y-m-d H:i:s', strtotime('-3 days')),
            '7' => date('Y-m-d H:i:s', strtotime('-7 days')),
            '14' => date('Y-m-d H:i:s', strtotime('-14 days')),
            default => null,
        };
    }

    private function ageDays(string $assignedAt): int
    {
        $assigned = strtotime($assignedAt);

        return $assigned ? max(0, (int) floor((time() - $assigned) / 86400)) : 0;
    }

    private function ageLabel(string $assignedAt): string
    {
        $days = $this->ageDays($assignedAt);
        if ($days < 1) {
            return 'Assigned today';
        }

        return $days . ' day' . ($days === 1 ? '' : 's') . ' assigned';
    }
}
