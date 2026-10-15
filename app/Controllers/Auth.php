<?php

namespace App\Controllers;

class Auth extends BaseController
{
    public function login()
    {
        if (session('user')) {
            return redirect()->to('/dashboard');
        }
        return view('auth/login');
    }

    public function attempt()
    {
        $db = db_connect();
        $u = $db->table('users')
            ->where('username', (string) $this->request->getPost('username'))
            ->where('aktif', 1)->get()->getRowArray();

        if (!$u || !password_verify((string) $this->request->getPost('password'), $u['password'])) {
            return redirect()->back()->withInput()->with('error', 'Username atau password salah.');
        }

        $unit = 'Administrator';
        if ($u['role'] === 'tpq' && $u['tpq_id']) {
            $unit = $db->table('tpq')->select('nama')->where('id', $u['tpq_id'])->get()->getRow()->nama ?? '';
        } elseif ($u['role'] === 'korcam' && $u['korcam_id']) {
            $unit = 'Korcam ' . ($db->table('korcam')->select('nama')->where('id', $u['korcam_id'])->get()->getRow()->nama ?? '');
        } elseif ($u['role'] === 'korcab' && $u['korcab_id']) {
            $unit = 'Korcab ' . ($db->table('korcab')->select('nama')->where('id', $u['korcab_id'])->get()->getRow()->nama ?? '');
        }

        session()->regenerate();
        session()->set('user', [
            'id' => (int) $u['id'], 'nama' => $u['nama'], 'role' => $u['role'], 'unit' => $unit,
            'korcab_id' => $u['korcab_id'] ? (int) $u['korcab_id'] : null,
            'korcam_id' => $u['korcam_id'] ? (int) $u['korcam_id'] : null,
            'tpq_id' => $u['tpq_id'] ? (int) $u['tpq_id'] : null,
        ]);
        return redirect()->to('/dashboard');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
