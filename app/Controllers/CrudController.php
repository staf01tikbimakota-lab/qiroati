<?php

namespace App\Controllers;

/**
 * Basis CRUD berbasis JSON. Halaman index merender tabel + modal form,
 * seluruh operasi (list/get/save/delete) dilakukan via fetch() dari public/assets/js/crud.js
 */
abstract class CrudController extends BaseController
{
    protected string $table;
    protected string $path;
    protected string $title;
    protected array $fields = [];
    protected bool $tpqScoped = true;
    /** Kolom berkas: field => jenis (siswa|guru|logo) → disimpan di writable/uploads/... */
    protected array $files = [];
    /** Pesan sukses khusus yang diisi hook afterSave (opsional). */
    protected string $notice = '';

    abstract protected function rules(?int $id): array;

    abstract protected function ui(): array;

    protected function query()
    {
        return db_connect()->table($this->table)->select($this->table . '.*');
    }

    protected function order(): string
    {
        return $this->table . '.id DESC';
    }

    protected function tpqId(): int
    {
        return (int) (session('user')['tpq_id'] ?? 0);
    }

    protected function beforeSave(array $d, ?int $id): array
    {
        return $d;
    }

    protected function afterSave(int $id, array $d, bool $isNew): void
    {
    }

    protected function beforeDelete(int $id): void
    {
    }

    protected function hide(array $row): array
    {
        return $row;
    }

    protected function scoped()
    {
        $q = $this->query();
        if ($this->tpqScoped) {
            $q->where($this->table . '.tpq_id', $this->tpqId());
        }
        return $q;
    }

    /** Pastikan referensi (kelas/guru/siswa) milik TPQ yang sedang login. */
    protected function own(string $table, $id, string $label): void
    {
        if ($id === null || $id === '') {
            return;
        }
        $n = db_connect()->table($table)->where(['id' => $id, 'tpq_id' => $this->tpqId()])->countAllResults();
        if (!$n) {
            throw new \RuntimeException($label . ' tidak valid.');
        }
    }

    protected function opts(array $a): array
    {
        $o = [];
        foreach ($a as $k => $v) {
            $o[] = ['v' => (string) $k, 't' => $v];
        }
        return $o;
    }

    protected function json($data, int $code = 200)
    {
        return $this->response->setStatusCode($code)->setJSON($data);
    }

    public function index()
    {
        $cfg = $this->ui() + ['path' => $this->path, 'title' => $this->title];
        $extra = $cfg['extra'] ?? '';
        unset($cfg['extra']);
        return view('crud/index', ['title' => $this->title, 'cfg' => $cfg, 'extra' => $extra]);
    }

    public function data()
    {
        $rows = $this->scoped()->orderBy($this->order(), '', false)->get()->getResultArray();
        return $this->json(['data' => array_map([$this, 'hide'], $rows)]);
    }

    public function show(int $id)
    {
        $row = $this->scoped()->where($this->table . '.id', $id)->get()->getRowArray();
        return $row ? $this->json($this->hide($row)) : $this->json(['message' => 'Data tidak ditemukan.'], 404);
    }

    public function save()
    {
        $id = (int) $this->request->getPost('id') ?: null;
        $d = [];
        foreach ($this->fields as $f) {
            $v = $this->request->getPost($f);
            if ($v !== null) {
                $v = trim((string) $v);
                $d[$f] = $v === '' ? null : $v;
            }
        }

        $val = service('validation')->reset()->setRules($this->rules($id));
        if (!$val->run($d)) {
            return $this->json(['message' => 'Periksa kembali isian form.', 'errors' => $val->getErrors()], 422);
        }

        // Validasi berkas upload (gambar PNG/JPG maks. 2 MB)
        $uploads = [];
        foreach (array_keys($this->files) as $f) {
            $file = $this->request->getFile($f);
            if ($file && $file->getError() !== UPLOAD_ERR_NO_FILE) {
                if (!$this->validate([$f => "uploaded[{$f}]|max_size[{$f},2048]|is_image[{$f}]|mime_in[{$f},image/png,image/jpeg]|ext_in[{$f},png,jpg,jpeg]"])) {
                    $err = $this->validator->getErrors();
                    return $this->json(['message' => implode(' ', $err), 'errors' => $err], 422);
                }
                $uploads[$f] = $file;
            }
        }

        $moved = [];
        $trash = [];
        try {
            $db = db_connect();
            $d = $this->beforeSave($d, $id);
            $isNew = !$id;
            $scopedB = function () use ($db, $id) {
                $q = $db->table($this->table)->where('id', $id);
                if ($this->tpqScoped) {
                    $q->where('tpq_id', $this->tpqId());
                }
                return $q;
            };

            $old = [];
            if ($id) {
                $old = $scopedB()->get()->getRowArray();
                if (!$old) {
                    return $this->json(['message' => 'Data tidak ditemukan.'], 404);
                }
            }

            foreach ($this->files as $f => $kind) {
                $dir = upload_dir($kind);
                if (isset($uploads[$f])) {
                    $name = sprintf('t%d_%s.%s', $this->tpqId(), bin2hex(random_bytes(6)), $uploads[$f]->getMimeType() === 'image/png' ? 'png' : 'jpg');
                    $uploads[$f]->move($dir, $name);
                    $moved[] = $dir . '/' . $name;
                    $d[$f] = $name;
                    if (!empty($old[$f])) {
                        $trash[] = $dir . '/' . basename($old[$f]);
                    }
                } elseif ($id && $this->request->getPost('hapus_' . $f) === '1') {
                    $d[$f] = null;
                    if (!empty($old[$f])) {
                        $trash[] = $dir . '/' . basename($old[$f]);
                    }
                }
            }

            if ($id) {
                $scopedB()->update($d);
            } else {
                if ($this->tpqScoped) {
                    $d['tpq_id'] = $this->tpqId();
                }
                $db->table($this->table)->insert($d);
                $id = (int) $db->insertID();
            }
            $this->afterSave($id, $d, $isNew);
        } catch (\RuntimeException $e) {
            foreach ($moved as $m) { @unlink($m); }
            return $this->json(['message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            foreach ($moved as $m) { @unlink($m); }
            log_message('error', $e->getMessage());
            return $this->json(['message' => 'Terjadi kesalahan saat menyimpan data.'], 500);
        }

        foreach ($trash as $t) {
            if (is_file($t)) { @unlink($t); }
        }
        return $this->json(['message' => $this->notice ?: 'Data berhasil disimpan.']);
    }

    public function delete(int $id)
    {
        try {
            $this->beforeDelete($id);
            $db = db_connect();
            $row = $this->scoped()->where($this->table . '.id', $id)->get()->getRowArray();
            $q = $db->table($this->table)->where('id', $id);
            if ($this->tpqScoped) {
                $q->where('tpq_id', $this->tpqId());
            }
            $ok = $q->delete();
            if ($ok && $db->affectedRows()) {
                foreach ($this->files as $f => $kind) {
                    if (!empty($row[$f])) {
                        @unlink(upload_dir($kind) . '/' . basename($row[$f]));
                    }
                }
                return $this->json(['message' => 'Data berhasil dihapus.']);
            }
        } catch (\RuntimeException $e) {
            return $this->json(['message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            log_message('error', $e->getMessage());
        }
        return $this->json(['message' => 'Data tidak dapat dihapus karena masih dipakai data lain.'], 409);
    }
}
