<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call('PenggunaSeeder');
        $this->call('GuruSeeder');
        $this->call('AturanAbsensiSeeder');
        $this->call('JadwalPiketSeeder');

        // Sinkronkan sequence PostgreSQL setelah insert ID eksplisit
        if ($this->db->DBDriver === 'Postgre') {
            $sequences = [
                ['pengguna', 'id'],
                ['guru', 'id_guru'],
                ['aturan_absensi', 'id_aturan'],
                ['jadwal_piket', 'id_piket'],
            ];
            foreach ($sequences as [$table, $pk]) {
                $this->db->query(
                    "SELECT setval(pg_get_serial_sequence('{$table}', '{$pk}'), COALESCE((SELECT MAX({$pk}) FROM {$table}), 1), true)"
                );
            }
        }
    }
}
