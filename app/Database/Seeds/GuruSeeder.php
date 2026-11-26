<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class GuruSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $data = [
            [
                'id_guru'         => 1,
                'id_pengguna'     => 3,
                'nip'             => '198501012010011001',
                'nama_lengkap'    => 'Ahmad Fauzi, S.Pd.',
                'jabatan'         => 'Guru Kelas 1',
                'no_hp'           => '081234567801',
                'face_descriptor' => null,
                'foto_wajah'      => null,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            [
                'id_guru'         => 2,
                'id_pengguna'     => 4,
                'nip'             => '199002152012042002',
                'nama_lengkap'    => 'Siti Nurhaliza, S.Pd.I.',
                'jabatan'         => 'Guru Kelas 2',
                'no_hp'           => '081234567802',
                'face_descriptor' => null,
                'foto_wajah'      => null,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
            [
                'id_guru'         => 3,
                'id_pengguna'     => 5,
                'nip'             => '198807202015031003',
                'nama_lengkap'    => 'Budi Santoso, S.Pd.',
                'jabatan'         => 'Guru Kelas 3',
                'no_hp'           => '081234567803',
                'face_descriptor' => null,
                'foto_wajah'      => null,
                'created_at'      => $now,
                'updated_at'      => $now,
            ],
        ];

        $this->db->table('guru')->insertBatch($data);
    }
}
