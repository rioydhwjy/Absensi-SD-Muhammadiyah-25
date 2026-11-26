<?php

namespace App\Models;

use CodeIgniter\Model;

class JadwalPiketModel extends Model
{
    protected $table            = 'jadwal_piket';
    protected $primaryKey       = 'id_piket';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'id_guru',
        'hari',
        'jenis_piket',
    ];

    /**
     * Ambil jadwal piket untuk hari tertentu beserta nama guru.
     */
    public function getByHari(string $hari): array
    {
        return $this->db->table('jadwal_piket jp')
            ->select('jp.*, g.nama_lengkap, g.nip, g.jabatan')
            ->join('guru g', 'g.id_guru = jp.id_guru', 'inner')
            ->where('jp.hari', $hari)
            ->orderBy('jp.jenis_piket', 'ASC')
            ->orderBy('g.nama_lengkap', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Cek apakah guru punya jadwal piket pagi pada hari tertentu.
     */
    public function isPiketPagi(int $idGuru, string $hari): bool
    {
        return $this->where([
            'id_guru'     => $idGuru,
            'hari'        => $hari,
            'jenis_piket' => 'piket_pagi',
        ])->countAllResults() > 0;
    }

    /**
     * Ambil seluruh jadwal piket milik seorang guru.
     */
    public function getByGuru(int $idGuru): array
    {
        return $this->where('id_guru', $idGuru)
            ->orderBy("CASE hari WHEN 'Senin' THEN 1 WHEN 'Selasa' THEN 2 WHEN 'Rabu' THEN 3 WHEN 'Kamis' THEN 4 WHEN 'Jumat' THEN 5 WHEN 'Sabtu' THEN 6 ELSE 7 END", '', false)
            ->findAll();
    }
}
