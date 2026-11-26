<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class Sales extends ResourceController
{
    protected $modelName = 'App\Models\SalesModel';
    protected $format    = 'json';

    // 1. GET /api/sales/summary (Ringkasan Statistik)
    public function summary()
    {
        $builder = $this->model->builder();
        
        $totalOmset = $builder->where('status_pesanan', 'Selesai')
                              ->selectSum('total_pembayaran')
                              ->get()->getRow()->total_pembayaran;

        $totalOrders = $this->model->countAllResults();

        return $this->respond([
            'status'  => 200,
            'summary' => [
                'total_transaksi'     => $totalOrders,
                'total_omset_selesai' => (float) $totalOmset,
            ],
        ]);
    }

    // 2. GET /api/sales (Daftar Transaksi)
    public function index()
    {
        $provinsi = $this->request->getGet('provinsi');
        $status   = $this->request->getGet('status');

        if ($provinsi) {
            $this->model->where('provinsi', $provinsi);
        }

        if ($status) {
            $this->model->where('status_pesanan', $status);
        }

        $page    = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 20;

        $data = $this->model->orderBy('id', 'ASC')->paginate($perPage, 'default', $page);

        return $this->respond([
            'status'  => 200,
            'message' => 'Daftar transaksi e-commerce',
            'page'    => $page,
            'total'   => $this->model->pager->getTotal(),
            'data'    => $data,
        ]);
    }

    // 3. GET /api/sales/{id} (Detail Transaksi Specific)
    public function show($id = null)
    {
        $data = $this->model->find($id);
        if (!$data) {
            return $this->failNotFound("Data transaksi dengan ID {$id} tidak ditemukan.");
        }

        return $this->respond([
            'status'  => 200,
            'message' => 'Detail transaksi',
            'data'    => $data,
        ]);
    }

    // 4. POST /api/sales (Tambah Transaksi Baru)
    public function create()
    {
        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        if (empty($data)) {
            return $this->fail("Tidak ada data yang dikirim.");
        }

        if (!$this->model->insert($data)) {
            return $this->fail($this->model->errors());
        }

        return $this->respondCreated([
            'status'  => 201,
            'message' => 'Transaksi berhasil ditambahkan',
            'id'      => $this->model->getInsertID(),
        ]);
    }

    // 5. PUT /api/sales/{id} (Update Data Transaksi)
    public function update($id = null)
    {
        $data = $this->request->getJSON(true) ?? $this->request->getRawInput();

        if (!$this->model->find($id)) {
            return $this->failNotFound("Data transaksi dengan ID {$id} tidak ditemukan.");
        }

        if (empty($data)) {
            return $this->fail("Tidak ada data yang dikirim untuk diperbarui.");
        }

        if (!$this->model->update($id, $data)) {
            return $this->fail($this->model->errors());
        }

        return $this->respond([
            'status'  => 200,
            'message' => "Data transaksi ID {$id} berhasil diperbarui",
        ]);
    }

    // 6. DELETE /api/sales/{id} (Hapus Transaksi)
    public function delete($id = null)
    {
        if (!$this->model->find($id)) {
            return $this->failNotFound("Data transaksi dengan ID {$id} tidak ditemukan.");
        }

        $this->model->delete($id);

        return $this->respondDeleted([
            'status'  => 200,
            'message' => "Data transaksi ID {$id} berhasil dihapus",
        ]);
    }
}