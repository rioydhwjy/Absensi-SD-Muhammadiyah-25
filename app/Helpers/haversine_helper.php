<?php

/**
 * ============================================================
 * Haversine Helper
 * Kalkulasi jarak antara dua titik GPS menggunakan rumus Haversine
 * ============================================================
 */

if (!function_exists('haversine_distance')) {
    /**
     * Menghitung jarak antara dua koordinat GPS dalam meter
     *
     * @param float $lat1 Latitude titik 1
     * @param float $lon1 Longitude titik 1
     * @param float $lat2 Latitude titik 2
     * @param float $lon2 Longitude titik 2
     * @return float Jarak dalam meter
     */
    function haversine_distance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // meter

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}

if (!function_exists('validate_geofence')) {
    /**
     * Validasi apakah posisi user dalam radius geofence sekolah
     *
     * @param float $userLat Latitude user
     * @param float $userLng Longitude user
     * @param float $schoolLat Latitude sekolah
     * @param float $schoolLng Longitude sekolah
     * @param int   $radiusMeter Radius geofencing (meter)
     * @return array ['valid' => bool, 'jarak' => float]
     */
    function validate_geofence(
        float $userLat,
        float $userLng,
        float $schoolLat,
        float $schoolLng,
        int $radiusMeter
    ): array {
        $jarak = haversine_distance($userLat, $userLng, $schoolLat, $schoolLng);

        return [
            'valid' => $jarak <= $radiusMeter,
            'jarak' => round($jarak, 2),
        ];
    }
}

if (!function_exists('nama_hari')) {
    /**
     * Mendapatkan nama hari Indonesia dari tanggal
     *
     * @param string|null $date Format Y-m-d, null untuk hari ini
     * @return string
     */
    function nama_hari(?string $date = null): string
    {
        $hari = [
            'Sunday'    => 'Minggu',
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
        ];

        $dayName = date('l', strtotime($date ?? 'today'));

        return $hari[$dayName] ?? $dayName;
    }
}

if (!function_exists('format_tanggal_id')) {
    /**
     * Format tanggal ke format Indonesia
     *
     * @param string $date Format Y-m-d
     * @param bool $withDay Sertakan nama hari
     * @return string
     */
    function format_tanggal_id(string $date, bool $withDay = true): string
    {
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        ];

        $timestamp = strtotime($date);
        $d = date('j', $timestamp);
        $m = (int) date('n', $timestamp);
        $y = date('Y', $timestamp);

        $formatted = "$d {$bulan[$m]} $y";

        if ($withDay) {
            $formatted = nama_hari($date) . ', ' . $formatted;
        }

        return $formatted;
    }
}
