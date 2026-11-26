<?php

namespace App\Libraries;

class KMeans
{
    /**
     * Fungsi untuk menghitung K-Means
     * @param array $data       Data dari database (Array of Objects/Arrays)
     * @param int $k            Jumlah cluster yang diinginkan
     * @param int $maxIterations Batas perulangan
     */
    public function calculate(array $data, $k = 3, $maxIterations = 10)
    {
        // 1. Inisialisasi Centroid secara acak dari data yang ada
        $centroids = [];
        $keys = array_rand($data, $k);
        foreach ($keys as $key) {
            $centroids[] = [
                'harga' => $data[$key]['harga'],
                'skor'  => $data[$key]['skor_performa']
            ];
        }

        $clusters = [];

        for ($iteration = 0; $iteration < $maxIterations; $iteration++) {
            $clusters = array_fill(0, $k, []);

            // 2. Tahap Assignment: Masukkan data ke cluster terdekat
            foreach ($data as $item) {
                $distances = [];
                foreach ($centroids as $index => $centroid) {
                    // Rumus Euclidean Distance: sqrt((x2-x1)^2 + (y2-y1)^2)
                    $dist = sqrt(
                        pow($item['harga'] - $centroid['harga'], 2) + 
                        pow($item['skor_performa'] - $centroid['skor'], 2)
                    );
                    $distances[$index] = $dist;
                }
                // Cari index cluster dengan jarak terkecil
                $closestCluster = array_keys($distances, min($distances))[0];
                $clusters[$closestCluster][] = $item;
            }

            // 3. Update Centroid: Hitung rata-rata posisi baru
            $newCentroids = [];
            foreach ($clusters as $index => $clusterItems) {
                if (count($clusterItems) == 0) {
                    $newCentroids[$index] = $centroids[$index]; // Tetap jika kosong
                    continue;
                }

                $avgHarga = array_sum(array_column($clusterItems, 'harga')) / count($clusterItems);
                $avgSkor = array_sum(array_column($clusterItems, 'skor_performa')) / count($clusterItems);
                
                $newCentroids[$index] = ['harga' => $avgHarga, 'skor' => $avgSkor];
            }

            // Cek jika centroid tidak berubah lagi (konvergen)
            if ($newCentroids === $centroids) break;
            $centroids = $newCentroids;
        }

        return [
            'centroids' => $centroids,
            'clusters'  => $clusters
        ];
    }
}