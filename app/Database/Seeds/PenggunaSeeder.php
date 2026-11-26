<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PenggunaSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $data = [
            // 1. Operator Sekolah
            [
                'id'           => 1,
                'username'     => 'operator',
                'password'     => password_hash('operator123', PASSWORD_BCRYPT),
                'role'         => 'operator',
                'status_aktif' => 1,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            // 2. Kepala Sekolah
            [
                'id'           => 2,
                'username'     => 'kepsek',
                'password'     => password_hash('kepsek123', PASSWORD_BCRYPT),
                'role'         => 'kepsek',
                'status_aktif' => 1,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            // 3-5. Guru
            [
                'id'           => 3,
                'username'     => 'guru.ahmad',
                'password'     => password_hash('guru123', PASSWORD_BCRYPT),
                'role'         => 'guru',
                'status_aktif' => 1,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'id'           => 4,
                'username'     => 'guru.siti',
                'password'     => password_hash('guru123', PASSWORD_BCRYPT),
                'role'         => 'guru',
                'status_aktif' => 1,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'id'           => 5,
                'username'     => 'guru.budi',
                'password'     => password_hash('guru123', PASSWORD_BCRYPT),
                'role'         => 'guru',
                'status_aktif' => 1,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
        ];

        $this->db->table('pengguna')->insertBatch($data);
    }
}
