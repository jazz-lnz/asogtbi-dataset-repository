<?php

namespace App\Filters;

use App\Models\UserModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $userId = session()->get('user_id');
        if (! $userId) {
            return redirect()->to('/login')->with('error', 'Please log in to continue.');
        }

        $user = model(UserModel::class)->find((int) $userId);
        if (! is_array($user) || ($user['status'] ?? 'inactive') !== 'active') {
            session()->destroy();

            return redirect()
                ->to('/login')
                ->with('error', 'Your account is inactive. Contact a repository administrator for access.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No after-response work needed for the MVP skeleton.
    }
}
