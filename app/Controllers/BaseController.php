<?php

namespace App\Controllers;

use App\Models\AuditLogModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    protected $helpers = ['form', 'url', 'citation', 'recommendation', 'dataset'];

    protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        $this->session = service('session');
    }

    protected function currentUserId(): ?int
    {
        $userId = $this->session->get('user_id');

        return is_numeric($userId) ? (int) $userId : null;
    }

    protected function currentRole(): ?string
    {
        $role = $this->session->get('role');

        return is_string($role) && $role !== '' ? $role : null;
    }

    protected function currentRoles(): array
    {
        $roles = $this->session->get('roles');

        return is_array($roles) ? array_values($roles) : array_filter([$this->currentRole()]);
    }

    protected function hasRole(string $role): bool
    {
        return in_array($role, $this->currentRoles(), true);
    }

    protected function isAuthenticated(): bool
    {
        return $this->currentUserId() !== null;
    }

    /**
     * @param array<string, mixed> $dataset
     */
    protected function canManageDataset(array $dataset): bool
    {
        return (int) ($dataset['contributor_id'] ?? 0) === $this->currentUserId();
    }

    protected function recordAudit(string $action, ?string $entityType = null, ?int $entityId = null, ?string $details = null): void
    {
        $auditModel = new AuditLogModel();
        $auditModel->insert([
            'user_id' => $this->currentUserId(),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'details' => $details,
            'ip_address' => $this->request->getIPAddress(),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
