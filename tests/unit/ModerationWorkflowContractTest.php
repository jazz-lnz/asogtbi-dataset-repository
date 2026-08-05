<?php

use App\Models\DatasetModel;
use App\Models\ReviewModel;
use App\Services\ModerationWorkflow;
use CodeIgniter\Test\CIUnitTestCase;

final class ModerationWorkflowContractTest extends CIUnitTestCase
{
    public function testReviewStagesHaveDistinctRequiredChecklists(): void
    {
        $ethics = ModerationWorkflow::checklist(ReviewModel::STAGE_ETHICS);
        $technical = ModerationWorkflow::checklist(ReviewModel::STAGE_TECHNICAL);

        $this->assertArrayHasKey('anonymization', $ethics);
        $this->assertArrayHasKey('consent_clearance', $ethics);
        $this->assertArrayHasKey('archive_readable', $technical);
        $this->assertArrayHasKey('documentation_complete', $technical);
        $this->assertCount(5, $ethics);
        $this->assertCount(5, $technical);
    }

    public function testStructuredChecklistNormalizesLegacyAndDetailedResponses(): void
    {
        $legacy = ModerationWorkflow::normalizeChecklist(ReviewModel::STAGE_TECHNICAL, [
            'archive_readable' => '1',
        ]);
        $detailed = ModerationWorkflow::normalizeChecklist(ReviewModel::STAGE_TECHNICAL, [
            'metadata_complete' => ['result' => 'issue', 'note' => 'Missing data dictionary.'],
        ]);

        $this->assertSame('confirmed', $legacy['archive_readable']['result']);
        $this->assertSame('not_reviewed', $legacy['metadata_complete']['result']);
        $this->assertSame('issue', $detailed['metadata_complete']['result']);
        $this->assertSame('Missing data dictionary.', $detailed['metadata_complete']['note']);
    }

    public function testChecklistProgressSeparatesReviewedConfirmedAndIssues(): void
    {
        $answers = ModerationWorkflow::normalizeChecklist(ReviewModel::STAGE_ETHICS, [
            'consent_clearance' => ['result' => 'confirmed'],
            'anonymization' => ['result' => 'issue', 'note' => 'Identifiers remain.'],
        ]);

        $this->assertSame([
            'reviewed' => 2,
            'confirmed' => 1,
            'issues' => 1,
            'total' => 5,
        ], ModerationWorkflow::checklistProgress($answers));
    }

    public function testReviewerRankingPrioritizesLeastLoadedThenLongestIdle(): void
    {
        $ranked = ModerationWorkflow::rankReviewerWorkloads([
            ['id' => 7, 'active_count' => 2, 'last_assigned_at' => '2026-07-20 10:00:00'],
            ['id' => 3, 'active_count' => 1, 'last_assigned_at' => '2026-07-21 10:00:00'],
            ['id' => 5, 'active_count' => 1, 'last_assigned_at' => '2026-07-19 10:00:00'],
            ['id' => 9, 'active_count' => 1, 'last_assigned_at' => null],
        ]);

        $this->assertSame([9, 5, 3, 7], array_column($ranked, 'id'));
    }

    public function testReviewerRankingKeepsSequentialDistributionBalanced(): void
    {
        $reviewers = [
            ['id' => 1, 'active_count' => 0, 'last_assigned_at' => null],
            ['id' => 2, 'active_count' => 0, 'last_assigned_at' => null],
            ['id' => 3, 'active_count' => 0, 'last_assigned_at' => null],
        ];
        $sequence = [];

        for ($assignment = 1; $assignment <= 7; $assignment++) {
            $reviewers = ModerationWorkflow::rankReviewerWorkloads($reviewers);
            $sequence[] = $reviewers[0]['id'];
            $reviewers[0]['active_count']++;
            $reviewers[0]['last_assigned_at'] = sprintf('2026-07-22 10:00:%02d', $assignment);
        }

        $loads = array_column($reviewers, 'active_count');
        $this->assertSame([1, 2, 3, 1, 2, 3, 1], $sequence);
        $this->assertLessThanOrEqual(1, max($loads) - min($loads));
    }

    public function testAssignmentMethodsRemainExplicit(): void
    {
        $this->assertSame('automatic', ReviewModel::ASSIGNMENT_AUTOMATIC);
        $this->assertSame('manual', ReviewModel::ASSIGNMENT_MANUAL);
    }

    public function testContributorRevisionStatesAreExplicit(): void
    {
        $this->assertTrue(DatasetModel::isRevisionStatus(DatasetModel::STATUS_ETHICS_REVISION));
        $this->assertTrue(DatasetModel::isRevisionStatus(DatasetModel::STATUS_TECHNICAL_REVISION));
        $this->assertFalse(DatasetModel::isRevisionStatus(DatasetModel::STATUS_REJECTED));
    }

    public function testActiveModerationStatesAreLocked(): void
    {
        $this->assertTrue(DatasetModel::isUnderReview(DatasetModel::STATUS_PENDING_ETHICS));
        $this->assertTrue(DatasetModel::isUnderReview(DatasetModel::STATUS_PENDING_TECHNICAL));
        $this->assertTrue(DatasetModel::isUnderReview(DatasetModel::STATUS_AWAITING_PUBLICATION));
        $this->assertFalse(DatasetModel::isUnderReview(DatasetModel::STATUS_PUBLISHED));
    }

    public function testContributorDashboardActionsMatchWorkflowState(): void
    {
        $revision = DatasetModel::dashboardActionForStatus(DatasetModel::STATUS_ETHICS_REVISION, 42);
        $published = DatasetModel::dashboardActionForStatus(DatasetModel::STATUS_PUBLISHED, 42);
        $pending = DatasetModel::dashboardActionForStatus(DatasetModel::STATUS_PENDING_TECHNICAL, 42);
        $rejected = DatasetModel::dashboardActionForStatus(DatasetModel::STATUS_REJECTED, 42);
        $archived = DatasetModel::dashboardActionForStatus(DatasetModel::STATUS_ARCHIVED, 42);

        $this->assertSame('attention', $revision['tone']);
        $this->assertSame('datasets/42/edit', $revision['url']);
        $this->assertSame('ready', $published['tone']);
        $this->assertSame('datasets/42/edit', $published['url']);
        $this->assertSame('locked', $pending['tone']);
        $this->assertNull($pending['url']);
        $this->assertSame('closed', $rejected['tone']);
        $this->assertSame('closed', $archived['tone']);
    }

    public function testContributorDashboardWorkflowExplainsReviewAndPublishedAccess(): void
    {
        $technical = DatasetModel::dashboardWorkflowForStatus(DatasetModel::STATUS_PENDING_TECHNICAL);
        $ethics = DatasetModel::dashboardWorkflowForStatus(DatasetModel::STATUS_PENDING_ETHICS);
        $restricted = DatasetModel::dashboardWorkflowForStatus(DatasetModel::STATUS_PUBLISHED, DatasetModel::ACCESS_RESTRICTED);

        $this->assertSame('Technical review', $technical['stage']);
        $this->assertSame(1, $technical['step']);
        $this->assertSame('Research ethics review', $ethics['stage']);
        $this->assertSame(2, $ethics['step']);
        $this->assertSame('Published with restricted access', $restricted['stage']);
        $this->assertSame('key', $restricted['icon']);
        $this->assertStringContainsString('authorization', $restricted['detail']);
    }
}
