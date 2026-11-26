<?= $this->extend('layouts/mobile') ?>

<?= $this->section('content') ?>
<header class="sdm-header">
    <div class="sdm-header-row" style="justify-content:flex-start;">
        <a href="<?= base_url('operator/beranda') ?>" class="sdm-icon-btn" aria-label="Kembali">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="m15 18-6-6 6-6"/>
            </svg>
        </a>
        <div>
            <div class="sdm-eyebrow">Konfigurasi Sistem</div>
            <h1 class="sdm-header-title">Pengaturan absensi</h1>
        </div>
    </div>
</header>

<main class="sdm-content">
    <?= view('partials/flash') ?>

    <div class="sdm-card">
        <h2 class="sdm-card-title" style="margin-bottom:12px;">Jam kerja &amp; geofence sekolah</h2>
        <form action="<?= base_url('operator/pengaturan/simpan') ?>" method="post">
            <?= csrf_field() ?>

            <div class="sdm-time-grid">
                <div>
                    <label for="jam_masuk_piket" class="sdm-label">Jam masuk piket</label>
                    <input type="time" id="jam_masuk_piket" name="jam_masuk_piket" class="sdm-input" value="<?= esc(substr($aturan['jam_masuk_piket'], 0, 5)) ?>" required>
                </div>
                <div>
                    <label for="jam_masuk_normal" class="sdm-label">Jam masuk normal</label>
                    <input type="time" id="jam_masuk_normal" name="jam_masuk_normal" class="sdm-input" value="<?= esc(substr($aturan['jam_masuk_normal'], 0, 5)) ?>" required>
                </div>
            </div>

            <div class="sdm-time-grid">
                <div>
                    <label for="jam_pulang" class="sdm-label">Jam pulang</label>
                    <input type="time" id="jam_pulang" name="jam_pulang" class="sdm-input" value="<?= esc(substr($aturan['jam_pulang'], 0, 5)) ?>" required>
                </div>
                <div>
                    <label for="toleransi_menit" class="sdm-label">Toleransi (menit)</label>
                    <input type="number" id="toleransi_menit" name="toleransi_menit" class="sdm-input" min="0" value="<?= (int) $aturan['toleransi_menit'] ?>" required>
                </div>
            </div>

            <div class="sdm-time-grid">
                <div>
                    <label for="latitude_sekolah" class="sdm-label">Latitude sekolah</label>
                    <input type="text" id="latitude_sekolah" name="latitude_sekolah" class="sdm-input" value="<?= esc($aturan['latitude_sekolah']) ?>" required>
                </div>
                <div>
                    <label for="longitude_sekolah" class="sdm-label">Longitude sekolah</label>
                    <input type="text" id="longitude_sekolah" name="longitude_sekolah" class="sdm-input" value="<?= esc($aturan['longitude_sekolah']) ?>" required>
                </div>
            </div>

            <div class="sdm-form-group">
                <label for="radius_meter" class="sdm-label">Radius geofence (meter)</label>
                <input type="number" id="radius_meter" name="radius_meter" class="sdm-input" min="10" value="<?= (int) $aturan['radius_meter'] ?>" required>
            </div>

            <button type="submit" class="sdm-btn-primary">Simpan pengaturan</button>
        </form>
    </div>

    <div class="sdm-card">
        <h2 class="sdm-card-title" style="margin-bottom:10px;">Audit log terbaru</h2>
        <?php if (empty($auditLogs)): ?>
            <p style="font-size:12.5px; color:var(--sdm-muted); margin:4px 0;">Belum ada catatan aktivitas.</p>
        <?php else: ?>
            <?php foreach ($auditLogs as $log): ?>
                <div class="sdm-list-item">
                    <div style="min-width:0;">
                        <div class="sdm-item-title"><?= esc($log['aksi']) ?> &bull; <?= esc($log['modul']) ?></div>
                        <div class="sdm-item-sub"><?= esc($log['detail_perubahan'] ?: '-') ?></div>
                    </div>
                    <span style="font-size:10.5px; color:var(--sdm-muted); white-space:nowrap;">
                        <?= esc(substr($log['waktu'], 11, 5)) ?>
                    </span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <a href="<?= base_url('logout') ?>" class="sdm-btn-soft-red">Keluar dari aplikasi</a>
</main>

<?= view('partials/nav_operator', ['activeNav' => $activeNav]) ?>
<?= $this->endSection() ?>
