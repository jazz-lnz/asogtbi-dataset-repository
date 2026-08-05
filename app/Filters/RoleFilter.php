<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $roles = session()->get('roles');
        $roles = is_array($roles) ? $roles : array_filter([(string) session()->get('role')]);
        if (array_intersect($roles, is_array($arguments) ? $arguments : []) === []) {
            return redirect()->to('/dashboard')->with('error', 'You do not have permission to access that workspace.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
