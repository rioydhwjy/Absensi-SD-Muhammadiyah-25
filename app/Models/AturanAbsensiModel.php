<?php

namespace App\Models;

use CodeIgniter\Model;

class AturanAbsensiModel extends Model
{
    protected $table            = 'aturan_absensi';
    protected $primaryKey       = 'id_aturan';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'jam_masuk_piket',
        'jam_masuk_normal',
        'jam_pulang',
        'toleransi_menit',
        'latitude_sekolah',
        'longitude_sekolah',
        'radius_meter',
    ];

    /**
     * Ambil aturan absensi aktif (baris pertama atau default).
     */
    public function getActive(): array
    {
        $row = $this->orderBy('id_aturan', 'ASC')->first();
        if ($row) {
            return $row;
        }

        return [
            'id_aturan'         => 1,
            'jam_masuk_piket'   => '06:45:00',
            'jam_masuk_normal'  => '07:00:00',
            'jam_pulang'        => '14:00:00',
            'toleransi_menit'   => 0,
            'latitude_sekolah'  => 3.58720000,
            'longitude_sekolah' => 98.68670000,
            'radius_meter'      => 50,
        ];
    }
}
