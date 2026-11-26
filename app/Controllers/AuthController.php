<?php

namespace App\Controllers;

use App\Models\GuruModel;
use App\Models\PenggunaModel;

class AuthController extends BaseController
{
    public function login()
    {
        if ($this->session->get('isLoggedIn')) {
            return $this->redirectByRole($this->session->get('role'));
        }

        return view('auth/login', [
            'title'     => 'Masuk — Absensi SD Muhammadiyah 25',
            'activeTab' => $this->request->getGet('tab') === 'operator' ? 'operator' : 'guru',
        ]);
    }

    public function attemptLogin()
    {
        $tab      = $this->request->getPost('login_tab') === 'operator' ? 'operator' : 'guru';
        $identity = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');

        if ($identity === '' || $password === '') {
            return redirect()->to('/login?tab=' . $tab)
                ->withInput()
                ->with('error', 'NIP / Nama pengguna dan kata sandi wajib diisi.');
        }

        $penggunaModel = new PenggunaModel();
        $user = $penggunaModel->findByUsernameOrNip($identity);

        if (! $user || ! password_verify($password, $user['password'])) {
            return redirect()->to('/login?tab=' . $tab)
                ->withInput()
                ->with('error', 'NIP / Nama pengguna atau kata sandi salah.');
        }

        if ((int) $user['status_aktif'] !== 1) {
            return redirect()->to('/login?tab=' . $tab)
                ->withInput()
                ->with('error', 'Akun Anda sedang dinonaktifkan. Hubungi operator sekolah.');
        }

        if ($tab === 'guru' && $user['role'] !== 'guru') {
            return redirect()->to('/login?tab=operator')
                ->withInput()
                ->with('error', 'Akun ini adalah akun Operator/Kepala Sekolah. Silakan masuk melalui tab Operator.');
        }

        if ($tab === 'operator' && ! in_array($user['role'], ['operator', 'kepsek'], true)) {
            return redirect()->to('/login?tab=guru')
                ->withInput()
                ->with('error', 'Akun ini adalah akun Guru. Silakan masuk melalui tab Guru.');
        }

        $guruModel = new GuruModel();
        $guru = $guruModel->findByPenggunaId((int) $user['id']);

        $namaLengkap = $guru['nama_lengkap'] ?? ($user['role'] === 'kepsek' ? 'Kepala Sekolah' : 'Operator Sekolah');

        $this->session->set([
            'isLoggedIn'   => true,
            'id_pengguna'  => (int) $user['id'],
            'username'     => $user['username'],
            'role'         => $user['role'],
            'id_guru'      => $guru ? (int) $guru['id_guru'] : null,
            'nama_lengkap' => $namaLengkap,
        ]);

        try {
            catat_audit('Login', 'Auth', "Pengguna {$user['username']} ({$user['role']}) berhasil login");
        } catch (\Throwable $e) {
            // Abaikan error audit jika log gagal ditulis
        }

        return $this->redirectByRole($user['role']);
    }

    public function logout()
    {
        if ($this->session->get('isLoggedIn')) {
            try {
                catat_audit('Logout', 'Auth', 'Pengguna keluar dari sistem');
            } catch (\Throwable $e) {
                // Abaikan
            }
        }

        $this->session->destroy();
        return redirect()->to('/login')->with('success', 'Anda telah keluar dari sistem.');
    }

    private function redirectByRole(?string $role)
    {
        if ($role === 'guru') {
            return redirect()->to('/guru/beranda');
        }
        return redirect()->to('/operator/beranda');
    }
}
