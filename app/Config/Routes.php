<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Halaman Awal & Autentikasi
$routes->get('/', 'AuthController::login');
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::attemptLogin');
$routes->get('logout', 'AuthController::logout');

// Panel Guru (Screens 2, 3, 4, 4a, 4b, 5, 5b, Profil)
$routes->group('guru', ['filter' => ['auth', 'role:guru']], static function ($routes) {
    $routes->get('beranda', 'GuruController::beranda');
    $routes->get('cek-lokasi', 'GuruController::cekLokasi');
    $routes->get('rekam-muka', 'GuruController::rekamMuka');
    $routes->post('absen-masuk', 'GuruController::submitAbsen');
    $routes->get('riwayat', 'GuruController::riwayat');
    $routes->post('izin', 'GuruController::ajukanIzin');
    $routes->get('profil', 'GuruController::profil');
    $routes->get('daftar-wajah', 'GuruController::daftarWajah');
    $routes->post('simpan-wajah', 'GuruController::simpanWajah');
    $routes->post('ganti-password', 'GuruController::gantiPassword');
});

// Panel Operator & Kepala Sekolah (Screens 6, 7, 8, 9, 10, Pengaturan)
$routes->group('operator', ['filter' => ['auth', 'role:operator,kepsek']], static function ($routes) {
    $routes->get('beranda', 'OperatorController::beranda');
    $routes->get('guru', 'OperatorController::dataGuru');
    $routes->post('guru/simpan', 'OperatorController::simpanGuru');
    $routes->post('guru/reset-wajah/(:num)', 'OperatorController::resetWajahGuru/$1');
    $routes->post('guru/hapus/(:num)', 'OperatorController::hapusGuru/$1');
    $routes->get('jadwal', 'OperatorController::jadwalPiket');
    $routes->post('jadwal/simpan', 'OperatorController::simpanJadwal');
    $routes->post('jadwal/hapus/(:num)', 'OperatorController::hapusJadwal/$1');
    $routes->get('persetujuan', 'OperatorController::persetujuan');
    $routes->post('persetujuan/proses/(:num)', 'OperatorController::prosesIzin/$1');
    $routes->post('persetujuan/koreksi', 'OperatorController::koreksiAbsen');
    $routes->get('rekap', 'OperatorController::rekap');
    $routes->get('rekap/export-pdf', 'OperatorController::exportPdf');
    $routes->get('rekap/export-excel', 'OperatorController::exportExcel');
    $routes->get('pengaturan', 'OperatorController::pengaturan');
    $routes->post('pengaturan/simpan', 'OperatorController::simpanPengaturan');
});
