<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Database;
use Config\Email;

/**
 * @internal
 */
final class PasswordResetFlowTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resetPasswordResetSchema();

        $emailConfig = config(Email::class);
        $emailConfig->fromEmail = 'repository@example.test';
        $emailConfig->fromName = 'repository';
    }

    public function testActiveExternalPasswordAccountReceivesResetEmail(): void
    {
        $email = 'approved.external@example.test';
        $this->createUser($email);

        $result = $this->post('/forgot-password', [
            'email' => strtoupper($email),
            csrf_token() => csrf_hash(),
        ]);

        $result->assertRedirectTo(site_url('forgot-password'));
        $result->assertSessionHas('info', 'If that active password account exists, a reset link has been prepared.');

        $reset = db_connect()->table('password_resets')->where('email', $email)->get()->getRowArray();
        $this->assertIsArray($reset);
        $this->assertNull($reset['used_at']);

        $archive = service('email')->archive;
        $this->assertIsArray($archive);
        $this->assertContains($email, $archive['recipients']);
    }

    public function testUnknownAccountDoesNotCreateTokenOrSendEmail(): void
    {
        $result = $this->post('/forgot-password', [
            'email' => 'missing@example.test',
            csrf_token() => csrf_hash(),
        ]);

        $result->assertRedirectTo(site_url('forgot-password'));
        $result->assertSessionHas('info', 'If that active password account exists, a reset link has been prepared.');
        $this->assertSame(0, db_connect()->table('password_resets')->countAllResults());
        $this->assertNull(service('email')->archive);
    }

    public function testRecoveryPageDoesNotShowStaleAuthenticationToast(): void
    {
        session()->setFlashdata('error', 'Please log in to continue.');

        $result = $this->get('/forgot-password');

        $result->assertStatus(200);
        $result->assertDontSee('Please log in to continue.');
        $result->assertSessionMissing('error');
    }

    /**
     * @dataProvider ineligibleAccountProvider
     */
    public function testIneligibleAccountDoesNotCreateTokenOrSendEmail(
        string $email,
        string $provider,
        string $status,
        bool $validPasswordHash
    ): void {
        $this->createUser($email, $provider, $status, $validPasswordHash);

        $result = $this->post('/forgot-password', [
            'email' => $email,
            csrf_token() => csrf_hash(),
        ]);

        $result->assertRedirectTo(site_url('forgot-password'));
        $result->assertSessionHas('info', 'If that active password account exists, a reset link has been prepared.');
        $this->assertSame(0, db_connect()->table('password_resets')->countAllResults());
        $this->assertNull(service('email')->archive);
    }

    /**
     * @return iterable<string, array{string, string, string, bool}>
     */
    public static function ineligibleAccountProvider(): iterable
    {
        yield 'inactive password account' => ['inactive@example.test', 'local', 'inactive', true];
        yield 'Google provider account' => ['member@my.cspc.edu.ph', 'google', 'active', true];
        yield 'CSPC domain marked local' => ['legacy@my.cspc.edu.ph', 'local', 'active', true];
        yield 'local account without valid password' => ['passwordless@example.test', 'local', 'active', false];
    }

    public function testTokenBecomesInvalidWhenAccountChangesToGoogleProvider(): void
    {
        $email = 'converted@example.test';
        $userId = $this->createUser($email);
        $token = bin2hex(random_bytes(32));

        db_connect()->table('password_resets')->insert([
            'email' => $email,
            'token_hash' => hash('sha256', $token),
            'expires_at' => date('Y-m-d H:i:s', strtotime('+30 minutes')),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        db_connect()->table('users')->where('id', $userId)->update([
            'auth_provider' => 'google',
            'google_sub' => 'google-sub-' . $userId,
        ]);

        $result = $this->get('/reset-password?token=' . $token . '&email=' . rawurlencode($email));

        $result->assertStatus(200);
        $result->assertSee('Reset Link Unavailable');
        $result->assertDontSee('UPDATE PASSWORD');
    }

    private function createUser(
        string $email,
        string $provider = 'local',
        string $status = 'active',
        bool $validPasswordHash = true
    ): int {
        $now = date('Y-m-d H:i:s');
        $db = db_connect();
        $db->table('users')->insert([
            'name' => 'Password Reset Test User',
            'email' => strtolower($email),
            'google_sub' => $provider === 'google' ? 'google-sub-' . bin2hex(random_bytes(8)) : null,
            'auth_provider' => $provider,
            'password_hash' => $validPasswordHash ? password_hash('temporary-password', PASSWORD_DEFAULT) : 'not-a-password-hash',
            'status' => $status,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return (int) $db->insertID();
    }

    private function resetPasswordResetSchema(): void
    {
        $forge = Database::forge($this->db);
        $forge->dropTable('password_resets', true);
        $forge->dropTable('users', true);

        $forge->addField([
            'id' => ['type' => 'INTEGER', 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 150],
            'email' => ['type' => 'VARCHAR', 'constraint' => 190],
            'google_sub' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'auth_provider' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'local'],
            'password_hash' => ['type' => 'VARCHAR', 'constraint' => 255],
            'status' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'active'],
            'last_login_at' => ['type' => 'DATETIME', 'null' => true],
            'first_login_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $forge->addKey('id', true);
        $forge->addUniqueKey('email');
        $forge->createTable('users');

        $forge->addField([
            'id' => ['type' => 'INTEGER', 'unsigned' => true, 'auto_increment' => true],
            'email' => ['type' => 'VARCHAR', 'constraint' => 190],
            'token_hash' => ['type' => 'VARCHAR', 'constraint' => 255],
            'expires_at' => ['type' => 'DATETIME'],
            'used_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $forge->addKey('id', true);
        $forge->addKey('email');
        $forge->addKey('token_hash');
        $forge->createTable('password_resets');
    }
}
