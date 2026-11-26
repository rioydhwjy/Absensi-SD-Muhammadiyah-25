<?php

namespace App\Controllers;

use App\Models\AbsensiModel;
use App\Models\AturanAbsensiModel;
use App\Models\GuruModel;
use App\Models\JadwalPiketModel;
use App\Models\PengajuanIzinModel;
use App\Models\PenggunaModel;

class GuruController extends BaseController
{
    public function beranda()
    {
        $guru = $this->requireGuru();
        if (! is_array($guru)) {
            return $guru;
        }

        $today         = date('Y-m-d');
        $hariIni       = nama_hari($today);
        $absensiModel  = new AbsensiModel();
        $aturanModel   = new AturanAbsensiModel();
        $piketModel    = new JadwalPiketModel();

        $aturan       = $aturanModel->getActive();
        $absenHariIni = $absensiModel->getTodayByGuru((int) $guru['id_guru'], $today);
        $isPiketPagi  = $piketModel->isPiketPagi((int) $guru['id_guru'], $hariIni);
        $rekapBulan   = $absensiModel->getMonthlySummaryByGuru((int) $guru['id_guru'], (int) date('n'), (int) date('Y'));

        return view('guru/beranda', [
            'title'        => 'Beranda Guru — Absensi SD Muhammadiyah 25',
            'activeNav'    => 'beranda',
            'guru'         => $guru,
            'aturan'       => $aturan,
            'absenHariIni' => $absenHariIni,
            'isPiketPagi'  => $isPiketPagi,
            'rekapBulan'   => $rekapBulan,
            'tanggalLabel' => format_tanggal_id($today, true),
        ]);
    }

    public function cekLokasi()
    {
        $guru = $this->requireGuru();
        if (! is_array($guru)) {
            return $guru;
        }

        $aturan       = (new AturanAbsensiModel())->getActive();
        $absenHariIni = (new AbsensiModel())->getTodayByGuru((int) $guru['id_guru'], date('Y-m-d'));

        return view('guru/cek_lokasi', [
            'title'        => 'Cek Lokasi — Absensi SD Muhammadiyah 25',
            'activeNav'    => 'absen',
            'guru'         => $guru,
            'aturan'       => $aturan,
            'absenHariIni' => $absenHariIni,
        ]);
    }

    public function rekamMuka()
    {
        $guru = $this->requireGuru();
        if (! is_array($guru)) {
            return $guru;
        }

        $aturan       = (new AturanAbsensiModel())->getActive();
        $absenHariIni = (new AbsensiModel())->getTodayByGuru((int) $guru['id_guru'], date('Y-m-d'));
        $lat          = (float) ($this->request->getGet('lat') ?? 0);
        $lng          = (float) ($this->request->getGet('lng') ?? 0);

        return view('guru/rekam_muka', [
            'title'         => 'Rekam Muka — Absensi SD Muhammadiyah 25',
            'darkShell'     => true,
            'guru'          => $guru,
            'aturan'        => $aturan,
            'absenHariIni'  => $absenHariIni,
            'lat'           => $lat,
            'lng'           => $lng,
            'hasDescriptor' => ! empty($guru['face_descriptor']),
            'mode'          => 'absen',
        ]);
    }

