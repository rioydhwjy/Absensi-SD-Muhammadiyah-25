<?php

namespace App\Models;

use CodeIgniter\Model;

class SalesModel extends Model
{
    protected $table            = 'sales';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useTimestamps   = true;
    protected $dateFormat      = 'datetime';

    protected $allowedFields    = [
        'order_id', 'total_qty', 'total_weight_gr', 'total_returned_qty',
        'total_diskon', 'product_categories', 'num_product_categories',
        'status_pesanan', 'alasan_pembatalan', 'opsi_pengiriman',
        'metode_pembayaran', 'kota_kabupaten', 'provinsi',
        'ongkir_pembeli', 'potongan_ongkir', 'total_pembayaran',
        'perkiraan_ongkir', 'waktu_pesanan_dibuat'
    ];
}