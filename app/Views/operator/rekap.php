<?= $this->extend('layouts/mobile') ?>

<?= $this->section('content') ?>
<header class="sdm-header">
    <div class="sdm-header-row">
        <div>
            <div class="sdm-eyebrow">Operator</div>
            <h1 class="sdm-header-title">Rekap dan laporan</h1>
        </div>
    </div>
</header>

<main class="sdm-content">
    <div class="sdm-card">
        <div class="sdm-card-header">
            <div style="display:flex; align-items:center; gap:10px;">
                <a href="<?= base_url("operator/rekap?bulan={$prevMonth}&tahun={$prevYear}") ?>" style="font-weight:700; color:var(--sdm-muted); padding:2px 6px;">&lsaquo;</a>
                <strong style="font-size:13.5px;"><?= esc($bulanLabel) ?></strong>
                <a href="<?= base_url("operator/rekap?bulan={$nextMonth}&tahun={$nextYear}") ?>" style="font-weight:700; color:var(--sdm-muted); padding:2px 6px;">&rsaquo;</a>
            </div>
            <span class="sdm-pill sdm-pill-green">Kehadiran <?= (int) $overallPct ?>%</span>
        </div>

        <div class="sdm-chart-bars">
            <?php foreach ($chart as $w): ?>
                <?php $heightPct = max(8, (int) $w['pct']); ?>
                <div class="sdm-chart-col">
                    <div class="sdm-chart-pct"><?= (int) $w['pct'] ?>%</div>
                    <div class="sdm-chart-bar" style="height: <?= $heightPct ?>%;"></div>
                    <div class="sdm-chart-lbl"><?= esc($w['label']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="sdm-time-grid" style="margin-bottom: 14px;">
        <a href="<?= base_url("operator/rekap/export-pdf?bulan={$month}&tahun={$year}") ?>" class="sdm-btn-soft-green">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5"/><path d="M12 15V3"/>
            </svg>
            PDF
        </a>
        <a href="<?= base_url("operator/rekap/export-excel?bulan={$month}&tahun={$year}") ?>" class="sdm-btn-soft-green">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m17 8-5-5-5 5"/><path d="M12 3v12"/>
            </svg>
            Excel
        </a>
    </div>

    <div class="sdm-card">
        <table class="sdm-recap-table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th class="num">H</th>
                    <th class="num">T</th>
                    <th class="num">I</th>
                    <th class="num">S/A</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rekap)): ?>
                    <tr><td colspan="5" style="text-align:center; color:var(--sdm-muted);">Belum ada data guru.</td></tr>
                <?php else: ?>
                    <?php foreach ($rekap as $r): ?>
                        <tr>
                            <td><?= esc($r['nama_lengkap']) ?></td>
                            <td class="num" style="color:var(--sdm-green);"><?= (int) $r['hadir'] ?></td>
                            <td class="num" style="color:var(--sdm-amber-text);"><?= (int) $r['terlambat'] ?></td>
                            <td class="num" style="color:var(--sdm-blue-text);"><?= (int) $r['izin'] ?></td>
                            <td class="num" style="color:var(--sdm-red-text);"><?= (int) $r['sakit_alpa'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?= view('partials/nav_operator', ['activeNav' => $activeNav]) ?>
<?= $this->endSection() ?>
