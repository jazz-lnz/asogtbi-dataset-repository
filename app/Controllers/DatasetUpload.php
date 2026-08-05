<?php

namespace App\Controllers;

use App\Models\DatasetFileModel;
use App\Models\DatasetModel;
use App\Models\DatasetVersionModel;
use App\Models\NotificationModel;
use App\Models\ReviewModel;
use App\Services\ModerationWorkflow;

class DatasetUpload extends BaseController
{
    public function create(): string
    {
        return view('upload/create', [
            'title' => 'Upload Dataset',
            'dataTypes' => ['Text', 'Image', 'Audio', 'Video', 'Tabular'],
            'sourceTypes' => ['Primary', 'Secondary'],
            'accessTypes' => DatasetModel::accessOptions(),
            'requiredMetadata' => [
                'Title',
                'Description',
                'Tags',
                'Category',
                'Data type',
                'Formats inside ZIP',
                'Research title',
                'Project head or adviser',
                'Source type',
                'ZIP file',
            ],
        ]);
    }

    public function store()
    {
        $messages = [
            'title' => ['required' => 'Please enter a dataset title.'],
            'description' => ['required' => 'Please describe what the dataset contains.'],
            'category' => [
                'required' => 'Please enter a category.',
                'max_length' => 'Category cannot exceed 120 characters.',
            ],
            'tags' => [
                'required' => 'Please enter at least one tag.',
                'max_length' => 'Tags cannot exceed 255 characters.',
            ],
            'data_type' => ['required' => 'Please select a data type.'],
            'content_formats' => ['required' => 'Please disclose the file formats inside the ZIP package.'],
            'source_type' => ['required' => 'Please select a source type.'],
            'research_title' => ['required' => 'Please enter the research title.'],
            'project_head' => [
                'required' => 'Please enter the project head or adviser.',
                'max_length' => 'Project head cannot exceed 150 characters.',
            ],
            'members' => ['max_length' => 'Members list cannot exceed 5000 characters.'],
            'source_link' => ['valid_url_strict' => 'Please enter a valid URL starting with https://'],
            'access_type' => ['required' => 'Please select an access type.'],
            'anonymized' => ['required' => 'You must confirm anonymization before submitting.'],
            'dataset_file' => [
                'uploaded' => 'Please select a ZIP file to upload.',
                'ext_in' => 'Only ZIP files are accepted.',
            ],
            'cover_image' => [
                'is_image' => 'The dataset cover must be a valid image.',
                'mime_in' => 'Use a JPG, PNG, or WebP image for the dataset cover.',
                'max_size' => 'The dataset cover cannot exceed 4 MB.',
                'max_dims' => 'The dataset cover cannot exceed 4000 by 4000 pixels.',
            ],
        ];

        $rules = [
            'title' => 'required',
            'description' => 'required',
            'category' => 'required|max_length[120]',
            'tags' => 'required|max_length[255]',
            'data_type' => 'required|max_length[80]',
            'content_formats' => 'required|max_length[255]',
            'source_type' => 'required|max_length[80]',
            'research_title' => 'required',
            'project_head' => 'required|max_length[150]',
            'access_type' => 'required|in_list[public,institutional,restricted,private]',
            'anonymized' => 'required',
            'members' => 'permit_empty|max_length[5000]',
            'source_link' => 'permit_empty|valid_url_strict|max_length[255]',
            'dataset_file' => 'uploaded[dataset_file]|ext_in[dataset_file,zip]',
            'cover_image' => 'permit_empty|is_image[cover_image]|mime_in[cover_image,image/jpeg,image/png,image/webp]|max_size[cover_image,4096]|max_dims[cover_image,4000,4000]',
        ];

        if (! $this->validate($rules, $messages)) {
            $errorMessages = $this->validator->getErrors();
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $errorMessages)
                ->with('error', 'Please fix the highlighted fields below.');
        }

        $datasetModel = new DatasetModel();
        $fileModel = new DatasetFileModel();
        $versionModel = new DatasetVersionModel();

