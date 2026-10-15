<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/** Pemakaian di route: ['filter' => 'auth'] atau ['filter' => 'auth:tpq,superadmin'] */
class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        helper('app');
        $u = session('user');

        if (!$u) {
            return $request->isAJAX()
                ? service('response')->setStatusCode(401)->setJSON(['message' => 'Sesi berakhir, silakan login ulang.'])
                : redirect()->to('/login');
        }
        if ($arguments && !in_array($u['role'], $arguments, true)) {
            return $request->isAJAX()
                ? service('response')->setStatusCode(403)->setJSON(['message' => 'Akses ditolak.'])
                : redirect()->to('/dashboard')->with('error', 'Anda tidak berhak mengakses halaman tersebut.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
