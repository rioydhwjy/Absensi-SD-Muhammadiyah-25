<?php

namespace App\Models;

use CodeIgniter\Model;

class GuruModel extends Model
{
    protected $table            = 'guru';
    protected $primaryKey       = 'id_guru';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'id_pengguna',
        'nip',
        'nama_lengkap',
        'jabatan',
        'no_hp',
        'face_descriptor',
        'foto_wajah',
    ];

    /**
     * Ambil data guru lengkap dengan info akun pengguna.
     */
    public function getAllWithUser(?string $search = null): array
    {
        $builder = $this->db->table('guru g')
            ->select('g.*, p.username, p.role, p.status_aktif')
            ->join('pengguna p', 'p.id = g.id_pengguna', 'left');

        if ($search !== null && trim($search) !== '') {
            $q = trim($search);
            $builder->groupStart()
                ->like('g.nama_lengkap', $q)
                ->orLike('g.nip', $q)
                ->orLike('g.jabatan', $q)
                ->groupEnd();
        }

        return $builder->orderBy('g.nama_lengkap', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Ambil data guru berdasarkan id_pengguna.
     */
    public function findByPenggunaId(int $idPengguna): ?array
    {
        return $this->where('id_pengguna', $idPengguna)->first();
    }

    /**
     * Ambil data guru beserta username dan status aktif.
     */
    public function findWithUser(int $idGuru): ?array
    {
        $row = $this->db->table('guru g')
            ->select('g.*, p.username, p.role, p.status_aktif')
            ->join('pengguna p', 'p.id = g.id_pengguna', 'left')
            ->where('g.id_guru', $idGuru)
            ->get()
            ->getRowArray();

        return $row ?: null;
    }
}
