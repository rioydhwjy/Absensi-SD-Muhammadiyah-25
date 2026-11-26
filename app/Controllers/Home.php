<?php

namespace App\Controllers;

use App\Models\SmartphoneModel;
use App\Libraries\KMeans;

class Home extends BaseController
{
    public function hasil()
    {
        $budgetInput = $this->request->getPost('budget');
        $prioritas = $this->request->getPost('prioritas');

        $model = new SmartphoneModel();
        $semuaData = $model->findAll(); 

        $kmeans = new KMeans();
        $hasilCluster = $kmeans->calculate($semuaData, 3);

        $clusterTerpilih = null;
        $selisihTerkecil = doubleval(PHP_INT_MAX);

        foreach ($hasilCluster['centroids'] as $index => $centroid) {
            $selisih = abs($centroid['harga'] - $budgetInput);
            if ($selisih < $selisihTerkecil) {
                $selisihTerkecil = $selisih;
                $clusterTerpilih = $hasilCluster['clusters'][$index];
            }
        }

        $rekomendasiFinal = $this->forwardChaining($clusterTerpilih, $prioritas);

        return view('home', [
            'hasil_rekomendasi' => $rekomendasiFinal,
            'budget' => $budgetInput,
            'prioritas' => $prioritas
        ]);
    }

    private function forwardChaining($daftarHp, $prioritas)
    {

        foreach ($daftarHp as $hp) {
            if ($prioritas == 'fotografi' && $hp['skor_kamera'] >= 80) return $hp;
            if ($prioritas == 'performa' && $hp['skor_performa'] >= 85) return $hp;
            // ... dst
        }
        return $daftarHp[0];
    }
}