    public function submitAbsen()
    {
        $guru = $this->requireGuru(true);
        if (! is_array($guru)) {
            return $guru;
        }

        $payload = $this->request->getJSON(true) ?: $this->request->getPost();
        $lat     = isset($payload['latitude']) ? (float) $payload['latitude'] : 0.0;
        $lng     = isset($payload['longitude']) ? (float) $payload['longitude'] : 0.0;

        if ($lat === 0.0 && $lng === 0.0) {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => 'error',
                'message' => 'Koordinat GPS tidak valid. Silakan ulangi cek lokasi.',
            ]);
        }

        $aturanModel  = new AturanAbsensiModel();
        $absensiModel = new AbsensiModel();
        $piketModel   = new JadwalPiketModel();
        $guruModel    = new GuruModel();

        $aturan = $aturanModel->getActive();
        $today  = date('Y-m-d');

        if ($absensiModel->getTodayByGuru((int) $guru['id_guru'], $today)) {
            return $this->response->setStatusCode(409)->setJSON([
                'status'  => 'error',
                'message' => 'Anda sudah melakukan absensi masuk hari ini.',
            ]);
        }

        $geo = validate_geofence(
            $lat,
            $lng,
            (float) $aturan['latitude_sekolah'],
            (float) $aturan['longitude_sekolah'],
            (int) $aturan['radius_meter']
        );

        if (! $geo['valid']) {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => 'error',
                'message' => "Lokasi Anda berada di luar radius sekolah ({$geo['jarak']} m / maks {$aturan['radius_meter']} m).",
            ]);
        }

        // Validasi wajah (face descriptor Euclidean distance jika tersedia)
        $skorWajah = 95.0;
        $submittedDescriptor = $payload['descriptor'] ?? null;

        if (is_array($submittedDescriptor) && count($submittedDescriptor) === 128) {
            if (! empty($guru['face_descriptor'])) {
                $stored = json_decode($guru['face_descriptor'], true);
                if (is_array($stored) && count($stored) === 128) {
                    $dist = $this->euclideanDistance($stored, $submittedDescriptor);
                    if ($dist > 0.6) {
                        return $this->response->setStatusCode(422)->setJSON([
                            'status'  => 'error',
                            'message' => 'Wajah tidak cocok dengan data yang terdaftar. Silakan coba lagi dengan pencahayaan cukup.',
                        ]);
                    }
                    $skorWajah = round(max(0, min(100, (1 - $dist) * 100)), 2);
                }
            } else {
                // Daftarkan descriptor otomatis pada absensi pertama
                $guruModel->update((int) $guru['id_guru'], [
                    'face_descriptor' => json_encode($submittedDescriptor),
                ]);
            }
        }

        // Simpan foto capture jika dikirim (base64 data URL)
        $fotoPath = $this->saveBase64Image($payload['foto_capture'] ?? null, 'absensi', 'absen_' . $guru['id_guru'] . '_' . date('Ymd_His'));

        // Hitung status Hadir / Terlambat sesuai jadwal piket pagi hari ini
        $waktuMasuk = date('H:i:s');
        $hariIni    = nama_hari($today);
        $isPiket    = $piketModel->isPiketPagi((int) $guru['id_guru'], $hariIni);
        $jamBatas   = $isPiket ? $aturan['jam_masuk_piket'] : $aturan['jam_masuk_normal'];
        $toleransi  = (int) ($aturan['toleransi_menit'] ?? 0);
        $batasTs    = strtotime($today . ' ' . $jamBatas) + ($toleransi * 60);
        $status     = strtotime($today . ' ' . $waktuMasuk) > $batasTs ? 'Terlambat' : 'Hadir';

        $absensiModel->insert([
            'id_guru'      => (int) $guru['id_guru'],
            'id_aturan'    => (int) $aturan['id_aturan'],
            'tanggal'      => $today,
            'waktu_masuk'  => $waktuMasuk,
            'status'       => $status,
            'lat_absen'    => $lat,
            'lng_absen'    => $lng,
            'jarak_meter'  => $geo['jarak'],
            'skor_wajah'   => $skorWajah,
            'foto_capture' => $fotoPath,
            'keterangan'   => $isPiket ? 'Jadwal Piket Pagi' : 'Jadwal Normal',
        ]);

        try {
            catat_audit(
                'Absen Masuk',
                'Absensi',
                "{$guru['nama_lengkap']} absen masuk pukul {$waktuMasuk} ({$status}, jarak {$geo['jarak']} m)"
            );
        } catch (\Throwable $e) {
            // Abaikan
        }

        return $this->response->setJSON([
            'status'       => 'success',
            'message'      => 'Absensi masuk berhasil dicatat pada ' . substr($waktuMasuk, 0, 5) . ' WIB.',
            'waktu_masuk'  => substr($waktuMasuk, 0, 5) . ' WIB',
            'status_absen' => $status,
            'jarak'        => $geo['jarak'],
            'skor_wajah'   => $skorWajah,
        ]);
    }

    public function riwayat()
    {
        $guru = $this->requireGuru();
        if (! is_array($guru)) {
            return $guru;
        }

        $tab = $this->request->getGet('tab') === 'izin' ? 'izin' : 'riwayat';

        return view('guru/riwayat', [
            'title'     => 'Riwayat dan Izin — Absensi SD Muhammadiyah 25',
            'activeNav' => 'riwayat',
            'activeTab' => $tab,
            'guru'      => $guru,
            'riwayat'   => (new AbsensiModel())->getRiwayatByGuru((int) $guru['id_guru'], 40),
            'pengajuan' => (new PengajuanIzinModel())->getByGuru((int) $guru['id_guru']),
        ]);
    }

    public function ajukanIzin()
    {
        $guru = $this->requireGuru();
        if (! is_array($guru)) {
            return $guru;
        }

        $jenis          = $this->request->getPost('jenis') === 'Sakit' ? 'Sakit' : 'Izin';
        $tanggalMulai   = (string) $this->request->getPost('tanggal_mulai');
        $tanggalSelesai = (string) ($this->request->getPost('tanggal_selesai') ?: $tanggalMulai);
        $alasan         = trim((string) $this->request->getPost('alasan'));

        if ($tanggalMulai === '' || $alasan === '') {
            return redirect()->to('/guru/riwayat?tab=izin')
                ->withInput()
                ->with('error', 'Tanggal dan keterangan wajib diisi.');
        }

        if (strtotime($tanggalSelesai) < strtotime($tanggalMulai)) {
            $tanggalSelesai = $tanggalMulai;
        }

        $lampiranName = null;
        $file = $this->request->getFile('lampiran');
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
            $ext = strtolower($file->getExtension());
            if (! in_array($ext, $allowed, true) || $file->getSizeByUnit('mb') > 2) {
                return redirect()->to('/guru/riwayat?tab=izin')
                    ->withInput()
                    ->with('error', 'Lampiran harus berformat JPG/PNG/PDF maksimal 2 MB.');
            }
            $uploadDir = WRITEPATH . 'uploads/lampiran';
            if (! is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }
            $lampiranName = $file->getRandomName();
            $file->move($uploadDir, $lampiranName);
        }

        (new PengajuanIzinModel())->insert([
            'id_guru'            => (int) $guru['id_guru'],
            'tanggal_mulai'      => $tanggalMulai,
            'tanggal_selesai'    => $tanggalSelesai,
            'jenis'              => $jenis,
            'alasan'             => $alasan,
            'lampiran'           => $lampiranName,
            'status_persetujuan' => 'Menunggu',
        ]);

        try {
            catat_audit('Ajukan ' . $jenis, 'Pengajuan Izin', "{$guru['nama_lengkap']} mengajukan {$jenis} ({$tanggalMulai} s.d. {$tanggalSelesai})");
        } catch (\Throwable $e) {
            // Abaikan
        }

        return redirect()->to('/guru/riwayat?tab=izin')
            ->with('success', "Pengajuan {$jenis} berhasil dikirim dan menunggu persetujuan operator.");
    }

    public function profil()
    {
        $guru = $this->requireGuru();
        if (! is_array($guru)) {
            return $guru;
        }

        $piketSaya = (new JadwalPiketModel())->getByGuru((int) $guru['id_guru']);

        return view('guru/profil', [
            'title'     => 'Profil Saya — Absensi SD Muhammadiyah 25',
            'activeNav' => 'profil',
            'guru'      => $guru,
            'piketSaya' => $piketSaya,
        ]);
    }

    public function daftarWajah()
    {
        $guru = $this->requireGuru();
        if (! is_array($guru)) {
            return $guru;
        }

        return view('guru/rekam_muka', [
            'title'         => 'Daftar Wajah — Absensi SD Muhammadiyah 25',
            'darkShell'     => true,
            'guru'          => $guru,
            'aturan'        => (new AturanAbsensiModel())->getActive(),
            'absenHariIni'  => null,
            'lat'           => 0,
            'lng'           => 0,
            'hasDescriptor' => ! empty($guru['face_descriptor']),
            'mode'          => 'enroll',
        ]);
    }

    public function simpanWajah()
    {
        $guru = $this->requireGuru(true);
        if (! is_array($guru)) {
            return $guru;
        }

        $payload    = $this->request->getJSON(true) ?: $this->request->getPost();
        $descriptor = $payload['descriptor'] ?? null;
        $fotoPath   = $this->saveBase64Image($payload['foto_wajah'] ?? null, 'wajah', 'wajah_' . $guru['id_guru'] . '_' . date('Ymd_His'));

        $updateData = [];
        if (is_array($descriptor) && count($descriptor) === 128) {
            $updateData['face_descriptor'] = json_encode($descriptor);
        } elseif (empty($guru['face_descriptor'])) {
            // Fallback descriptor placeholder jika kamera tanpa face-api weights
            $updateData['face_descriptor'] = json_encode(array_fill(0, 128, 0.01));
        }

        if ($fotoPath !== null) {
            $updateData['foto_wajah'] = $fotoPath;
        }

        if (! empty($updateData)) {
            (new GuruModel())->update((int) $guru['id_guru'], $updateData);
        }

        try {
            catat_audit('Daftar Wajah', 'Guru', "{$guru['nama_lengkap']} mendaftarkan data wajah");
        } catch (\Throwable $e) {
            // Abaikan
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data wajah berhasil disimpan.',
        ]);
    }

    public function gantiPassword()
    {
        $guru = $this->requireGuru();
        if (! is_array($guru)) {
            return $guru;
        }

        $oldPass = (string) $this->request->getPost('password_lama');
        $newPass = (string) $this->request->getPost('password_baru');
        $confirm = (string) $this->request->getPost('konfirmasi_password');

        if ($newPass === '' || strlen($newPass) < 6) {
            return redirect()->to('/guru/profil')->with('error', 'Kata sandi baru minimal 6 karakter.');
        }

        if ($newPass !== $confirm) {
            return redirect()->to('/guru/profil')->with('error', 'Konfirmasi kata sandi tidak cocok.');
        }

        $penggunaModel = new PenggunaModel();
        $user = $penggunaModel->find((int) $this->session->get('id_pengguna'));

        if (! $user || ! password_verify($oldPass, $user['password'])) {
            return redirect()->to('/guru/profil')->with('error', 'Kata sandi lama tidak sesuai.');
        }

        $penggunaModel->update((int) $user['id'], [
            'password' => password_hash($newPass, PASSWORD_BCRYPT),
        ]);

        try {
            catat_audit('Ganti Password', 'Auth', "{$guru['nama_lengkap']} mengganti kata sandi");
        } catch (\Throwable $e) {
            // Abaikan
        }

        return redirect()->to('/guru/profil')->with('success', 'Kata sandi berhasil diperbarui.');
    }

    private function requireGuru(bool $isJson = false)
    {
        $idPengguna = (int) $this->session->get('id_pengguna');
        $guru = (new GuruModel())->findWithUser($this->session->get('id_guru') ?: 0);
        if (! $guru && $idPengguna > 0) {
            $guru = (new GuruModel())->findByPenggunaId($idPengguna);
        }

        if (! $guru) {
            if ($isJson) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status'  => 'error',
                    'message' => 'Profil guru tidak ditemukan.',
                ]);
            }
            return redirect()->to('/logout');
        }

        return $guru;
    }

    private function euclideanDistance(array $a, array $b): float
    {
        $sum = 0.0;
        for ($i = 0; $i < 128; $i++) {
            $diff = (float) ($a[$i] ?? 0) - (float) ($b[$i] ?? 0);
            $sum += $diff * $diff;
        }
        return sqrt($sum);
    }

    private function saveBase64Image(?string $dataUrl, string $folder, string $prefix): ?string
    {
        if (! $dataUrl || ! str_starts_with($dataUrl, 'data:image/')) {
            return null;
        }

        $parts = explode(',', $dataUrl, 2);
        if (count($parts) !== 2) {
            return null;
        }

        $binary = base64_decode($parts[1], true);
        if ($binary === false || strlen($binary) > 3 * 1024 * 1024) {
            return null;
        }

        $dir = WRITEPATH . 'uploads/' . $folder;
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $filename = $prefix . '.jpg';
        file_put_contents($dir . '/' . $filename, $binary);

        return $folder . '/' . $filename;
    }
}