        $datasetId = (int) $datasetModel->insert([
            'title' => trim((string) $this->request->getPost('title')),
            'description' => trim((string) $this->request->getPost('description')),
            'category' => trim((string) $this->request->getPost('category')),
            'tags' => trim((string) $this->request->getPost('tags')),
            'data_type' => trim((string) $this->request->getPost('data_type')),
            'file_format' => 'ZIP',
            'content_formats' => trim((string) $this->request->getPost('content_formats')),
            'anonymized' => (int) ($this->request->getPost('anonymized') === '1'),
            'form' => trim((string) $this->request->getPost('form')),
            'source_type' => trim((string) $this->request->getPost('source_type')),
            'source_link' => trim((string) $this->request->getPost('source_link')),
            'research_title' => trim((string) $this->request->getPost('research_title')),
            'project_head' => trim((string) $this->request->getPost('project_head')),
            'members' => trim((string) $this->request->getPost('members')),
            'contributor_id' => (int) $this->currentUserId(),
            'status' => DatasetModel::STATUS_PENDING,
            'access_type' => trim((string) $this->request->getPost('access_type')),
            'version' => '1.0',
        ], true);

        $uploadedFile = $this->request->getFile('dataset_file');
        $fileRecordId = $this->storeDatasetFile($datasetId, $uploadedFile, (int) $this->currentUserId());
        $coverImage = $this->request->getFile('cover_image');
        if ($coverImage && $coverImage->isValid() && ! $coverImage->hasMoved()) {
            $datasetModel->update($datasetId, [
                'cover_image' => $this->storeCoverImage($datasetId, $coverImage),
            ]);
        }

        $versionModel->insert([
            'dataset_id' => $datasetId,
            'version' => '1.0',
            'change_summary' => 'Initial dataset submission.',
            'dataset_file_id' => $fileRecordId,
            'created_by' => (int) $this->currentUserId(),
        ]);

        $this->recordAudit('dataset_upload', 'dataset', $datasetId, 'Dataset submitted for technical verification.');
        $assignment = (new ModerationWorkflow())->autoAssign(
            $datasetId,
            ReviewModel::STAGE_TECHNICAL,
            (int) $this->currentUserId(),
            $this->request->getIPAddress()
        );

        $title = trim((string) $this->request->getPost('title'));
        model(NotificationModel::class)->insert([
            'user_id' => $this->currentUserId(),
            'type' => 'submission_confirmation',
            'title' => 'Dataset submitted',
            'message' => 'Your dataset "' . $title . '" has been submitted and is pending technical review.',
            'link' => '/datasets/' . $datasetId . '/edit',
        ]);
        if ($assignment !== null) {
            $admins = \Config\Database::connect()->table('users')
                ->select('users.id')
                ->join('user_roles', 'user_roles.user_id = users.id')
                ->join('roles', 'roles.id = user_roles.role_id')
                ->where('roles.name', 'repository_administrator')
                ->where('users.status', 'active')
                ->get()
                ->getResultArray();
            foreach ($admins as $admin) {
                model(NotificationModel::class)->insert([
                    'user_id' => (int) $admin['id'],
                    'type' => 'workflow_attention',
                    'title' => 'Technical review assigned',
                    'message' => 'The new dataset "' . $title . '" was automatically assigned to ' . $assignment['reviewer_name'] . ' for technical review.',
                    'link' => '/admin/datasets/' . $datasetId,
                ]);
            }
        }

        return redirect()
            ->to('/datasets/' . $datasetId . '/edit')
            ->with('info', 'Dataset submitted successfully and is now pending technical verification.');
    }

    private function storeDatasetFile(int $datasetId, object $uploadedFile, int $userId): int
    {
        $targetDir = WRITEPATH . 'uploads/datasets/' . $datasetId;
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $storedName = $uploadedFile->getRandomName();
        $uploadedFile->move($targetDir, $storedName);
        $relativePath = 'uploads/datasets/' . $datasetId . '/' . $storedName;

        return (int) model(DatasetFileModel::class)->insert([
            'dataset_id' => $datasetId,
            'stored_name' => $storedName,
            'original_name' => $uploadedFile->getClientName(),
            'file_path' => $relativePath,
            'file_size' => (int) $uploadedFile->getSize(),
            'file_type' => (string) $uploadedFile->getClientMimeType(),
            'uploaded_by' => $userId,
        ], true);
    }

    private function storeCoverImage(int $datasetId, object $uploadedFile): string
    {
        $targetDir = WRITEPATH . 'uploads/dataset-covers/' . $datasetId;
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $storedName = $uploadedFile->getRandomName();
        $uploadedFile->move($targetDir, $storedName);

        return 'uploads/dataset-covers/' . $datasetId . '/' . $storedName;
    }
}
