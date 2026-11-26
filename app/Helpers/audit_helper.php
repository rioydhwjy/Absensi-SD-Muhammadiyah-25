<?php

/**
 * ============================================================
 * Audit Log Helper
 * Mencatat aksi penting ke tabel audit_log
 * ============================================================
 */

if (!function_exists('catat_audit')) {
    /**
     * Catat audit log ke database
     *
     * @param string      $aksi             Aksi yang dilakukan
     * @param string      $modul            Modul yang terkait
     * @param string|null $detailPerubahan   Detail perubahan (opsional)
     * @param int|null    $idPengguna       ID pengguna (null = ambil dari session)
     * @return void
     */
    function catat_audit(
        string $aksi,
        string $modul,
        ?string $detailPerubahan = null,
        ?int $idPengguna = null
    ): void {
        $db = \Config\Database::connect();
        $request = \Config\Services::request();
        $session = session();

        $db->table('audit_log')->insert([
            'id_pengguna'      => $idPengguna ?? ($session->get('id_pengguna') ?? null),
            'aksi'             => $aksi,
            'modul'            => $modul,
            'detail_perubahan' => $detailPerubahan,
            'ip_address'       => $request->getIPAddress(),
            'waktu'            => date('Y-m-d H:i:s'),
        ]);
    }
}
