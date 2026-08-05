<?php

namespace App\Models;

use CodeIgniter\Model;

class DatasetModel extends Model
{
    public const STATUS_PENDING = 'pending_technical_review';
    public const STATUS_PENDING_ETHICS = 'pending_ethics_review';
    public const STATUS_ETHICS_REVISION = 'ethics_revision_requested';
    public const STATUS_PENDING_TECHNICAL = 'pending_technical_review';
    public const STATUS_TECHNICAL_REVISION = 'technical_revision_requested';
    public const STATUS_AWAITING_PUBLICATION = 'awaiting_publication';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_REVISION_REQUESTED = 'ethics_revision_requested';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_ARCHIVED = 'archived';

    public const ACCESS_PUBLIC = 'public';
    public const ACCESS_INSTITUTIONAL = 'institutional';
    public const ACCESS_RESTRICTED = 'restricted';
    public const ACCESS_PRIVATE = 'private';

    protected $table = 'datasets';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'title',
        'description',
        'cover_image',
        'category',
        'tags',
        'data_type',
        'file_format',
        'content_formats',
        'source_type',
        'source_link',
        'form',
        'research_title',
        'project_head',
        'members',
        'contributor_id',
        'status',
        'access_type',
        'anonymized',
        'version',
        'approved_by',
        'approved_at',
        'archived_at',
        'archived_from_status',
    ];

    /**
     * @return array<string, string>
     */
    public static function statusLabels(): array
    {
        return [
            self::STATUS_PENDING_TECHNICAL => 'Pending Technical Review',
            self::STATUS_TECHNICAL_REVISION => 'Technical Revision Requested',
            self::STATUS_PENDING_ETHICS => 'Pending Ethics Review',
            self::STATUS_ETHICS_REVISION => 'Ethics Revision Requested',
            self::STATUS_AWAITING_PUBLICATION => 'Awaiting Publication',
            self::STATUS_PUBLISHED => 'Published',
            self::STATUS_ARCHIVED => 'Archived',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }

    public static function isRevisionStatus(?string $status): bool
    {
        return in_array($status, [self::STATUS_ETHICS_REVISION, self::STATUS_TECHNICAL_REVISION], true);
    }

    public static function isUnderReview(?string $status): bool
    {
        return in_array($status, [self::STATUS_PENDING_ETHICS, self::STATUS_PENDING_TECHNICAL, self::STATUS_AWAITING_PUBLICATION], true);
    }

    public static function statusLabel(?string $status): string
    {
        return self::statusLabels()[$status ?? ''] ?? 'Unknown';
    }

    /**
     * @return array<string, string>
     */
    public static function accessOptions(): array
    {
        return [
            self::ACCESS_PUBLIC => 'Public',
            self::ACCESS_INSTITUTIONAL => 'Institutional',
            self::ACCESS_RESTRICTED => 'Restricted',
            self::ACCESS_PRIVATE => 'Private',
        ];
    }

    public static function accessLabel(?string $accessType): string
    {
        return self::accessOptions()[$accessType ?? ''] ?? 'Public';
    }

    /**
     * @return array{label: string, url: ?string, tone: string}
     */
    public static function dashboardActionForStatus(?string $status, int $datasetId): array
    {
        return match ($status) {
            self::STATUS_ETHICS_REVISION,
            self::STATUS_TECHNICAL_REVISION => [
                'label' => 'Revise dataset and resubmit for review.',
                'url' => 'datasets/' . $datasetId . '/edit',
                'tone' => 'attention',
            ],
            self::STATUS_PUBLISHED => [
                'label' => 'Published in the catalog. Submit an update when the record changes.',
                'url' => 'datasets/' . $datasetId . '/edit',
                'tone' => 'ready',
            ],
            self::STATUS_PENDING_ETHICS => [
                'label' => 'Locked while ethics review is active.',
                'url' => null,
                'tone' => 'locked',
            ],
            self::STATUS_PENDING_TECHNICAL => [
                'label' => 'Locked while technical review is active.',
                'url' => null,
                'tone' => 'locked',
            ],
            self::STATUS_AWAITING_PUBLICATION => [
                'label' => 'Approved by reviewers and waiting for publication.',
                'url' => null,
                'tone' => 'locked',
            ],
            self::STATUS_REJECTED => [
                'label' => 'Review ended with rejection. Open the record for details.',
                'url' => null,
                'tone' => 'closed',
            ],
            self::STATUS_ARCHIVED => [
                'label' => 'Archived from normal browsing.',
                'url' => null,
                'tone' => 'closed',
            ],
            default => [
                'label' => 'Open this record to review its current state.',
                'url' => null,
                'tone' => 'neutral',
            ],
        };
    }

    /**
     * @return array{stage: string, detail: string, icon: string, tone: string, step: int}
     */
    public static function dashboardWorkflowForStatus(?string $status, ?string $accessType = null): array
    {
        return match ($status) {
            self::STATUS_PENDING_TECHNICAL => [
                'stage' => 'Technical review',
                'detail' => 'The ZIP package, metadata, documentation, and declared file formats are being verified.',
                'icon' => 'sdk',
                'tone' => 'technical',
                'step' => 1,
            ],
            self::STATUS_TECHNICAL_REVISION => [
                'stage' => 'Technical corrections required',
                'detail' => 'Update the package or metadata, then resubmit it to the technical reviewer.',
                'icon' => 'build_circle',
                'tone' => 'attention',
                'step' => 1,
            ],
            self::STATUS_PENDING_ETHICS => [
                'stage' => 'Research ethics review',
                'detail' => 'Technical verification passed. Consent, anonymization, clearance, and data handling are being reviewed.',
                'icon' => 'verified_user',
                'tone' => 'ethics',
                'step' => 2,
            ],
            self::STATUS_ETHICS_REVISION => [
                'stage' => 'Ethics corrections required',
                'detail' => 'Address the ethics reviewer findings, then resubmit without repeating technical approval.',
                'icon' => 'policy',
                'tone' => 'attention',
                'step' => 2,
            ],
            self::STATUS_AWAITING_PUBLICATION => [
                'stage' => 'Ready for publication',
                'detail' => 'Both review stages passed. A repository administrator is confirming final access and publication.',
                'icon' => 'publish',
                'tone' => 'publication',
                'step' => 3,
            ],
            self::STATUS_PUBLISHED => [
                'stage' => 'Published with ' . strtolower(self::accessLabel($accessType)) . ' access',
                'detail' => self::publishedAccessDescription($accessType),
                'icon' => self::accessIcon($accessType),
                'tone' => 'published',
                'step' => 4,
            ],
            self::STATUS_REJECTED => [
                'stage' => 'Submission rejected',
                'detail' => 'This submission is closed. Open the record to review the final decision and comments.',
                'icon' => 'block',
                'tone' => 'closed',
                'step' => 0,
            ],
            self::STATUS_ARCHIVED => [
                'stage' => 'Archived',
                'detail' => 'This dataset is retained in your records but removed from active workflow and catalog surfaces.',
                'icon' => 'inventory_2',
                'tone' => 'closed',
                'step' => 0,
            ],
            default => [
                'stage' => 'Workflow status unavailable',
                'detail' => 'Open the record to inspect its current repository state.',
                'icon' => 'info',
                'tone' => 'neutral',
                'step' => 0,
            ],
        };
    }

    private static function accessIcon(?string $accessType): string
    {
        return match ($accessType) {
            self::ACCESS_PUBLIC => 'public',
            self::ACCESS_INSTITUTIONAL => 'school',
            self::ACCESS_RESTRICTED => 'key',
            self::ACCESS_PRIVATE => 'lock',
            default => 'visibility',
        };
    }

    private static function publishedAccessDescription(?string $accessType): string
    {
        return match ($accessType) {
            self::ACCESS_PUBLIC => 'Visible in the public catalog. Signed-in users can download the published ZIP.',
            self::ACCESS_INSTITUTIONAL => 'Visible to signed-in repository users within the institution.',
            self::ACCESS_RESTRICTED => 'Catalog access is limited and downloads require repository authorization.',
            self::ACCESS_PRIVATE => 'Kept private to authorized repository maintainers and the contributor.',
            default => 'Published using the repository access classification.',
        };
    }
}
