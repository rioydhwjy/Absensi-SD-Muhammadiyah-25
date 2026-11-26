<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class JadwalPiketSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $data = [
            // Ahmad Fauzi: Piket Pagi Senin, Piket Istirahat Rabu
            [
                'id_guru'      => 1,
                'hari'         => 'Senin',
                'jenis_piket'  => 'piket_pagi',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'id_guru'      => 1,
                'hari'         => 'Rabu',
                'jenis_piket'  => 'piket_istirahat',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            // Siti Nurhaliza: Piket Pagi Selasa, Piket Pagi Kamis
            [
                'id_guru'      => 2,
                'hari'         => 'Selasa',
                'jenis_piket'  => 'piket_pagi',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'id_guru'      => 2,
                'hari'         => 'Kamis',
                'jenis_piket'  => 'piket_pagi',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            // Budi Santoso: Piket Pagi Rabu, Piket Istirahat Jumat
            [
                'id_guru'      => 3,
                'hari'         => 'Rabu',
                'jenis_piket'  => 'piket_pagi',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'id_guru'      => 3,
                'hari'         => 'Jumat',
                'jenis_piket'  => 'piket_istirahat',
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
        ];

        $this->db->table('jadwal_piket')->insertBatch($data);
    }
}
