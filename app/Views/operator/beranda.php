<?= $this->extend('layouts/mobile') ?>

<?= $this->section('content') ?>
<header class="sdm-header">
    <div class="sdm-header-row">
        <div>
            <div class="sdm-eyebrow"><?= esc($roleLabel) ?> &bull; <?= esc($tanggalLabel) ?></div>
            <h1 class="sdm-header-title">Ringkasan hari ini</h1>
        </div>
        <a href="<?= base_url('operator/pengaturan') ?>" class="sdm-icon-btn" title="Pengaturan Aturan &amp; Akun">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9c.26.6.79 1 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1Z"/>
            </svg>
        </a>
    </div>
</header>

<main class="sdm-content">
    <?= view('partials/flash') ?>

    <div class="sdm-stat-grid">
        <div class="sdm-stat-box sdm-stat-green">
            <div class="sdm-stat-num"><?= (int) $stats['sudah_absen'] ?></div>
            <div class="sdm-stat-lbl">Sudah absen</div>
        </div>
        <div class="sdm-stat-box sdm-stat-amber">
            <div class="sdm-stat-num"><?= (int) $stats['belum'] ?></div>
            <div class="sdm-stat-lbl">Belum</div>
        </div>
        <div class="sdm-stat-box sdm-stat-red">
            <div class="sdm-stat-num"><?= (int) $stats['terlambat'] ?></div>
            <div class="sdm-stat-lbl">Terlambat</div>
        </div>
        <div class="sdm-stat-box sdm-stat-blue">
            <div class="sdm-stat-num"><?= (int) $stats['izin'] ?></div>
            <div class="sdm-stat-lbl">Izin</div>
        </div>
    </div>

    <a href="<?= base_url('operator/persetujuan') ?>" class="sdm-card" style="display:flex; align-items:center; justify-content:space-between; gap:10px;">
        <div>
            <div style="font-size:13.5px; font-weight:700;"><?= (int) $pendingCount ?> menunggu persetujuan</div>
            <div style="font-size:11.5px; color:var(--sdm-muted); margin-top:2px;">Izin, sakit, &amp; koreksi absen</div>
        </div>
        <div style="display:flex; align-items:center; gap:8px;">
            <span class="sdm-pill sdm-pill-amber">Tinjau</span>
            <span style="color:var(--sdm-muted); font-weight:700;">&rsaquo;</span>
        </div>
    </a>

    <div class="sdm-card">
        <div class="sdm-card-header">
            <h2 class="sdm-card-title">Status guru hari ini</h2>
            <span style="font-size:11.5px; color:var(--sdm-muted);"><?= count($guruStatuses) ?> guru</span>
        </div>

        <?php if (empty($guruStatuses)): ?>
            <p style="font-size:12.5px; color:var(--sdm-muted); margin:8px 0;">Belum ada data guru terdaftar.</p>
        <?php else: ?>
            <?php foreach ($guruStatuses as $row): ?>
                <?php
                $st = $row['status'] ?: 'Belum absen';
                $jam = ! empty($row['waktu_masuk']) ? substr($row['waktu_masuk'], 0, 5) . ' WIB' : '-';
                ?>
                <div class="sdm-list-item">
                    <div class="sdm-list-left">
                        <div class="sdm-avatar"><?= esc(inisial_nama($row['nama_lengkap'])) ?></div>
                        <div style="min-width:0;">
                            <div class="sdm-item-title"><?= esc($row['nama_lengkap']) ?></div>
                            <div class="sdm-item-sub"><?= esc($jam) ?></div>
                        </div>
                    </div>
                    <span class="sdm-pill <?= esc(badge_status_class($st)) ?>"><?= esc($st) ?></span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<?= view('partials/nav_operator', ['activeNav' => $activeNav]) ?>
<?= $this->endSection() ?>
