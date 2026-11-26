<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pakar Rekomendasi Smartphone</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #0a0e1a;
            color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 80px;
        }
        .navbar {
            background-color: rgba(10, 14, 26, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #1e293b;
        }
        .hero-section {
            padding: 40px 0 30px 0;
        }
        .card-custom {
            background-color: #121829;
            border: 1px solid #1e293b;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }
        .form-control, .form-select {
            background-color: #1a2238;
            border: 1px solid #2d3748;
            color: #fff;
        }
        .form-control:focus, .form-select:focus {
            background-color: #1a2238;
            border-color: #3b82f6;
            color: #fff;
            box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
        }
        .btn-tech {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            border: none;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-tech:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.4);
            color: white;
        }
        .result-card {
            background-color: #121829;
            border-left: 4px solid #3b82f6;
            border-radius: 8px;
            margin-top: 2rem;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="#">📱 AI Smartphone Recommender</a>
        </div>
    </nav>

    <header class="hero-section text-center">
        <div class="container">
            <h1 class="display-5 fw-bold mb-2">Temukan HP Idamanmu dengan <span class="text-primary">Kecerdasan Buatan</span></h1>
            <p class="lead text-secondary">Masukkan budget dan preferensi Anda, biarkan sistem yang menemukan yang terbaik.</p>
        </div>
    </header>

    <main class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                
                <div class="card card-custom p-4">
                    <h5 class="mb-4 text-center text-light">Formulir Analisis Kebutuhan</h5>
                    
                    <form action="<?= base_url('home/hasil'); ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="budget" class="form-label">Masukkan Budget Maksimal Anda (Rp)</label>
                        <input type="number" class="form-control" id="budget" name="budget" required>
                    </div>

                    <div class="mb-3">
                        <label for="prioritas" class="form-label">Apa Prioritas Utama Anda?</label>
                        <select class="form-select" id="prioritas" name="prioritas" required>
                            <option value="" selected disabled>Pilih Kategori Prioritas</option>
                            <option value="seimbang">Seimbang (Pemakaian Sehari-hari)</option>
                            <option value="fotografi">Fotografi (Kamera Bagus)</option>
                            <option value="cas_cepat">Cas Cepat (Mobilitas Tinggi)</option>
                            <option value="performa">Performa Super Berat (Sultan/Gaming)</option>
                        </select>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-tech btn-lg py-2">Cari Rekomendasi</button>
                    </div>
                </form>
                </div>

                <div class="card card-custom mb-3">
                <div class="card-body">
                    <h5 class="text-primary"><?= $nama_hp ?></h5>
                    <div class="row mt-3">
                        <div class="col-6">
                            <small class="text-secondary">Performa</small>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar bg-info" style="width: 85%"></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <small class="text-secondary">Kamera</small>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar bg-success" style="width: 90%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                <?php if (isset($hasil_rekomendasi)): ?>
                    <div class="result-card p-4 shadow-sm">
                        <h6 class="text-primary mb-3">Hasil Rekomendasi AI:</h6>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="fw-bold mb-0 text-light"><?= $hasil_rekomendasi['nama_hp']; ?></h5>
                            <span class="badge bg-primary px-3 py-2 fs-6">Rp <?= number_format($hasil_rekomendasi['harga'], 0, ',', '.'); ?></span>
                        </div>
                        <p class="text-secondary small mb-3">Rekomendasi disesuaikan dengan budget Rp <?= number_format($budget, 0, ',', '.'); ?> dan prioritas: <?= ucfirst($prioritas); ?>.</p>
                        
                        <ul class="list-unstyled text-secondary small">
                            <li><strong>RAM:</strong> <?= $hasil_rekomendasi['ram']; ?></li>
                            <li><strong>Memori:</strong> <?= $hasil_rekomendasi['memori']; ?></li>
                            <li><strong>Spesifikasi Utama:</strong> <?= $hasil_rekomendasi['spek_utama']; ?></li>
                        </ul>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>