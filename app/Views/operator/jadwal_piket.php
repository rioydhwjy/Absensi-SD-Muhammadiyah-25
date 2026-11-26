<?= $this->extend('layouts/mobile') ?>

<?= $this->section('content') ?>
<?php
$shortHari = [
    'Senin'  => 'Sen',
    'Selasa' => 'Sel',
    'Rabu'   => 'Rab',
    'Kamis'  => 'Kam',
    'Jumat'  => 'Jum',
    'Sabtu'  => 'Sab',
];
$jamPiket  = substr($aturan['jam_masuk_piket'] ?? '06:45:00', 0, 5);
$jamNormal = substr($aturan['jam_masuk_normal'] ?? '07:00:00', 0, 5);
?>
<header class="sdm-header">
    <div class="sdm-header-row">
        <div>
            <div class="sdm-eyebrow">Operator</div>
            <h1 class="sdm-header-title">Jadwal piket</h1>
        </div>
        <button type="button" class="sdm-yellow-btn" onclick="App.openModal('modal-jadwal')">
            Ubah jadwal
        </button>
    </div>
</header>

<main class="sdm-content">
    <?= view('partials/flash') ?>

    <div class="sdm-day-pills">
        <?php foreach ($hariList as $h): ?>
            <a href="<?= base_url('operator/jadwal?hari=' . urlencode($h)) ?>" class="sdm-day-pill <?= $hariAktif === $h ? 'active' : '' ?>">
                <?= esc($shortHari[$h] ?? $h) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="sdm-card">
        <div class="sdm-card-header">
            <h2 class="sdm-card-title">Piket pagi</h2>
            <span class="sdm-pill sdm-pill-green">Datang <?= esc($jamPiket) ?></span>
        </div>

        <?php if (empty($piketPagi)): ?>
            <p style="font-size:12.5px; color:var(--sdm-muted); margin:6px 0;">Belum ada guru piket pagi hari <?= esc($hariAktif) ?>.</p>
        <?php else: ?>
            <?php foreach ($piketPagi as $row): ?>
                <div class="sdm-list-item">
                    <div class="sdm-list-left">
                        <div class="sdm-avatar"><?= esc(inisial_nama($row['nama_lengkap'])) ?></div>
                        <div class="sdm-item-title"><?= esc($row['nama_lengkap']) ?></div>
                    </div>
                    <form action="<?= base_url('operator/jadwal/hapus/' . $row['id_piket']) ?>" method="post" onsubmit="return confirm('Hapus jadwal piket ini?');">
                        <?= csrf_field() ?>
                        <button type="submit" title="Hapus" style="border:none; background:#FEECEC; color:#DC2626; width:26px; height:26px; border-radius:50%; cursor:pointer; font-weight:700;">&times;</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="sdm-card">
        <div class="sdm-card-header">
            <h2 class="sdm-card-title">Piket istirahat</h2>
            <span class="sdm-pill sdm-pill-blue">Siaga</span>
        </div>

        <?php if (empty($piketIstirahat)): ?>
            <p style="font-size:12.5px; color:var(--sdm-muted); margin:6px 0;">Belum ada guru piket istirahat hari <?= esc($hariAktif) ?>.</p>
        <?php else: ?>
            <?php foreach ($piketIstirahat as $row): ?>
                <div class="sdm-list-item">
                    <div class="sdm-list-left">
                        <div class="sdm-avatar"><?= esc(inisial_nama($row['nama_lengkap'])) ?></div>
                        <div class="sdm-item-title"><?= esc($row['nama_lengkap']) ?></div>
                    </div>
                    <form action="<?= base_url('operator/jadwal/hapus/' . $row['id_piket']) ?>" method="post" onsubmit="return confirm('Hapus jadwal piket ini?');">
                        <?= csrf_field() ?>
                        <button type="submit" title="Hapus" style="border:none; background:#FEECEC; color:#DC2626; width:26px; height:26px; border-radius:50%; cursor:pointer; font-weight:700;">&times;</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="sdm-warning-box">
        <div class="sdm-warning-box-title">Aturan piket</div>
        <ul style="margin:6px 0 0; padding-left:18px; line-height:1.55;">
            <li>Guru piket pagi masuk paling lambat <strong><?= esc($jamPiket) ?> WIB</strong> (15 menit lebih awal dari jam normal <?= esc($jamNormal) ?> WIB).</li>
            <li>Guru piket istirahat bertugas mengawasi ketertiban siswa saat jam istirahat.</li>
            <li>Sistem otomatis menyesuaikan batas keterlambatan berdasarkan jadwal piket hari berjalan.</li>
        </ul>
    </div>
</main>

<!-- Modal Tambah Jadwal Piket -->
<div id="modal-jadwal" class="sdm-modal-backdrop">
    <div class="sdm-modal-sheet">
        <div class="sdm-card-header">
            <h2 class="sdm-card-title">Tambah Jadwal Piket</h2>
            <button type="button" onclick="App.closeModal('modal-jadwal')" style="border:none; background:none; font-size:20px; cursor:pointer; color:var(--sdm-muted);">&times;</button>
        </div>

        <form action="<?= base_url('operator/jadwal/simpan') ?>" method="post">
            <?= csrf_field() ?>
            <div class="sdm-form-group">
                <label for="jp_hari" class="sdm-label">Hari</label>
                <select id="jp_hari" name="hari" class="sdm-select" required>
                    <?php foreach ($hariList as $h): ?>
                        <option value="<?= esc($h) ?>" <?= $hariAktif === $h ? 'selected' : '' ?>><?= esc($h) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="sdm-form-group">
                <label for="jp_jenis" class="sdm-label">Sesi piket</label>
                <select id="jp_jenis" name="jenis_piket" class="sdm-select" required>
                    <option value="piket_pagi">Piket pagi (Datang <?= esc($jamPiket) ?>)</option>
                    <option value="piket_istirahat">Piket istirahat</option>
                </select>
            </div>
            <div class="sdm-form-group">
                <label for="jp_guru" class="sdm-label">Pilih guru</label>
                <select id="jp_guru" name="id_guru" class="sdm-select" required>
                    <?php foreach ($allGurus as $g): ?>
                        <option value="<?= (int) $g['id_guru'] ?>"><?= esc($g['nama_lengkap']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="sdm-btn-primary">Simpan jadwal piket</button>
        </form>
    </div>
</div>

<?= view('partials/nav_operator', ['activeNav' => $activeNav]) ?>
<?= $this->endSection() ?>
