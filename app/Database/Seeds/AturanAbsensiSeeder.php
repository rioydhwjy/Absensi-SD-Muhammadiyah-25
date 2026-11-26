<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AturanAbsensiSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // Koordinat SD Muhammadiyah 25 Medan (area Medan Tembung)
        $data = [
            'id_aturan'          => 1,
            'jam_masuk_piket'    => '06:45:00',
            'jam_masuk_normal'   => '07:00:00',
            'jam_pulang'         => '14:00:00',
            'toleransi_menit'    => 0,
            'latitude_sekolah'   => 3.58720000,   // Lat Medan
            'longitude_sekolah'  => 98.68670000,   // Lng Medan
            'radius_meter'       => 50,
            'created_at'         => $now,
            'updated_at'         => $now,
        ];

        $this->db->table('aturan_absensi')->insert($data);
    }
}
