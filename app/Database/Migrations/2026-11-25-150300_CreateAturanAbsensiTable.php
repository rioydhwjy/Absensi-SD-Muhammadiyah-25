<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAturanAbsensiTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_aturan' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'jam_masuk_piket' => [
                'type'    => 'TIME',
                'default' => '06:45:00',
            ],
            'jam_masuk_normal' => [
                'type'    => 'TIME',
                'default' => '07:00:00',
            ],
            'jam_pulang' => [
                'type'    => 'TIME',
                'default' => '14:00:00',
            ],
            'toleransi_menit' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'latitude_sekolah' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,8',
                'null'       => true,
            ],
            'longitude_sekolah' => [
                'type'       => 'DECIMAL',
                'constraint' => '11,8',
                'null'       => true,
            ],
            'radius_meter' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 50,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_aturan', true);
        $this->forge->createTable('aturan_absensi', true);
    }

    public function down()
    {
        $this->forge->dropTable('aturan_absensi', true);
    }
}
