<?php

namespace App\Models;

use CodeIgniter\Model;

class AbsensiModel extends Model
{
    protected $table            = 'absensi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'id_guru',
        'id_aturan',
        'tanggal',
        'waktu_masuk',
        'status',
        'lat_absen',
        'lng_absen',
        'jarak_meter',
        'skor_wajah',
        'foto_capture',
        'keterangan',
    ];

    /**
     * Ambil absensi guru pada tanggal tertentu.
     */
    public function getTodayByGuru(int $idGuru, ?string $tanggal = null): ?array
    {
        $tanggal ??= date('Y-m-d');

        return $this->where([
            'id_guru' => $idGuru,
            'tanggal' => $tanggal,
        ])->first();
    }

    /**
     * Riwayat absensi guru (terbaru dulu).
     */
    public function getRiwayatByGuru(int $idGuru, int $limit = 30): array
    {
        return $this->where('id_guru', $idGuru)
            ->orderBy('tanggal', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Ringkasan status bulanan seorang guru.
     */
    public function getMonthlySummaryByGuru(int $idGuru, int $month, int $year): array
    {
        [$startDate, $endDate] = $this->monthDateRange($month, $year);

        $rows = $this->select('status, COUNT(*) as total')
            ->where('id_guru', $idGuru)
            ->where('tanggal >=', $startDate)
            ->where('tanggal <=', $endDate)
            ->groupBy('status')
            ->findAll();

        $summary = [
            'Hadir'     => 0,
            'Terlambat' => 0,
            'Izin'      => 0,
            'Sakit'     => 0,
            'Alpa'      => 0,
        ];

        foreach ($rows as $row) {
            $status = $row['status'];
            if (array_key_exists($status, $summary)) {
                $summary[$status] = (int) $row['total'];
            }
        }

        return $summary;
    }

    /**
     * Status seluruh guru untuk tanggal tertentu (untuk Beranda Operator & Koreksi).
     */
    public function getDailyTeacherStatuses(string $tanggal): array
    {
        return $this->db->table('guru g')
            ->select('g.id_guru, g.nama_lengkap, g.nip, g.jabatan, a.id as id_absensi, a.waktu_masuk, a.status, a.jarak_meter, a.skor_wajah, a.keterangan')
            ->join('absensi a', "a.id_guru = g.id_guru AND a.tanggal = " . $this->db->escape($tanggal), 'left')
            ->orderBy('g.nama_lengkap', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Rekap bulanan seluruh guru untuk halaman Rekap & Export.
     */
    public function getMonthlyRecapAll(int $month, int $year): array
    {
        $gurus = $this->db->table('guru')
            ->select('id_guru, nip, nama_lengkap, jabatan')
            ->orderBy('nama_lengkap', 'ASC')
            ->get()
            ->getResultArray();

        [$startDate, $endDate] = $this->monthDateRange($month, $year);

        $rows = $this->select('id_guru, status, COUNT(*) as total')
            ->where('tanggal >=', $startDate)
            ->where('tanggal <=', $endDate)
            ->groupBy('id_guru, status')
            ->findAll();

        $map = [];
        foreach ($rows as $r) {
            $map[$r['id_guru']][$r['status']] = (int) $r['total'];
        }

        $result = [];
        foreach ($gurus as $g) {
            $id = $g['id_guru'];
            $hadir = $map[$id]['Hadir'] ?? 0;
            $terlambat = $map[$id]['Terlambat'] ?? 0;
            $izin = $map[$id]['Izin'] ?? 0;
            $sakit = $map[$id]['Sakit'] ?? 0;
            $alpa = $map[$id]['Alpa'] ?? 0;

            $result[] = [
                'id_guru'      => $id,
                'nip'          => $g['nip'],
                'nama_lengkap' => $g['nama_lengkap'],
                'jabatan'      => $g['jabatan'],
                'hadir'        => $hadir,
                'terlambat'    => $terlambat,
                'izin'         => $izin,
                'sakit'        => $sakit,
                'alpa'         => $alpa,
                'sakit_alpa'   => $sakit + $alpa,
            ];
        }

        return $result;
    }

    /**
     * Hitung persentase kehadiran per minggu (W1-W4) dalam bulan tertentu.
     */
    public function getWeeklyAttendanceChart(int $month, int $year, int $totalGuru): array
    {
        $weeks = [
            ['label' => 'W1', 'start' => 1,  'end' => 7,  'pct' => 0],
            ['label' => 'W2', 'start' => 8,  'end' => 14, 'pct' => 0],
            ['label' => 'W3', 'start' => 15, 'end' => 21, 'pct' => 0],
            ['label' => 'W4', 'start' => 22, 'end' => 31, 'pct' => 0],
        ];

        if ($totalGuru <= 0) {
            return $weeks;
        }

        [$startDate, $endDate] = $this->monthDateRange($month, $year);

        $records = $this->select('tanggal, status')
            ->where('tanggal >=', $startDate)
            ->where('tanggal <=', $endDate)
            ->findAll();

        foreach ($weeks as &$week) {
            $presentCount = 0;
            $activeDays = [];

            foreach ($records as $rec) {
                $d = (int) date('j', strtotime($rec['tanggal']));
                if ($d >= $week['start'] && $d <= $week['end']) {
                    $activeDays[$rec['tanggal']] = true;
                    if (in_array($rec['status'], ['Hadir', 'Terlambat'], true)) {
                        $presentCount++;
                    }
                }
            }

            $dayCount = count($activeDays);
            if ($dayCount > 0) {
                $week['pct'] = (int) min(100, round(($presentCount / ($totalGuru * $dayCount)) * 100));
            }
        }

        return $weeks;
    }

    private function monthDateRange(int $month, int $year): array
    {
        $start = sprintf('%04d-%02d-01', $year, $month);
        $end   = date('Y-m-t', strtotime($start));
        return [$start, $end];
    }
}
