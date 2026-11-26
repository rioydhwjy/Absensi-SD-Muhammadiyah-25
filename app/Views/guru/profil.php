<?= $this->extend('layouts/mobile') ?>

<?= $this->section('content') ?>
<?php $wajahOk = ! empty($guru['face_descriptor']); ?>
<header class="sdm-header">
    <div class="sdm-header-row">
        <div>
            <div class="sdm-eyebrow">Akun Guru</div>
            <h1 class="sdm-header-title">Profil saya</h1>
        </div>
        <a href="<?= base_url('logout') ?>" class="sdm-icon-btn" title="Keluar">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5M21 12H9"/>
            </svg>
        </a>
    </div>
</header>

<main class="sdm-content">
    <?= view('partials/flash') ?>

    <div class="sdm-card">
        <div class="sdm-list-left" style="margin-bottom: 14px;">
            <div class="sdm-avatar" style="width:46px; height:46px; font-size:15px;">
                <?= esc(inisial_nama($guru['nama_lengkap'])) ?>
            </div>
            <div>
                <div style="font-size:15px; font-weight:700;"><?= esc($guru['nama_lengkap']) ?></div>
                <div style="font-size:12px; color:var(--sdm-muted);">NIP: <?= esc($guru['nip'] ?: '-') ?> &bull; <?= esc($guru['jabatan'] ?: 'Guru') ?></div>
            </div>
        </div>

        <div class="sdm-list-item">
            <span style="font-size:12.5px; color:var(--sdm-muted);">Nama pengguna</span>
            <strong style="font-size:12.5px;"><?= esc($guru['username'] ?? '-') ?></strong>
        </div>
        <div class="sdm-list-item">
            <span style="font-size:12.5px; color:var(--sdm-muted);">Nomor HP</span>
            <strong style="font-size:12.5px;"><?= esc($guru['no_hp'] ?: '-') ?></strong>
        </div>
        <div class="sdm-list-item">
            <span style="font-size:12.5px; color:var(--sdm-muted);">Biometrik wajah</span>
            <span class="sdm-pill <?= $wajahOk ? 'sdm-pill-green' : 'sdm-pill-amber' ?>">
                <?= $wajahOk ? 'Wajah terdaftar' : 'Belum daftar wajah' ?>
            </span>
        </div>

        <a href="<?= base_url('guru/daftar-wajah') ?>" class="sdm-btn-soft-green" style="margin-top: 12px;">
            <?= $wajahOk ? 'Perbarui data wajah' : 'Daftarkan wajah sekarang' ?>
        </a>
    </div>

    <div class="sdm-card">
        <h2 class="sdm-card-title" style="margin-bottom: 10px;">Jadwal piket saya</h2>
        <?php if (empty($piketSaya)): ?>
            <p style="font-size:12.5px; color:var(--sdm-muted); margin:4px 0;">Belum ada jadwal piket terdaftar.</p>
        <?php else: ?>
            <?php foreach ($piketSaya as $pk): ?>
                <div class="sdm-list-item">
                    <span style="font-size:13px; font-weight:600;"><?= esc($pk['hari']) ?></span>
                    <span class="sdm-pill <?= $pk['jenis_piket'] === 'piket_pagi' ? 'sdm-pill-green' : 'sdm-pill-blue' ?>">
                        <?= $pk['jenis_piket'] === 'piket_pagi' ? 'Piket pagi' : 'Piket istirahat' ?>
                    </span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="sdm-card">
        <h2 class="sdm-card-title" style="margin-bottom: 12px;">Ganti kata sandi</h2>
        <form action="<?= base_url('guru/ganti-password') ?>" method="post">
            <?= csrf_field() ?>
            <div class="sdm-form-group">
                <label for="password_lama" class="sdm-label">Kata sandi lama</label>
                <input type="password" id="password_lama" name="password_lama" class="sdm-input" required>
            </div>
            <div class="sdm-form-group">
                <label for="password_baru" class="sdm-label">Kata sandi baru</label>
                <input type="password" id="password_baru" name="password_baru" class="sdm-input" minlength="6" required>
            </div>
            <div class="sdm-form-group">
                <label for="konfirmasi_password" class="sdm-label">Konfirmasi kata sandi baru</label>
                <input type="password" id="konfirmasi_password" name="konfirmasi_password" class="sdm-input" minlength="6" required>
            </div>
            <button type="submit" class="sdm-btn-primary">Simpan kata sandi</button>
        </form>
    </div>

    <a href="<?= base_url('logout') ?>" class="sdm-btn-soft-red">Keluar dari aplikasi</a>
</main>

<?= view('partials/nav_guru', ['activeNav' => $activeNav]) ?>
<?= $this->endSection() ?>
