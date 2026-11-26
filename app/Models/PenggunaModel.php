<?php

namespace App\Models;

use CodeIgniter\Model;

class PenggunaModel extends Model
{
    protected $table            = 'pengguna';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'username',
        'password',
        'role',
        'status_aktif',
    ];

    /**
     * Cari pengguna berdasarkan username atau NIP guru.
     */
    public function findByUsernameOrNip(string $identifier): ?array
    {
        $user = $this->where('username', $identifier)->first();
        if ($user) {
            return $user;
        }

        $row = $this->db->table('pengguna p')
            ->select('p.*')
            ->join('guru g', 'g.id_pengguna = p.id', 'inner')
            ->where('g.nip', $identifier)
            ->get()
            ->getRowArray();

        return $row ?: null;
    }
}
