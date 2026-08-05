<?php

use App\Models\DatasetModel;
use App\Models\ReviewModel;
use App\Services\ModerationWorkflow;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Test\CIUnitTestCase;
use Config\Database;

final class AutomaticReviewAssignmentTest extends CIUnitTestCase
{
    private BaseConnection $connection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->connection = Database::connect([
            'DBDriver' => 'SQLite3',
            'database' => ':memory:',
            'DBPrefix' => '',
            'DBDebug' => true,
            'foreignKeys' => false,
            'busyTimeout' => 1000,
        ], false);
        $this->createSchema();
        $this->seedReviewers();
    }

    protected function tearDown(): void
    {
        $this->connection->close();

        parent::tearDown();
    }

    public function testAutomaticAssignmentBalancesEligibleReviewerQueues(): void
    {
        $workflow = new ModerationWorkflow($this->connection);
        $reviewerIds = [];

        foreach ([11, 12, 13] as $datasetId) {
            $this->insertDataset($datasetId);
            $assignment = $workflow->autoAssign($datasetId, ReviewModel::STAGE_TECHNICAL, 1, '127.0.0.1');
            $this->assertNotNull($assignment);
            $reviewerIds[] = $assignment['reviewer_id'];
        }

        $this->assertSame([2, 3, 2], $reviewerIds);
        $this->assertSame(
            [
                ['reviewer_id' => 2, 'assignments' => 2],
                ['reviewer_id' => 3, 'assignments' => 1],
            ],
            $this->connection->table('reviews')
                ->select('reviewer_id, COUNT(*) AS assignments')
                ->groupBy('reviewer_id')
                ->orderBy('reviewer_id')
                ->get()
                ->getResultArray()
        );
        $this->assertSame(3, $this->connection->table('reviews')->where('assignment_method', ReviewModel::ASSIGNMENT_AUTOMATIC)->countAllResults());
        $this->assertSame(3, $this->connection->table('notifications')->countAllResults());
        $this->assertSame(3, $this->connection->table('audit_logs')->where('action', 'review_auto_assigned')->countAllResults());
    }

    public function testAutomaticAssignmentIsIdempotentAndLeavesWorkPendingWithoutReviewer(): void
    {
        $workflow = new ModerationWorkflow($this->connection);
        $this->insertDataset(21);

        $first = $workflow->autoAssign(21, ReviewModel::STAGE_TECHNICAL, 1, '127.0.0.1');
        $duplicate = $workflow->autoAssign(21, ReviewModel::STAGE_TECHNICAL, 1, '127.0.0.1');

        $this->assertNotNull($first);
        $this->assertNull($duplicate);
        $this->assertSame(1, $this->connection->table('reviews')->where('dataset_id', 21)->countAllResults());

        $this->connection->table('users')->whereIn('id', [2, 3])->update(['status' => 'inactive']);
        $this->insertDataset(22);
        $this->assertNull($workflow->autoAssign(22, ReviewModel::STAGE_TECHNICAL, 1, '127.0.0.1'));
        $this->assertSame(0, $this->connection->table('reviews')->where('dataset_id', 22)->countAllResults());
    }

    public function testTechnicalApprovalImmediatelyCreatesBalancedEthicsAssignment(): void
    {
        $workflow = new ModerationWorkflow($this->connection);
        $this->insertDataset(31);
        $technical = $workflow->autoAssign(31, ReviewModel::STAGE_TECHNICAL, 1, '127.0.0.1');
        $this->assertNotNull($technical);

        $checklist = [];
        foreach (array_keys(ModerationWorkflow::checklist(ReviewModel::STAGE_TECHNICAL)) as $item) {
            $checklist[$item] = ['result' => 'confirmed', 'note' => ''];
        }
        $workflow->decide(
            $technical['review_id'],
            $technical['reviewer_id'],
            ReviewModel::STATUS_APPROVED,
            $checklist,
            '',
            '127.0.0.1'
        );

        $dataset = $this->connection->table('datasets')->where('id', 31)->get()->getRowArray();
        $ethics = $this->connection->table('reviews')
            ->where([
                'dataset_id' => 31,
                'stage' => ReviewModel::STAGE_ETHICS,
                'status' => ReviewModel::STATUS_ASSIGNED,
            ])
            ->get()
            ->getRowArray();

        $this->assertSame(DatasetModel::STATUS_PENDING_ETHICS, $dataset['status']);
        $this->assertIsArray($ethics);
        $this->assertSame(5, (int) $ethics['reviewer_id']);
        $this->assertSame(ReviewModel::ASSIGNMENT_AUTOMATIC, $ethics['assignment_method']);
    }

    public function testAutomaticAssignmentWillNotCreateASecondActiveStage(): void
    {
        $workflow = new ModerationWorkflow($this->connection);
        $this->insertDataset(41);
        $this->connection->table('reviews')->insert([
            'dataset_id' => 41,
            'dataset_version_id' => null,
            'stage' => ReviewModel::STAGE_ETHICS,
            'review_round' => 1,
            'reviewer_id' => 5,
            'assigned_by' => 1,
            'assignment_method' => ReviewModel::ASSIGNMENT_MANUAL,
            'status' => ReviewModel::STATUS_ASSIGNED,
            'assigned_at' => '2026-07-22 10:00:00',
            'created_at' => '2026-07-22 10:00:00',
            'updated_at' => '2026-07-22 10:00:00',
        ]);

        $this->assertNull($workflow->autoAssign(41, ReviewModel::STAGE_TECHNICAL, 1, '127.0.0.1'));
        $this->assertSame(1, $this->connection->table('reviews')->where('dataset_id', 41)->countAllResults());
    }

    public function testPendingBacklogIsDistributedWhenReviewersBecomeAvailable(): void
    {
        $workflow = new ModerationWorkflow($this->connection);
        $this->insertDataset(51);
        $this->insertDataset(52);

        $distributed = $workflow->autoAssignPending(1, '127.0.0.1', ReviewModel::STAGE_TECHNICAL);
        $assignments = $this->connection->table('reviews')
            ->select('dataset_id, reviewer_id')
            ->whereIn('dataset_id', [51, 52])
            ->orderBy('dataset_id')
            ->get()
            ->getResultArray();

        $this->assertSame(2, $distributed);
        $this->assertSame(
            [
                ['dataset_id' => 51, 'reviewer_id' => 2],
                ['dataset_id' => 52, 'reviewer_id' => 3],
            ],
            $assignments
        );
    }

    public function testUndistributedTaskNotifiesEveryActiveAdministrator(): void
    {
        $workflow = new ModerationWorkflow($this->connection);
        $this->connection->table('users')->whereIn('id', [2, 3])->update(['status' => 'inactive']);
        $this->insertDataset(61);

        $this->assertNull($workflow->autoAssign(61, ReviewModel::STAGE_TECHNICAL, 1, '127.0.0.1'));

        $notifications = $this->connection->table('notifications')
            ->select('user_id, title')
            ->where('type', 'workflow_attention')
            ->orderBy('user_id')
            ->get()
            ->getResultArray();

        $this->assertSame([
            ['user_id' => 1, 'title' => 'Technical reviewer unavailable'],
            ['user_id' => 6, 'title' => 'Technical reviewer unavailable'],
        ], $notifications);
        $this->assertSame(1, $this->connection->table('audit_logs')->where('action', 'review_auto_assignment_deferred')->countAllResults());
    }

    public function testFailedEthicsHandoffSendsOneAlertPerActiveAdministrator(): void
    {
        $workflow = new ModerationWorkflow($this->connection);
        $this->connection->table('users')->where('id', 5)->update(['status' => 'inactive']);
        $this->insertDataset(71);
        $technical = $workflow->autoAssign(71, ReviewModel::STAGE_TECHNICAL, 1, '127.0.0.1');
        $this->assertNotNull($technical);

        $checklist = [];
        foreach (array_keys(ModerationWorkflow::checklist(ReviewModel::STAGE_TECHNICAL)) as $item) {
            $checklist[$item] = ['result' => 'confirmed', 'note' => ''];
        }
        $workflow->decide(
            $technical['review_id'],
            $technical['reviewer_id'],
            ReviewModel::STATUS_APPROVED,
            $checklist,
            '',
            '127.0.0.1'
        );

        $recipients = $this->connection->table('notifications')
            ->select('user_id')
            ->where(['type' => 'workflow_attention', 'title' => 'Ethics reviewer unavailable'])
            ->orderBy('user_id')
            ->get()
            ->getResultArray();

        $this->assertSame([['user_id' => 1], ['user_id' => 6]], $recipients);
    }

    private function createSchema(): void
    {
        $statements = [
            'CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT NOT NULL, email TEXT NOT NULL, status TEXT NOT NULL)',
            'CREATE TABLE roles (id INTEGER PRIMARY KEY, name TEXT NOT NULL)',
            'CREATE TABLE user_roles (id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER NOT NULL, role_id INTEGER NOT NULL)',
            'CREATE TABLE datasets (id INTEGER PRIMARY KEY, title TEXT NOT NULL, contributor_id INTEGER NOT NULL, status TEXT NOT NULL, access_type TEXT, approved_by INTEGER, approved_at TEXT, updated_at TEXT)',
            'CREATE TABLE dataset_versions (id INTEGER PRIMARY KEY AUTOINCREMENT, dataset_id INTEGER NOT NULL)',
            'CREATE TABLE reviews (id INTEGER PRIMARY KEY AUTOINCREMENT, dataset_id INTEGER NOT NULL, dataset_version_id INTEGER, stage TEXT NOT NULL, review_round INTEGER NOT NULL, reviewer_id INTEGER NOT NULL, assigned_by INTEGER NOT NULL, assignment_method TEXT NOT NULL DEFAULT "manual", status TEXT NOT NULL, checklist TEXT, comments TEXT, draft_saved_at TEXT, reassignment_reason TEXT, assigned_at TEXT NOT NULL, decided_at TEXT, created_at TEXT, updated_at TEXT)',
            'CREATE TABLE notifications (id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER NOT NULL, type TEXT NOT NULL, title TEXT NOT NULL, message TEXT NOT NULL, link TEXT, created_at TEXT, updated_at TEXT)',
            'CREATE TABLE audit_logs (id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER, action TEXT NOT NULL, entity_type TEXT, entity_id INTEGER, details TEXT, ip_address TEXT, created_at TEXT NOT NULL)',
        ];

        foreach ($statements as $statement) {
            $this->connection->query($statement);
        }
    }

    private function seedReviewers(): void
    {
        $this->connection->table('roles')->insertBatch([
            ['id' => 1, 'name' => ModerationWorkflow::ROLE_TECHNICAL],
            ['id' => 2, 'name' => ModerationWorkflow::ROLE_ETHICS],
            ['id' => 3, 'name' => ModerationWorkflow::ROLE_ADMIN],
        ]);
        $this->connection->table('users')->insertBatch([
            ['id' => 1, 'name' => 'Workflow Actor', 'email' => 'actor@example.test', 'status' => 'active'],
            ['id' => 2, 'name' => 'Reviewer A', 'email' => 'a@example.test', 'status' => 'active'],
            ['id' => 3, 'name' => 'Reviewer B', 'email' => 'b@example.test', 'status' => 'active'],
            ['id' => 4, 'name' => 'Inactive Reviewer', 'email' => 'inactive@example.test', 'status' => 'inactive'],
            ['id' => 5, 'name' => 'Ethics Reviewer', 'email' => 'ethics@example.test', 'status' => 'active'],
            ['id' => 6, 'name' => 'Second Administrator', 'email' => 'admin2@example.test', 'status' => 'active'],
            ['id' => 7, 'name' => 'Inactive Administrator', 'email' => 'admin3@example.test', 'status' => 'inactive'],
        ]);
        $this->connection->table('user_roles')->insertBatch([
            ['user_id' => 1, 'role_id' => 3],
            ['user_id' => 2, 'role_id' => 1],
            ['user_id' => 3, 'role_id' => 1],
            ['user_id' => 4, 'role_id' => 1],
            ['user_id' => 5, 'role_id' => 2],
            ['user_id' => 6, 'role_id' => 3],
            ['user_id' => 7, 'role_id' => 3],
        ]);
    }

    private function insertDataset(int $datasetId): void
    {
        $this->connection->table('datasets')->insert([
            'id' => $datasetId,
            'title' => 'Dataset ' . $datasetId,
            'contributor_id' => 1,
            'status' => DatasetModel::STATUS_PENDING_TECHNICAL,
            'updated_at' => '2026-07-22 10:00:00',
        ]);
        $this->connection->table('dataset_versions')->insert(['dataset_id' => $datasetId]);
    }
}
