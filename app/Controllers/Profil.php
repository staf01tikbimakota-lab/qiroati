<?php

namespace App\Controllers;

/** Profil TPQ (identitas kop surat) + upload logo TPQ — dipakai pada kop semua PDF. */
class Profil extends BaseController
{
    private function tpq(): array
    {
        return db_connect()->table('tpq')->where('id', (int) session('user')['tpq_id'])->get()->getRowArray() ?? [];
    }

    public function index()
    {
        return view('profil/index', ['title' => 'Profil & Logo TPQ', 'tpq' => $this->tpq()]);
    }

    public function simpan()
    {
        $tid = (int) session('user')['tpq_id'];
        $old = $this->tpq();
        $json = fn (array $d, int $c = 200) => $this->response->setStatusCode($c)->setJSON($d);

        $rules = [
            'alamat' => 'permit_empty|max_length[500]',
            'desa' => 'permit_empty|max_length[100]',
            'kepala' => 'permit_empty|max_length[100]',
            'telp' => 'permit_empty|max_length[30]',
        ];
        $file = $this->request->getFile('logo');
        $hasFile = $file && $file->getError() !== UPLOAD_ERR_NO_FILE;
        if ($hasFile) {
            $rules['logo'] = 'uploaded[logo]|max_size[logo,1024]|is_image[logo]|mime_in[logo,image/png,image/jpeg]|ext_in[logo,png,jpg,jpeg]';
        }
        if (!$this->validate($rules)) {
            return $json(['message' => implode(' ', $this->validator->getErrors())], 422);
        }

        $upd = [];
        foreach (['alamat', 'desa', 'kepala', 'telp'] as $f) {
            $v = trim((string) $this->request->getPost($f));
            $upd[$f] = $v === '' ? null : $v;
        }

        $dir = upload_dir('logo');
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $removeOld = false;
        if ($hasFile && $file->isValid() && !$file->hasMoved()) {
            $name = sprintf('t%d_%s.%s', $tid, bin2hex(random_bytes(6)), $file->getMimeType() === 'image/png' ? 'png' : 'jpg');
            $file->move($dir, $name);
            $upd['logo'] = $name;
            $removeOld = true;
        } elseif ($this->request->getPost('hapus_logo') === '1') {
            $upd['logo'] = null;
            $removeOld = true;
        }

        db_connect()->table('tpq')->where('id', $tid)->update($upd);
        if ($removeOld && !empty($old['logo']) && is_file($dir . '/' . basename($old['logo']))) {
            @unlink($dir . '/' . basename($old['logo']));
        }
        return $json(['message' => 'Profil TPQ berhasil disimpan.']);
    }
}
