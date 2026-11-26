<?= $this->extend('layouts/mobile') ?>

<?= $this->section('content') ?>
<?php
$statusHariIni = $absenHariIni['status'] ?? 'Belum absen';
$jamMasukText  = ! empty($absenHariIni['waktu_masuk']) ? substr($absenHariIni['waktu_masuk'], 0, 5) . ' WIB' : '--:--';
$jamPulangText = substr($aturan['jam_pulang'] ?? '14:00:00', 0, 5) . ' WIB';
$batasMasuk    = $isPiketPagi
    ? substr($aturan['jam_masuk_piket'] ?? '06:45:00', 0, 5)
    : substr($aturan['jam_masuk_normal'] ?? '07:00:00', 0, 5);
?>
<header class="sdm-header">
    <div class="sdm-header-row">
        <div>
            <div class="sdm-eyebrow"><?= esc(sapaan_waktu()) ?></div>
            <h1 class="sdm-header-title"><?= esc($guru['nama_lengkap']) ?></h1>
        </div>
        <a href="<?= base_url('guru/profil') ?>" class="sdm-icon-btn" title="Profil &amp; Keluar">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="8" r="4"/><path d="M5 20a7 7 0 0 1 14 0"/>
            </svg>
        </a>
    </div>
    <div class="sdm-header-meta">
        <span class="sdm-date-chip">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>
            </svg>
            <?= esc($tanggalLabel) ?>
        </span>
        <?php if ($isPiketPagi): ?>
            <span class="sdm-piket-chip">Piket pagi hari ini</span>
        <?php endif; ?>
    </div>
</header>

<main class="sdm-content">
    <?= view('partials/flash') ?>

    <div class="sdm-card">
        <div class="sdm-card-header">
            <h2 class="sdm-card-title">Absensi hari ini</h2>
            <span class="sdm-pill <?= esc(badge_status_class($statusHariIni)) ?>"><?= esc($statusHariIni) ?></span>
        </div>

        <div class="sdm-soft-green-box">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0; margin-top:1px;">
                <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>
            </svg>
            <div>
                <?php if ($isPiketPagi): ?>
                    <strong>Piket pagi:</strong> Masuk maksimal <?= esc($batasMasuk) ?> WIB (Normal <?= esc(substr($aturan['jam_masuk_normal'], 0, 5)) ?> WIB)
                <?php else: ?>
                    <strong>Jadwal normal:</strong> Masuk maksimal <?= esc($batasMasuk) ?> WIB (Piket <?= esc(substr($aturan['jam_masuk_piket'], 0, 5)) ?> WIB)
                <?php endif; ?>
            </div>
        </div>

        <div class="sdm-time-grid">
            <div class="sdm-time-box">
                <div class="sdm-time-label">Jam masuk</div>
                <div class="sdm-time-val"><?= esc($jamMasukText) ?></div>
            </div>
            <div class="sdm-time-box">
                <div class="sdm-time-label">Jam pulang</div>
                <div class="sdm-time-val"><?= esc($absenHariIni ? $jamPulangText : '--:--') ?></div>
            </div>
        </div>

        <?php if (! $absenHariIni): ?>
            <a href="<?= base_url('guru/cek-lokasi') ?>" class="sdm-btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 21s7-6.2 7-11.5A7 7 0 0 0 5 9.5C5 14.8 12 21 12 21Z"/><circle cx="12" cy="9.5" r="2.5"/>
                </svg>
                Absen masuk sekarang
            </a>
        <?php else: ?>
            <div class="sdm-btn-soft-green" style="cursor: default;">
                &#10003; Absensi hari ini sudah tercatat
            </div>
        <?php endif; ?>
    </div>

    <div class="sdm-section-label">Rekap bulan ini</div>
    <div class="sdm-stat-grid">
        <div class="sdm-stat-box sdm-stat-green">
            <div class="sdm-stat-num"><?= (int) $rekapBulan['Hadir'] ?></div>
            <div class="sdm-stat-lbl">Hadir</div>
        </div>
        <div class="sdm-stat-box sdm-stat-blue">
            <div class="sdm-stat-num"><?= (int) ($rekapBulan['Izin'] + $rekapBulan['Sakit']) ?></div>
            <div class="sdm-stat-lbl">Izin/Sakit</div>
        </div>
        <div class="sdm-stat-box sdm-stat-red">
            <div class="sdm-stat-num"><?= (int) $rekapBulan['Alpa'] ?></div>
            <div class="sdm-stat-lbl">Alpa</div>
        </div>
        <div class="sdm-stat-box sdm-stat-amber">
            <div class="sdm-stat-num"><?= (int) $rekapBulan['Terlambat'] ?></div>
            <div class="sdm-stat-lbl">Terlambat</div>
        </div>
    </div>

    <div class="sdm-warning-box">
        <div class="sdm-warning-box-title">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/><path d="M12 9v4M12 17h.01"/>
            </svg>
            Pengingat
        </div>
        <div>
            Absensi masuk hanya dapat dilakukan di dalam radius <?= (int) $aturan['radius_meter'] ?> meter dari SD Muhammadiyah 25 Medan menggunakan verifikasi GPS dan wajah.
        </div>
    </div>
</main>

<?= view('partials/nav_guru', ['activeNav' => $activeNav]) ?>
<?= $this->endSection() ?>
