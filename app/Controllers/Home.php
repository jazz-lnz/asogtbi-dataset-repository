<?php

namespace App\Controllers;

use App\Models\DatasetModel;

class Home extends BaseController
{
    public function index(): string
    {
        $datasetModel = new DatasetModel();
        $db = db_connect();

        $downloadCountSubquery = $db->table('dataset_downloads')
            ->select('dataset_id, COUNT(*) AS download_count')
            ->groupBy('dataset_id')
            ->getCompiledSelect();

        $featuredDatasets = $datasetModel
            ->select('datasets.*, users.name AS author_name, COALESCE(download_counts.download_count, 0) AS download_count')
            ->join('users', 'users.id = datasets.contributor_id', 'left')
            ->join('(' . $downloadCountSubquery . ') download_counts', 'download_counts.dataset_id = datasets.id', 'left')
            ->where('datasets.status', DatasetModel::STATUS_PUBLISHED)
            ->where('datasets.access_type', DatasetModel::ACCESS_PUBLIC)
            ->where('datasets.archived_at', null)
            ->orderBy('datasets.created_at', 'DESC')
            ->findAll(5);

        $viewCountSubquery = $db->table('dataset_views')
            ->select('dataset_id, COUNT(*) AS view_count')
            ->groupBy('dataset_id')
            ->getCompiledSelect();

        // Collect IDs already shown in featured to avoid overlap
        $featuredIds = array_column($featuredDatasets, 'id');

        $popularDatasets = $db->table('datasets')
            ->select('datasets.*, users.name AS author_name, COALESCE(view_counts.view_count, 0) AS view_count')
            ->join('users', 'users.id = datasets.contributor_id', 'left')
            ->join('(' . $viewCountSubquery . ') view_counts', 'view_counts.dataset_id = datasets.id', 'left')
            ->where('datasets.status', DatasetModel::STATUS_PUBLISHED)
            ->where('datasets.access_type', DatasetModel::ACCESS_PUBLIC)
            ->where('datasets.archived_at', null)
            ->where('view_counts.view_count IS NOT NULL', null, false)
            ->whereNotIn('datasets.id', $featuredIds)
            ->orderBy('view_count', 'DESC')
            ->orderBy('datasets.created_at', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        return view('home/index', [
            'title' => 'ASOG TBI Dataset Repository',
            'publishedCount' => $datasetModel
                ->where('status', DatasetModel::STATUS_PUBLISHED)
                ->where('access_type', DatasetModel::ACCESS_PUBLIC)
                ->where('archived_at', null)
                ->countAllResults(),
            'featuredDatasets' => $featuredDatasets,
            'popularDatasets' => $popularDatasets,
        ]);
    }
    public function aboutPlatform(): string
    {
        return view('home/about_platform', ['title' => 'About the Platform · ASOG TBI Dataset Repository']);
    }

    public function aboutPartners(): string
    {
        return view('home/about_partners', ['title' => 'Partners & Team · ASOG TBI Dataset Repository']);
    }
}
