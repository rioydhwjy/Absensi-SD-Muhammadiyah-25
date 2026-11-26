<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $role = $session->get('role');

        if (! $role) {
            return redirect()->to('/login');
        }

        if (! empty($arguments) && ! in_array($role, $arguments, true)) {
            if ($request->isAJAX()) {
                return service('response')
                    ->setStatusCode(403)
                    ->setJSON(['status' => 'error', 'message' => 'Anda tidak memiliki akses ke fitur ini.']);
            }

            $target = $role === 'guru' ? '/guru/beranda' : '/operator/beranda';
            return redirect()->to($target)->with('error', 'Akses ditolak untuk peran Anda.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }
}
