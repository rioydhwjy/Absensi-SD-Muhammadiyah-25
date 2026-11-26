<?= $this->extend('layouts/mobile') ?>

<?= $this->section('content') ?>
<header class="sdm-header">
    <div class="sdm-header-row">
        <div>
            <div class="sdm-eyebrow">Kehadiran</div>
            <h1 class="sdm-header-title">Riwayat dan izin</h1>
        </div>
        <a href="<?= base_url('guru/profil') ?>" class="sdm-icon-btn" title="Profil">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="8" r="4"/><path d="M5 20a7 7 0 0 1 14 0"/>
            </svg>
        </a>
    </div>
</header>

<main class="sdm-content">
    <?= view('partials/flash') ?>

    <div class="sdm-segment">
        <button type="button" class="sdm-segment-btn <?= $activeTab === 'riwayat' ? 'active' : '' ?>" data-panel="panel-riwayat">Riwayat</button>
        <button type="button" class="sdm-segment-btn <?= $activeTab === 'izin' ? 'active' : '' ?>" data-panel="panel-izin">Izin / Sakit</button>
    </div>

    <!-- Panel 5: Riwayat Kehadiran -->
    <section id="panel-riwayat" style="<?= $activeTab === 'riwayat' ? '' : 'display:none;' ?>">
        <div class="sdm-card">
            <?php if (empty($riwayat)): ?>
                <p style="text-align:center; font-size:13px; color:var(--sdm-muted); margin:16px 0;">
                    Belum ada riwayat kehadiran yang tercatat.
                </p>
            <?php else: ?>
                <?php foreach ($riwayat as $row): ?>
                    <?php
                    $jamText = ! empty($row['waktu_masuk']) ? 'Masuk ' . substr($row['waktu_masuk'], 0, 5) : 'Tanpa jam masuk';
                    $jarakText = isset($row['jarak_meter']) ? ' • ' . round((float) $row['jarak_meter']) . ' m' : '';
                    $wajahText = isset($row['skor_wajah']) ? ' • Wajah cocok' : '';
                    ?>
                    <div class="sdm-list-item">
                        <div style="min-width: 0;">
                            <div class="sdm-item-title"><?= esc(format_tanggal_pendek($row['tanggal'])) ?></div>
                            <div class="sdm-item-sub"><?= esc($jamText . $jarakText . $wajahText) ?></div>
                        </div>
                        <span class="sdm-pill <?= esc(badge_status_class($row['status'])) ?>"><?= esc($row['status']) ?></span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Panel 5b: Pengajuan Izin / Sakit -->
    <section id="panel-izin" style="<?= $activeTab === 'izin' ? '' : 'display:none;' ?>">
        <div class="sdm-card">
            <h2 class="sdm-card-title" style="margin-bottom: 12px;">Ajukan izin / sakit</h2>
            <form action="<?= base_url('guru/izin') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="jenis" id="jenis_izin" value="Izin">

                <div class="sdm-segment" style="margin-bottom: 14px;">
                    <button type="button" class="sdm-segment-btn active" data-jenis="Izin">Izin</button>
                    <button type="button" class="sdm-segment-btn" data-jenis="Sakit">Sakit</button>
                </div>

                <div class="sdm-time-grid" style="margin-bottom: 14px;">
                    <div>
                        <label for="tanggal_mulai" class="sdm-label">Tanggal mulai</label>
                        <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="sdm-input" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div>
                        <label for="tanggal_selesai" class="sdm-label">Sampai tanggal</label>
                        <input type="date" id="tanggal_selesai" name="tanggal_selesai" class="sdm-input" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>

                <div class="sdm-form-group">
                    <label for="alasan" class="sdm-label">Keterangan</label>
                    <textarea id="alasan" name="alasan" rows="3" class="sdm-textarea" placeholder="Tulis alasan singkat..." required></textarea>
                </div>

                <div class="sdm-form-group">
                    <label for="lampiran" class="sdm-label">Lampiran surat (opsional, JPG/PNG/PDF maks 2 MB)</label>
                    <input type="file" id="lampiran" name="lampiran" class="sdm-input" accept=".jpg,.jpeg,.png,.pdf">
                </div>

                <button type="submit" class="sdm-btn-primary">Kirim pengajuan</button>
            </form>
        </div>

        <div class="sdm-card">
            <h2 class="sdm-card-title" style="margin-bottom: 10px;">Pengajuan saya</h2>
            <?php if (empty($pengajuan)): ?>
                <p style="font-size:12.5px; color:var(--sdm-muted); margin:6px 0;">Belum ada pengajuan izin atau sakit.</p>
            <?php else: ?>
                <?php foreach ($pengajuan as $p): ?>
                    <div class="sdm-list-item">
                        <div style="min-width:0;">
                            <div class="sdm-item-title">
                                <?= esc($p['jenis']) ?> &bull; <?= esc(format_tanggal_pendek($p['tanggal_mulai'])) ?>
                            </div>
                            <div class="sdm-item-sub"><?= esc($p['alasan']) ?></div>
                        </div>
                        <span class="sdm-pill <?= esc(badge_status_class($p['status_persetujuan'])) ?>">
                            <?= esc($p['status_persetujuan']) ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</main>

<?= view('partials/nav_guru', ['activeNav' => $activeNav]) ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.querySelectorAll('.sdm-segment-btn[data-panel]').forEach((btn) => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.sdm-segment-btn[data-panel]').forEach((b) => b.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('panel-riwayat').style.display = btn.dataset.panel === 'panel-riwayat' ? '' : 'none';
        document.getElementById('panel-izin').style.display    = btn.dataset.panel === 'panel-izin' ? '' : 'none';
    });
});

document.querySelectorAll('.sdm-segment-btn[data-jenis]').forEach((btn) => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.sdm-segment-btn[data-jenis]').forEach((b) => b.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('jenis_izin').value = btn.dataset.jenis;
    });
});
</script>
<?= $this->endSection() ?>
