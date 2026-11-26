<?php

/**
 * Helper UI untuk komponen tampilan Absensi SD Muhammadiyah 25.
 */

if (! function_exists('inisial_nama')) {
    /**
     * Ambil 2 huruf inisial dari nama lengkap (abaikan gelar).
     */
    function inisial_nama(string $nama): string
    {
        $clean = preg_replace('/,.*$/', '', trim($nama));
        $parts = preg_split('/\s+/', $clean ?: $nama);
        if (count($parts) >= 2) {
            return strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
        }
        return strtoupper(substr($parts[0] ?? 'GR', 0, 2));
    }
}

if (! function_exists('sapaan_waktu')) {
    /**
     * Sapaan sesuai jam saat ini (WIB).
     */
    function sapaan_waktu(): string
    {
        $hour = (int) date('H');
        if ($hour < 11) {
            return 'Selamat pagi,';
        }
        if ($hour < 15) {
            return 'Selamat siang,';
        }
        if ($hour < 18) {
            return 'Selamat sore,';
        }
        return 'Selamat malam,';
    }
}

if (! function_exists('format_tanggal_pendek')) {
    /**
     * Format tanggal singkat seperti "Selasa, 24 Nov".
     */
    function format_tanggal_pendek(string $date): string
    {
        $bulan = [
            1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des',
        ];
        $ts = strtotime($date);
        return nama_hari($date) . ', ' . date('j', $ts) . ' ' . $bulan[(int) date('n', $ts)];
    }
}

if (! function_exists('format_bulan_tahun')) {
    /**
     * Format bulan dan tahun Indonesia, misal "November 2026".
     */
    function format_bulan_tahun(int $month, int $year): string
    {
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        ];
        return ($bulan[$month] ?? 'Bulan') . ' ' . $year;
    }
}

if (! function_exists('badge_status_class')) {
    /**
     * Kelas CSS pill untuk status absensi/pengajuan.
     */
    function badge_status_class(string $status): string
    {
        return match ($status) {
            'Hadir', 'Disetujui', 'Valid', 'Wajah terdaftar' => 'sdm-pill-green',
            'Terlambat', 'Menunggu', 'Belum daftar wajah'   => 'sdm-pill-amber',
            'Izin'                                           => 'sdm-pill-blue',
            'Sakit', 'Alpa', 'Ditolak', 'Belum absen', 'Di luar' => 'sdm-pill-red',
            default                                          => 'sdm-pill-gray',
        };
    }
}
