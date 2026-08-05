<?php

namespace Tests\Feature;

use App\Database\Seeds\MvpSeeder;
use App\Models\DatasetModel;
use App\Models\UserModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * @internal
 */
final class DatasetAccessTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $refresh = true;
    protected $namespace = 'App';
    protected $seed = MvpSeeder::class;

    private array $otherUserSession;

    protected function setUp(): void
    {
        parent::setUp();

        // Second, unrelated regular account to test "logged in, but neither the owner nor an admin" (currently no seeded accounts like this).
        $this->otherUserSession = $this->createRegularUser('second-user@example.test');
    }

    // ------------------------------------------------------------------
    // Guest browse
    // ------------------------------------------------------------------

    public function testGuestBrowseShowsOnlyPublishedPublicDatasets(): void
    {
        $result = $this->get('/datasets');

        $result->assertStatus(200);
        $result->assertSee('Startup Survey Responses'); // published + public

        // Not public access â€” must not appear for a guest.
        $result->assertDontSee('Innovation Program Interviews'); // institutional
        $result->assertDontSee('Restricted Incubatee Finance Extract'); // restricted
        $result->assertDontSee('Private Founder Notes'); // private
    }

    // ------------------------------------------------------------------
    // Logged-in browse
    // ------------------------------------------------------------------

    public function testAuthenticatedBrowseIncludesInstitutionalAndRestrictedButNotPrivate(): void
    {
        $session = $this->sessionDataForEmail('user@example.test');

        $result = $this->withSession($session)->get('/datasets');

        $result->assertStatus(200);
        $result->assertSee('Startup Survey Responses');       // public
        $result->assertSee('Innovation Program Interviews');  // institutional
        $result->assertSee('Restricted Incubatee Finance Extract'); // restricted

        // Private is excluded from the listing for everyone, even the owner â€”
        // the listing query never includes access_type=private.
        $result->assertDontSee('Private Founder Notes');
    }

    // ------------------------------------------------------------------
    // Public listing exclusions (non-published / archived)
    // ------------------------------------------------------------------

    public function testListingExcludesNonPublishedAndArchivedSubmissionsForGuestAndAuthenticated(): void
    {
        $nonPublishedTitles = [
            'Pending Prototype Dataset',
            'Ethics Revision Sample',
            'Technical Queue Sample',
            'Technical Revision Sample',
            'Publication Gate Sample',
            'Rejected Submission Sample',
            'Archived Public Sample',
        ];

        $guestResult = $this->get('/datasets');
        $guestResult->assertStatus(200);
        foreach ($nonPublishedTitles as $title) {
            $guestResult->assertDontSee($title);
        }

        $ownerSession = $this->sessionDataForEmail('user@example.test');
        $ownerResult = $this->withSession($ownerSession)->get('/datasets');
        $ownerResult->assertStatus(200);
        foreach ($nonPublishedTitles as $title) {
            // Even the owner of these drafts/archived items shouldn't see
            // them in the public browse listing â€” only via their dashboard.
            $ownerResult->assertDontSee($title);
        }
    }

    // ------------------------------------------------------------------
    // Detail access
    // ------------------------------------------------------------------

    public function testGuestCanViewPublicDatasetDetail(): void
    {
        $id = $this->datasetIdByTitle('Startup Survey Responses');

        $result = $this->get('/datasets/' . $id);

        $result->assertStatus(200);
        $result->assertSee('Startup Survey Responses');
    }

    public function testGuestGetsNotFoundForNonPublicDetail(): void
    {
        foreach (['Innovation Program Interviews', 'Restricted Incubatee Finance Extract', 'Private Founder Notes'] as $title) {
            $id = $this->datasetIdByTitle($title);
            $this->assertDatasetNotFound('/datasets/' . $id);
        }
    }

    public function testAuthenticatedNonOwnerCanViewInstitutionalAndRestrictedButNotPrivate(): void
    {
        $institutionalId = $this->datasetIdByTitle('Innovation Program Interviews');
        $restrictedId = $this->datasetIdByTitle('Restricted Incubatee Finance Extract');
        $privateId = $this->datasetIdByTitle('Private Founder Notes');

        $this->withSession($this->otherUserSession)->get('/datasets/' . $institutionalId)
            ->assertStatus(200);

        $this->withSession($this->otherUserSession)->get('/datasets/' . $restrictedId)
            ->assertStatus(200);

        // Must be caught rather than asserted via ->assertStatus(404) â€” the
        // controller throws PageNotFoundException directly, which propagates
        // to PHPUnit in feature tests rather than becoming a 404 response.
        try {
            $this->withSession($this->otherUserSession)->get('/datasets/' . $privateId);
            $this->fail('Expected a PageNotFoundException for the private dataset.');
        } catch (PageNotFoundException $exception) {
            $this->assertInstanceOf(PageNotFoundException::class, $exception);
        }
    }

    public function testOwnerCanViewOwnPrivateAndUnpublishedDatasetDetail(): void
    {
        $ownerSession = $this->sessionDataForEmail('user@example.test');

        $privateId = $this->datasetIdByTitle('Private Founder Notes');
        $this->withSession($ownerSession)->get('/datasets/' . $privateId)
            ->assertStatus(200);

        $pendingId = $this->datasetIdByTitle('Pending Prototype Dataset');
        $this->withSession($ownerSession)->get('/datasets/' . $pendingId)
            ->assertStatus(200);
    }

    public function testAdministratorCanViewAnyDatasetRegardlessOfAccessOrStatus(): void
    {
        $adminSession = $this->sessionDataForEmail('admin@example.test');

        foreach (['Private Founder Notes', 'Pending Prototype Dataset', 'Archived Public Sample'] as $title) {
            $id = $this->datasetIdByTitle($title);
            $this->withSession($adminSession)->get('/datasets/' . $id)
                ->assertStatus(200);
        }
    }

    // ------------------------------------------------------------------
    // Request-gated downloads
    // ------------------------------------------------------------------

    public function testGuestDownloadOfPublicDatasetRedirectsToLoginAndIsLogged(): void
    {
        $id = $this->datasetIdByTitle('Startup Survey Responses');

        $result = $this->get('/datasets/' . $id . '/download');

        $result->assertStatus(302);
        $this->assertStringStartsWith(
            site_url('login'),
            $result->getRedirectUrl(),
            'Expected guest to be redirected to the login page.'
        );
        $this->assertDownloadDeniedAuditExists($id, 'Guest attempted to download');
    }

    public function testGuestDownloadOfNonPublicDatasetRedirectsToLoginAndIsLogged(): void
    {
        foreach (['Innovation Program Interviews', 'Restricted Incubatee Finance Extract', 'Private Founder Notes'] as $title) {
            $id = $this->datasetIdByTitle($title);
            $result = $this->get('/datasets/' . $id . '/download');

            $result->assertStatus(302);
            $this->assertStringStartsWith(
                site_url('login'),
                $result->getRedirectUrl(),
                'Expected guest to be redirected to the login page.'
            );
            $this->assertDownloadDeniedAuditExists($id, 'Guest attempted to download');
        }
    }

    public function testGuestPublicDatasetDetailPromptsSignInToDownload(): void
    {
        $id = $this->datasetIdByTitle('Startup Survey Responses');

        $result = $this->get('/datasets/' . $id);

        $result->assertStatus(200);
        $result->assertSee('Sign in to Download');
        $result->assertDontSee('Download ZIP');
    }

    public function testAuthenticatedNonOwnerCanDownloadInstitutionalAndRestrictedButNotPrivate(): void
    {
        $institutionalId = $this->datasetIdByTitle('Innovation Program Interviews');
        $restrictedId = $this->datasetIdByTitle('Restricted Incubatee Finance Extract');
        $privateId = $this->datasetIdByTitle('Private Founder Notes');

        // Can download Institutional
        $this->withSession($this->otherUserSession)->get('/datasets/' . $institutionalId . '/download')
            ->assertStatus(200);

        // Can download Restricted
        $this->withSession($this->otherUserSession)->get('/datasets/' . $restrictedId . '/download')
            ->assertStatus(200);

        // Cannot download Private.
        try {
            $this->withSession($this->otherUserSession)->get('/datasets/' . $privateId . '/download');
            $this->fail('Expected a PageNotFoundException for the private dataset download.');
        } catch (PageNotFoundException $exception) {
            $this->assertInstanceOf(PageNotFoundException::class, $exception);
        }
        $this->assertDownloadDeniedAuditExists($privateId, 'Authenticated account failed');
    }

    public function testOwnerCanDownloadOwnPrivateDataset(): void
    {
        $ownerSession = $this->sessionDataForEmail('user@example.test');
        $privateId = $this->datasetIdByTitle('Private Founder Notes');

        $this->withSession($ownerSession)->get('/datasets/' . $privateId . '/download')
            ->assertStatus(200);
    }

    public function testAdministratorCanDownloadAnyDatasetRegardlessOfAccessOrStatus(): void
    {
        $adminSession = $this->sessionDataForEmail('admin@example.test');

        $testCases = [
            'Private Founder Notes',      // Tests private bypass
            'Pending Prototype Dataset',  // Tests unpublished bypass
            'Archived Public Sample'      // Tests archived bypass
        ];

        foreach ($testCases as $title) {
            $id = $this->datasetIdByTitle($title);
            $this->withSession($adminSession)->get('/datasets/' . $id . '/download')
                ->assertStatus(200);
        }
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    private function datasetIdByTitle(string $title): int
    {
        $dataset = model(DatasetModel::class)->where('title', $title)->first();
        $this->assertIsArray($dataset, 'Expected seeded dataset "' . $title . '" to exist.');

        return (int) $dataset['id'];
    }

    /**
     * @return array<string, mixed>
     */
    private function sessionDataForEmail(string $email): array
    {
        $user = model(UserModel::class)->where('email', $email)->first();
        $this->assertIsArray($user, 'Expected seeded user "' . $email . '" to exist.');

        $roleRows = db_connect()->table('user_roles')
            ->select('roles.name')
            ->join('roles', 'roles.id = user_roles.role_id')
            ->where('user_roles.user_id', (int) $user['id'])
            ->get()
            ->getResultArray();

        $roles = array_column($roleRows, 'name');

        return [
            'user_id' => (int) $user['id'],
            'user_name' => $user['name'],
            'user_email' => $user['email'],
            'role' => $roles[0] ?? 'user',
            'roles' => $roles !== [] ? $roles : ['user'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function createRegularUser(string $email): array
    {
        $now = date('Y-m-d H:i:s');
        $db = db_connect();

        $role = $db->table('roles')->where('name', 'user')->get()->getRowArray();
        if (is_array($role)) {
            $roleId = (int) $role['id'];
        } else {
            $db->table('roles')->insert([
                'name' => 'user',
                'description' => 'Authorized dataset user',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $roleId = (int) $db->insertID();
        }

        $db->table('users')->insert([
            'name' => 'Second Regular User',
            'email' => $email,
            'password_hash' => password_hash('change-me', PASSWORD_DEFAULT),
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $userId = (int) $db->insertID();

        $db->table('user_roles')->insert([
            'user_id' => $userId,
            'role_id' => $roleId,
        ]);

        return [
            'user_id' => $userId,
            'user_name' => 'Second Regular User',
            'user_email' => $email,
            'role' => 'user',
            'roles' => ['user'],
        ];
    }
    private function assertDatasetNotFound(string $uri): void
    {
        try {
            $this->get($uri);
            $this->fail('Expected a PageNotFoundException for ' . $uri);
        } catch (PageNotFoundException $exception) {
            $this->assertInstanceOf(PageNotFoundException::class, $exception);
        }
    }

    private function assertDownloadDeniedAuditExists(int $datasetId, string $detailsLike): void
    {
        $count = db_connect()->table('audit_logs')
            ->where('action', 'dataset_download_denied')
            ->where('entity_type', 'dataset')
            ->where('entity_id', $datasetId)
            ->like('details', $detailsLike)
            ->countAllResults();

        $this->assertGreaterThan(0, $count, 'Expected denied download attempt to be written to audit logs.');
    }
}
