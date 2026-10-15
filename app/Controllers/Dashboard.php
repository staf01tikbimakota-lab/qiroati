<?php

namespace App\Controllers;

use App\Libraries\Rekap;

class Dashboard extends BaseController
{
    public function index()
    {
        $u = session('user');
        $b = (int) date('n');
        $t = (int) date('Y');
        $d = ['title' => 'Dashboard', 'b' => $b, 't' => $t];
        $db = db_connect();

        switch ($u['role']) {
            case 'tpq':
                $d['rekap'] = Rekap::tpq($u['tpq_id'], $b, $t);
                $d['kirim'] = Rekap::kirimRow('tpq', $u['tpq_id'], $b, $t);
                break;
            case 'korcam':
                $d['rows'] = Rekap::korcamTpqRows($u['korcam_id'], $b, $t);
                $d['total'] = Rekap::sum(array_column($d['rows'], 'data'));
                break;
            case 'korcab':
                $d['rows'] = Rekap::korcabKorcamRows($u['korcab_id'], $b, $t);
                $d['total'] = Rekap::sum(array_map(static fn ($r) => $r['data']['total'] ?? null, $d['rows']));
                break;
            default:
                $d['cnt'] = [
                    'Korcab' => $db->table('korcab')->countAllResults(),
                    'Korcam' => $db->table('korcam')->countAllResults(),
                    'TPQ' => $db->table('tpq')->countAllResults(),
                    'User' => $db->table('users')->countAllResults(),
                ];
        }
        return view('dashboard', $d);
    }
}
