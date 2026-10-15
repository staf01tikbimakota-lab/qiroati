<?php

namespace App\Controllers;

/**
 * Menyajikan berkas upload dari writable/uploads (tidak bisa diakses langsung lewat URL).
 * Admin TPQ hanya boleh membuka berkas milik TPQ-nya (nama berkas berawalan t{id_tpq}_).
 */
class Berkas extends BaseController
{
    public function tampil(string $kind, string $file)
    {
        helper('app');
        $u = session('user');
        $file = basename($file);

        if (!in_array($kind, ['siswa', 'guru', 'logo'], true) || !preg_match('/^t(\d+)_[a-f0-9]+\.(png|jpg)$/', $file, $m)) {
            return $this->response->setStatusCode(404);
        }
        $allowed = $u['role'] === 'superadmin' || ($u['role'] === 'tpq' && (int) $m[1] === (int) $u['tpq_id']);
        if (!$allowed) {
            return $this->response->setStatusCode(403);
        }

        $path = upload_dir($kind) . '/' . $file;
        if (!is_file($path)) {
            return $this->response->setStatusCode(404);
        }
        return $this->response
            ->setHeader('Content-Type', $m[2] === 'png' ? 'image/png' : 'image/jpeg')
            ->setHeader('Cache-Control', 'private, max-age=86400')
            ->setBody(file_get_contents($path));
    }
}
