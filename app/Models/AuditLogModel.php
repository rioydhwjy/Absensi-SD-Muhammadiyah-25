<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table            = 'audit_log';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $allowedFields    = [
        'id_pengguna',
        'aksi',
        'modul',
        'detail_perubahan',
        'ip_address',
        'waktu',
    ];

    public function getLatestWithUser(int $limit = 50): array
    {
        return $this->db->table('audit_log al')
            ->select('al.*, p.username, p.role')
            ->join('pengguna p', 'p.id = al.id_pengguna', 'left')
            ->orderBy('al.waktu', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
}
