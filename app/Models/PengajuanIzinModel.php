<?php

namespace App\Models;

use CodeIgniter\Model;

class PengajuanIzinModel extends Model
{
    protected $table            = 'pengajuan_izin';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'id_guru',
        'tanggal_mulai',
        'tanggal_selesai',
        'jenis',
        'alasan',
        'lampiran',
        'status_persetujuan',
        'diproses_oleh',
        'catatan_operator',
    ];

    /**
     * Daftar pengajuan milik satu guru.
     */
    public function getByGuru(int $idGuru): array
    {
        return $this->where('id_guru', $idGuru)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Daftar semua pengajuan beserta data guru (Menunggu diurutkan di atas).
     */
    public function getAllWithGuru(): array
    {
        return $this->db->table('pengajuan_izin pi')
            ->select('pi.*, g.nama_lengkap, g.nip, g.jabatan')
            ->join('guru g', 'g.id_guru = pi.id_guru', 'inner')
            ->orderBy("CASE pi.status_persetujuan WHEN 'Menunggu' THEN 1 WHEN 'Disetujui' THEN 2 ELSE 3 END", '', false)
            ->orderBy('pi.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Jumlah pengajuan yang menunggu persetujuan.
     */
    public function countPending(): int
    {
        return $this->where('status_persetujuan', 'Menunggu')->countAllResults();
    }
}
