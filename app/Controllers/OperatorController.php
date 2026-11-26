<?php

namespace App\Controllers;

use App\Models\AbsensiModel;
use App\Models\AturanAbsensiModel;
use App\Models\AuditLogModel;
use App\Models\GuruModel;
use App\Models\JadwalPiketModel;
use App\Models\PengajuanIzinModel;
use App\Models\PenggunaModel;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class OperatorController extends BaseController
{
    private const HARI_LIST = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    public function beranda()
    {
        $today        = date('Y-m-d');
        $absensiModel = new AbsensiModel();
        $izinModel    = new PengajuanIzinModel();

        $guruStatuses = $absensiModel->getDailyTeacherStatuses($today);

        $sudahAbsen = 0;
        $belum      = 0;
        $terlambat  = 0;
        $izinSakit  = 0;

        foreach ($guruStatuses as $row) {
            $st = $row['status'] ?? null;
            if ($st === 'Hadir') {
                $sudahAbsen++;
            } elseif ($st === 'Terlambat') {
                $terlambat++;
                $sudahAbsen++;
            } elseif (in_array($st, ['Izin', 'Sakit'], true)) {
                $izinSakit++;
            } else {
                $belum++;
            }
        }

        return view('operator/beranda', [
            'title'        => 'Beranda Operator — Absensi SD Muhammadiyah 25',
            'activeNav'    => 'beranda',
            'roleLabel'    => $this->session->get('role') === 'kepsek' ? 'Kepala Sekolah' : 'Operator',
            'tanggalLabel' => format_tanggal_pendek($today),
            'stats'        => [
                'sudah_absen' => $sudahAbsen,
                'belum'       => $belum,
                'terlambat'   => $terlambat,
                'izin'        => $izinSakit,
            ],
            'pendingCount' => $izinModel->countPending(),
            'guruStatuses' => $guruStatuses,
        ]);
    }

    public function dataGuru()
    {
        $q     = trim((string) $this->request->getGet('q'));
        $gurus = (new GuruModel())->getAllWithUser($q);

        return view('operator/data_guru', [
            'title'     => 'Data Guru — Absensi SD Muhammadiyah 25',
            'activeNav' => 'guru',
            'search'    => $q,
            'gurus'     => $gurus,
        ]);
    }

    public function simpanGuru()
    {
        $idGuru   = (int) $this->request->getPost('id_guru');
        $nama     = trim((string) $this->request->getPost('nama_lengkap'));
        $nip      = trim((string) $this->request->getPost('nip'));
        $jabatan  = trim((string) $this->request->getPost('jabatan'));
        $noHp     = trim((string) $this->request->getPost('no_hp'));
        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');
        $aktif    = (int) ($this->request->getPost('status_aktif') ?? 1);

        if ($nama === '') {
            return redirect()->to('/operator/guru')->with('error', 'Nama lengkap guru wajib diisi.');
        }

        $db            = \Config\Database::connect();
        $penggunaModel = new PenggunaModel();
        $guruModel     = new GuruModel();

        $db->transStart();

        if ($idGuru > 0) {
            $existing = $guruModel->find($idGuru);
            if (! $existing) {
                $db->transRollback();
                return redirect()->to('/operator/guru')->with('error', 'Data guru tidak ditemukan.');
            }

            $guruModel->update($idGuru, [
                'nip'          => $nip ?: null,
                'nama_lengkap' => $nama,
                'jabatan'      => $jabatan ?: null,
                'no_hp'        => $noHp ?: null,
            ]);

            $userUpdate = ['status_aktif' => $aktif ? 1 : 0];
            if ($username !== '') {
                $dup = $penggunaModel->where('username', $username)
                    ->where('id !=', (int) $existing['id_pengguna'])
                    ->first();
                if ($dup) {
                    $db->transRollback();
                    return redirect()->to('/operator/guru')->with('error', "Username '{$username}' sudah digunakan.");
                }
                $userUpdate['username'] = $username;
            }
            if ($password !== '') {
                $userUpdate['password'] = password_hash($password, PASSWORD_BCRYPT);
            }
            $penggunaModel->update((int) $existing['id_pengguna'], $userUpdate);

            $aksi = 'Ubah Guru';
        } else {
            if ($username === '' || $password === '') {
                $db->transRollback();
                return redirect()->to('/operator/guru')->with('error', 'Username dan kata sandi wajib diisi untuk guru baru.');
            }

            if ($penggunaModel->where('username', $username)->first()) {
                $db->transRollback();
                return redirect()->to('/operator/guru')->with('error', "Username '{$username}' sudah digunakan.");
            }

            $idPengguna = $penggunaModel->insert([
                'username'     => $username,
                'password'     => password_hash($password, PASSWORD_BCRYPT),
                'role'         => 'guru',
                'status_aktif' => 1,
            ]);

            $guruModel->insert([
                'id_pengguna'  => $idPengguna,
                'nip'          => $nip ?: null,
                'nama_lengkap' => $nama,
                'jabatan'      => $jabatan ?: null,
                'no_hp'        => $noHp ?: null,
            ]);

            $aksi = 'Tambah Guru';
        }

        $db->transComplete();

        try {
            catat_audit($aksi, 'Data Guru', "{$aksi}: {$nama}");
        } catch (\Throwable $e) {
            // Abaikan
        }

        return redirect()->to('/operator/guru')->with('success', "Data guru {$nama} berhasil disimpan.");
    }

    public function resetWajahGuru(int $idGuru)
    {
        $guruModel = new GuruModel();
        $guru      = $guruModel->find($idGuru);
        if (! $guru) {
            return redirect()->to('/operator/guru')->with('error', 'Data guru tidak ditemukan.');
        }

        $guruModel->update($idGuru, [
            'face_descriptor' => null,
            'foto_wajah'      => null,
        ]);

        try {
            catat_audit('Reset Wajah', 'Data Guru', "Reset data wajah {$guru['nama_lengkap']}");
        } catch (\Throwable $e) {
            // Abaikan
        }

        return redirect()->to('/operator/guru')->with('success', "Data wajah {$guru['nama_lengkap']} berhasil direset.");
    }

    public function hapusGuru(int $idGuru)
    {
        $guruModel     = new GuruModel();
        $penggunaModel = new PenggunaModel();
        $guru          = $guruModel->find($idGuru);

        if (! $guru) {
            return redirect()->to('/operator/guru')->with('error', 'Data guru tidak ditemukan.');
        }

        $penggunaModel->delete((int) $guru['id_pengguna']);

        try {
            catat_audit('Hapus Guru', 'Data Guru', "Menghapus guru {$guru['nama_lengkap']}");
        } catch (\Throwable $e) {
            // Abaikan
        }

        return redirect()->to('/operator/guru')->with('success', "Guru {$guru['nama_lengkap']} berhasil dihapus.");
    }

    public function jadwalPiket()
    {
        $hariDefault = nama_hari(date('Y-m-d'));
        if (! in_array($hariDefault, self::HARI_LIST, true)) {
            $hariDefault = 'Senin';
        }

        $hariAktif = (string) ($this->request->getGet('hari') ?: $hariDefault);
        if (! in_array($hariAktif, self::HARI_LIST, true)) {
            $hariAktif = 'Senin';
        }

        $rows = (new JadwalPiketModel())->getByHari($hariAktif);

        $piketPagi      = [];
        $piketIstirahat = [];
        foreach ($rows as $row) {
            if ($row['jenis_piket'] === 'piket_pagi') {
                $piketPagi[] = $row;
            } else {
                $piketIstirahat[] = $row;
            }
        }

        return view('operator/jadwal_piket', [
            'title'          => 'Jadwal Piket — Absensi SD Muhammadiyah 25',
            'activeNav'      => 'jadwal',
            'hariList'       => self::HARI_LIST,
            'hariAktif'      => $hariAktif,
            'piketPagi'      => $piketPagi,
            'piketIstirahat' => $piketIstirahat,
            'allGurus'       => (new GuruModel())->orderBy('nama_lengkap', 'ASC')->findAll(),
            'aturan'         => (new AturanAbsensiModel())->getActive(),
        ]);
    }

    public function simpanJadwal()
    {
        $idGuru     = (int) $this->request->getPost('id_guru');
        $hari       = (string) $this->request->getPost('hari');
        $jenisPiket = $this->request->getPost('jenis_piket') === 'piket_istirahat' ? 'piket_istirahat' : 'piket_pagi';

        if (! $idGuru || ! in_array($hari, self::HARI_LIST, true)) {
            return redirect()->to('/operator/jadwal')->with('error', 'Data jadwal piket tidak valid.');
        }

        $piketModel = new JadwalPiketModel();
        $exists = $piketModel->where([
            'id_guru'     => $idGuru,
            'hari'        => $hari,
            'jenis_piket' => $jenisPiket,
        ])->first();

        if ($exists) {
            return redirect()->to('/operator/jadwal?hari=' . urlencode($hari))
                ->with('error', 'Guru tersebut sudah terjadwal pada sesi piket ini.');
        }

        $piketModel->insert([
            'id_guru'     => $idGuru,
            'hari'        => $hari,
            'jenis_piket' => $jenisPiket,
        ]);

        try {
            catat_audit('Tambah Jadwal Piket', 'Jadwal Piket', "Guru #{$idGuru} piket {$jenisPiket} hari {$hari}");
        } catch (\Throwable $e) {
            // Abaikan
        }

        return redirect()->to('/operator/jadwal?hari=' . urlencode($hari))
            ->with('success', 'Jadwal piket berhasil ditambahkan.');
    }

    public function hapusJadwal(int $idPiket)
    {
        $piketModel = new JadwalPiketModel();
        $row        = $piketModel->find($idPiket);

        if ($row) {
            $piketModel->delete($idPiket);
            try {
                catat_audit('Hapus Jadwal Piket', 'Jadwal Piket', "Menghapus jadwal piket #{$idPiket} hari {$row['hari']}");
            } catch (\Throwable $e) {
                // Abaikan
            }
            return redirect()->to('/operator/jadwal?hari=' . urlencode($row['hari']))
                ->with('success', 'Jadwal piket berhasil dihapus.');
        }

        return redirect()->to('/operator/jadwal')->with('error', 'Jadwal piket tidak ditemukan.');
    }

    public function persetujuan()
    {
        $tab     = $this->request->getGet('tab') === 'koreksi' ? 'koreksi' : 'izin';
        $tanggal = (string) ($this->request->getGet('tanggal') ?: date('Y-m-d'));

        return view('operator/persetujuan', [
            'title'        => 'Persetujuan dan Koreksi — Absensi SD Muhammadiyah 25',
            'activeNav'    => 'persetujuan',
            'activeTab'    => $tab,
            'tanggal'      => $tanggal,
            'pengajuan'    => (new PengajuanIzinModel())->getAllWithGuru(),
            'guruStatuses' => (new AbsensiModel())->getDailyTeacherStatuses($tanggal),
        ]);
    }

    public function prosesIzin(int $id)
    {
        $aksi    = $this->request->getPost('aksi');
        $catatan = trim((string) $this->request->getPost('catatan_operator'));

        $izinModel    = new PengajuanIzinModel();
        $absensiModel = new AbsensiModel();
        $aturan       = (new AturanAbsensiModel())->getActive();

        $izin = $izinModel->find($id);
        if (! $izin) {
            return redirect()->to('/operator/persetujuan')->with('error', 'Pengajuan tidak ditemukan.');
        }

        $statusBaru = $aksi === 'setujui' ? 'Disetujui' : 'Ditolak';

        $izinModel->update($id, [
            'status_persetujuan' => $statusBaru,
            'diproses_oleh'      => (int) $this->session->get('id_pengguna'),
            'catatan_operator'   => $catatan ?: null,
        ]);

        // Sinkronisasi ke tabel absensi jika disetujui
        if ($statusBaru === 'Disetujui') {
            $start = strtotime($izin['tanggal_mulai']);
            $end   = strtotime($izin['tanggal_selesai']);
            for ($ts = $start; $ts <= $end; $ts += 86400) {
                $tgl = date('Y-m-d', $ts);
                $existing = $absensiModel->getTodayByGuru((int) $izin['id_guru'], $tgl);
                $dataAbsen = [
                    'id_guru'    => (int) $izin['id_guru'],
                    'id_aturan'  => (int) $aturan['id_aturan'],
                    'tanggal'    => $tgl,
                    'status'     => $izin['jenis'],
                    'keterangan' => $izin['alasan'],
                ];
                if ($existing) {
                    $absensiModel->update((int) $existing['id'], $dataAbsen);
                } else {
                    $absensiModel->insert($dataAbsen);
                }
            }
        }

        try {
            catat_audit($statusBaru . ' Pengajuan', 'Persetujuan', "Pengajuan #{$id} ({$izin['jenis']}) {$statusBaru}");
        } catch (\Throwable $e) {
            // Abaikan
        }

        return redirect()->to('/operator/persetujuan?tab=izin')
            ->with('success', "Pengajuan berhasil {$statusBaru}.");
    }

    public function koreksiAbsen()
    {
        $idGuru     = (int) $this->request->getPost('id_guru');
        $tanggal    = (string) ($this->request->getPost('tanggal') ?: date('Y-m-d'));
        $status     = (string) $this->request->getPost('status');
        $waktuMasuk = (string) ($this->request->getPost('waktu_masuk') ?: '');
        $keterangan = trim((string) $this->request->getPost('keterangan'));

        $allowedStatus = ['Hadir', 'Terlambat', 'Izin', 'Sakit', 'Alpa'];
        if (! $idGuru || ! in_array($status, $allowedStatus, true)) {
            return redirect()->to('/operator/persetujuan?tab=koreksi&tanggal=' . urlencode($tanggal))
                ->with('error', 'Data koreksi absensi tidak valid.');
        }

        $absensiModel = new AbsensiModel();
        $aturan       = (new AturanAbsensiModel())->getActive();
        $existing     = $absensiModel->getTodayByGuru($idGuru, $tanggal);

        $data = [
            'id_guru'     => $idGuru,
            'id_aturan'   => (int) $aturan['id_aturan'],
            'tanggal'     => $tanggal,
            'status'      => $status,
            'waktu_masuk' => $waktuMasuk !== '' ? (strlen($waktuMasuk) === 5 ? $waktuMasuk . ':00' : $waktuMasuk) : ($existing['waktu_masuk'] ?? null),
            'keterangan'  => $keterangan !== '' ? $keterangan : 'Koreksi oleh operator',
        ];

        if ($existing) {
            $absensiModel->update((int) $existing['id'], $data);
        } else {
            $absensiModel->insert($data);
        }

        try {
            catat_audit('Koreksi Absensi', 'Koreksi Absen', "Koreksi absen guru #{$idGuru} tgl {$tanggal} menjadi {$status}");
        } catch (\Throwable $e) {
            // Abaikan
        }

        return redirect()->to('/operator/persetujuan?tab=koreksi&tanggal=' . urlencode($tanggal))
            ->with('success', 'Koreksi absensi berhasil disimpan.');
    }

    public function rekap()
    {
        [$month, $year] = $this->resolveMonthYear();
        $data = $this->buildRecapData($month, $year);

        return view('operator/rekap', array_merge($data, [
            'title'     => 'Rekap dan Laporan — Absensi SD Muhammadiyah 25',
            'activeNav' => 'rekap',
        ]));
    }

    public function exportPdf()
    {
        [$month, $year] = $this->resolveMonthYear();
        $data = $this->buildRecapData($month, $year);

        $html = view('operator/export_pdf', $data);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = sprintf('Rekap_Absensi_%04d_%02d.pdf', $year, $month);
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }

    public function exportExcel()
    {
        [$month, $year] = $this->resolveMonthYear();
        $data = $this->buildRecapData($month, $year);

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Absensi');

        $sheet->setCellValue('A1', 'REKAPITULASI ABSENSI GURU — SD MUHAMMADIYAH 25 MEDAN');
        $sheet->setCellValue('A2', 'Periode: ' . $data['bulanLabel'] . ' | Tingkat Kehadiran: ' . $data['overallPct'] . '%');

        $headers = ['No', 'NIP', 'Nama Guru', 'Jabatan', 'Hadir', 'Terlambat', 'Izin', 'Sakit', 'Alpa'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '4', $h);
            $sheet->getStyle($col . '4')->getFont()->setBold(true);
            $col++;
        }

        $rowIdx = 5;
        foreach ($data['rekap'] as $i => $row) {
            $sheet->setCellValue('A' . $rowIdx, $i + 1);
            $sheet->setCellValue('B' . $rowIdx, $row['nip'] ?? '-');
            $sheet->setCellValue('C' . $rowIdx, $row['nama_lengkap']);
            $sheet->setCellValue('D' . $rowIdx, $row['jabatan'] ?? '-');
            $sheet->setCellValue('E' . $rowIdx, $row['hadir']);
            $sheet->setCellValue('F' . $rowIdx, $row['terlambat']);
            $sheet->setCellValue('G' . $rowIdx, $row['izin']);
            $sheet->setCellValue('H' . $rowIdx, $row['sakit']);
            $sheet->setCellValue('I' . $rowIdx, $row['alpa']);
            $rowIdx++;
        }

        foreach (range('A', 'I') as $columnId) {
            $sheet->getColumnDimension($columnId)->setAutoSize(true);
        }

        $writer   = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'rekap_');
        $writer->save($tempFile);
        $content = file_get_contents($tempFile);
        @unlink($tempFile);

        $filename = sprintf('Rekap_Absensi_%04d_%02d.xlsx', $year, $month);
        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($content);
    }

    public function pengaturan()
    {
        return view('operator/pengaturan', [
            'title'     => 'Pengaturan Sistem — Absensi SD Muhammadiyah 25',
            'activeNav' => 'beranda',
            'aturan'    => (new AturanAbsensiModel())->getActive(),
            'auditLogs' => (new AuditLogModel())->getLatestWithUser(20),
        ]);
    }

    public function simpanPengaturan()
    {
        $aturanModel = new AturanAbsensiModel();
        $aturan      = $aturanModel->getActive();

        $data = [
            'jam_masuk_piket'   => $this->normalizeTime((string) $this->request->getPost('jam_masuk_piket'), '06:45:00'),
            'jam_masuk_normal'  => $this->normalizeTime((string) $this->request->getPost('jam_masuk_normal'), '07:00:00'),
            'jam_pulang'        => $this->normalizeTime((string) $this->request->getPost('jam_pulang'), '14:00:00'),
            'toleransi_menit'   => max(0, (int) $this->request->getPost('toleransi_menit')),
            'latitude_sekolah'  => (float) $this->request->getPost('latitude_sekolah'),
            'longitude_sekolah' => (float) $this->request->getPost('longitude_sekolah'),
            'radius_meter'      => max(10, (int) $this->request->getPost('radius_meter')),
        ];

        $aturanModel->update((int) $aturan['id_aturan'], $data);

        try {
            catat_audit('Ubah Aturan Absensi', 'Pengaturan', 'Memperbarui jam kerja & geofence sekolah');
        } catch (\Throwable $e) {
            // Abaikan
        }

        return redirect()->to('/operator/pengaturan')->with('success', 'Pengaturan aturan absensi dan geofence berhasil disimpan.');
    }

    private function resolveMonthYear(): array
    {
        $month = (int) ($this->request->getGet('bulan') ?: date('n'));
        $year  = (int) ($this->request->getGet('tahun') ?: date('Y'));
        if ($month < 1 || $month > 12) {
            $month = (int) date('n');
        }
        if ($year < 2020 || $year > 2100) {
            $year = (int) date('Y');
        }
        return [$month, $year];
    }

    private function buildRecapData(int $month, int $year): array
    {
        $absensiModel = new AbsensiModel();
        $rekap        = $absensiModel->getMonthlyRecapAll($month, $year);
        $totalGuru    = count($rekap);
        $chart        = $absensiModel->getWeeklyAttendanceChart($month, $year, $totalGuru);

        $totalHadirTerlambat = 0;
        $totalEntries        = 0;
        foreach ($rekap as $r) {
            $totalHadirTerlambat += $r['hadir'] + $r['terlambat'];
            $totalEntries        += $r['hadir'] + $r['terlambat'] + $r['izin'] + $r['sakit'] + $r['alpa'];
        }
        $overallPct = $totalEntries > 0 ? (int) round(($totalHadirTerlambat / $totalEntries) * 100) : 0;

        $prevMonth = $month === 1 ? 12 : $month - 1;
        $prevYear  = $month === 1 ? $year - 1 : $year;
        $nextMonth = $month === 12 ? 1 : $month + 1;
        $nextYear  = $month === 12 ? $year + 1 : $year;

        return [
            'month'      => $month,
            'year'       => $year,
            'bulanLabel' => format_bulan_tahun($month, $year),
            'prevMonth'  => $prevMonth,
            'prevYear'   => $prevYear,
            'nextMonth'  => $nextMonth,
            'nextYear'   => $nextYear,
            'rekap'      => $rekap,
            'chart'      => $chart,
            'overallPct' => $overallPct,
        ];
    }

    private function normalizeTime(string $time, string $fallback): string
    {
        $time = trim($time);
        if (preg_match('/^\d{2}:\d{2}$/', $time)) {
            return $time . ':00';
        }
        if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $time)) {
            return $time;
        }
        return $fallback;
    }
}
