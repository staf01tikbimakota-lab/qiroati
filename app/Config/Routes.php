<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->setAutoRoute(false);

$routes->get('/', static fn () => redirect()->to('/dashboard'));
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attempt');
$routes->get('logout', 'Auth::logout');

// Helper untuk mendaftarkan 5 endpoint CRUD JSON
$crud = static function ($routes, string $path, string $ctrl): void {
    $routes->get($path, "{$ctrl}::index");
    $routes->get("{$path}/data", "{$ctrl}::data");
    $routes->get("{$path}/show/(:num)", "{$ctrl}::show/$1");
    $routes->post("{$path}/save", "{$ctrl}::save");
    $routes->post("{$path}/delete/(:num)", "{$ctrl}::delete/$1");
};

// Semua user yang login
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('dashboard', 'Dashboard::index');
    $routes->get('lookup/(:segment)', 'Lookup::get/$1');
    $routes->get('berkas/(:segment)/(:segment)', 'Berkas::tampil/$1/$2');
});

// Admin TPQ: data operasional + laporan bulanan
$routes->group('', ['filter' => 'auth:tpq'], static function ($routes) use ($crud) {
    foreach (['siswa' => 'Siswa', 'guru' => 'Guru', 'kelas' => 'Kelas', 'jadwal' => 'Jadwal', 'progress' => 'Progress', 'penempatan' => 'Penempatan', 'ujian' => 'Ujian', 'pembayaran' => 'Pembayaran'] as $path => $ctrl) {
        $crud($routes, $path, $ctrl);
    }
    $routes->get('absensi', 'Absensi::index');
    $routes->get('absensi/data', 'Absensi::data');
    $routes->post('absensi/simpan', 'Absensi::simpan');

    $routes->get('profil', 'Profil::index');
    $routes->post('profil/simpan', 'Profil::simpan');

    $routes->get('laporan', 'Laporan::index');
    $routes->post('laporan/kirim', 'Laporan::kirim');
    $routes->get('laporan/kelas-siswa', 'Laporan::kelasSiswa');
    $routes->get('laporan/spp-bulanan', 'Laporan::sppBulanan');
    $routes->get('laporan/spp-tahunan', 'Laporan::sppTahunan');
    $routes->get('laporan/ujian-peserta', 'Laporan::ujianPeserta');
    $routes->get('laporan/ujian-lulus', 'Laporan::ujianLulus');
    $routes->get('laporan/kwitansi/(:num)', 'Laporan::kwitansi/$1');
});

// Korcam: rekap per TPQ, kirim ke Korcab
$routes->group('', ['filter' => 'auth:korcam'], static function ($routes) {
    $routes->get('rekap/korcam', 'RekapWilayah::korcam');
    $routes->post('rekap/korcam/kirim', 'RekapWilayah::kirimKorcam');
});

// Korcab: rekap per Korcam
$routes->group('', ['filter' => 'auth:korcab'], static function ($routes) {
    $routes->get('rekap/korcab', 'RekapWilayah::korcab');
});

// Super admin: master wilayah & user
$routes->group('', ['filter' => 'auth:superadmin'], static function ($routes) use ($crud) {
    foreach (['master/korcab' => 'Korcab', 'master/korcam' => 'Korcam', 'master/tpq' => 'Tpq', 'master/jilid' => 'Jilid', 'master/users' => 'Users'] as $path => $ctrl) {
        $crud($routes, $path, $ctrl);
    }
});
