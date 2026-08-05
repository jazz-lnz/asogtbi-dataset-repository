<?php

namespace App\Models;

use CodeIgniter\Model;

class ReviewModel extends Model
{
    public const STAGE_ETHICS = 'ethics';
    public const STAGE_TECHNICAL = 'technical';
    public const STATUS_ASSIGNED = 'assigned';
    public const STATUS_REASSIGNED = 'reassigned';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_REVISION = 'revision_requested';
    public const ASSIGNMENT_AUTOMATIC = 'automatic';
    public const ASSIGNMENT_MANUAL = 'manual';

    protected $table = 'reviews';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'dataset_id',
        'dataset_version_id',
        'stage',
        'review_round',
        'reviewer_id',
        'assigned_by',
        'assignment_method',
        'status',
        'checklist',
        'comments',
        'draft_saved_at',
        'reassignment_reason',
        'assigned_at',
        'decided_at',
    ];
}
