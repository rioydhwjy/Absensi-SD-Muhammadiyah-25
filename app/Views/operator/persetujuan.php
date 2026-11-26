<?= $this->extend('layouts/mobile') ?>

<?= $this->section('content') ?>
<header class="sdm-header">
    <div class="sdm-header-row">
        <div>
            <div class="sdm-eyebrow">Operator</div>
            <h1 class="sdm-header-title">Persetujuan dan koreksi</h1>
        </div>
    </div>
</header>

<main class="sdm-content">
    <?= view('partials/flash') ?>

    <div class="sdm-segment">
        <button type="button" class="sdm-segment-btn <?= $activeTab === 'izin' ? 'active' : '' ?>" data-panel="panel-izin">Izin / Sakit</button>
        <button type="button" class="sdm-segment-btn <?= $activeTab === 'koreksi' ? 'active' : '' ?>" data-panel="panel-koreksi">Koreksi absen</button>
    </div>

    <!-- Tab 1: Persetujuan Izin / Sakit -->
    <section id="panel-izin" style="<?= $activeTab === 'izin' ? '' : 'display:none;' ?>">
        <?php if (empty($pengajuan)): ?>
            <div class="sdm-card">
                <p style="text-align:center; font-size:13px; color:var(--sdm-muted); margin:14px 0;">Belum ada pengajuan izin atau sakit.</p>
            </div>
        <?php else: ?>
            <?php foreach ($pengajuan as $p): ?>
                <?php
                $rentang = $p['tanggal_mulai'] === $p['tanggal_selesai']
                    ? format_tanggal_pendek($p['tanggal_mulai'])
                    : format_tanggal_pendek($p['tanggal_mulai']) . ' - ' . format_tanggal_pendek($p['tanggal_selesai']);
                ?>
                <div class="sdm-card">
                    <div class="sdm-card-header" style="margin-bottom:8px;">
                        <div class="sdm-list-left">
                            <div class="sdm-avatar"><?= esc(inisial_nama($p['nama_lengkap'])) ?></div>
                            <div style="min-width:0;">
                                <div class="sdm-item-title"><?= esc($p['nama_lengkap']) ?></div>
                                <div class="sdm-item-sub"><?= esc($p['jenis'] . ' • ' . $rentang) ?></div>
                            </div>
                        </div>
                        <span class="sdm-pill <?= esc(badge_status_class($p['jenis'])) ?>"><?= esc($p['jenis']) ?></span>
                    </div>

                    <p style="font-size:12.5px; color:var(--sdm-muted); margin:0 0 12px;">
                        Alasan: <?= esc($p['alasan']) ?>
                    </p>

                    <?php if ($p['status_persetujuan'] === 'Menunggu'): ?>
                        <div class="sdm-time-grid" style="margin-bottom:0;">
                            <form action="<?= base_url('operator/persetujuan/proses/' . $p['id']) ?>" method="post">
                                <?= csrf_field() ?>
                                <input type="hidden" name="aksi" value="tolak">
                                <button type="submit" class="sdm-btn-soft-red">Tolak</button>
                            </form>
                            <form action="<?= base_url('operator/persetujuan/proses/' . $p['id']) ?>" method="post">
                                <?= csrf_field() ?>
                                <input type="hidden" name="aksi" value="setujui">
                                <button type="submit" class="sdm-btn-primary" style="height:39px;">Setujui</button>
                            </form>
                        </div>
                    <?php elseif ($p['status_persetujuan'] === 'Disetujui'): ?>
                        <div class="sdm-btn-soft-green" style="padding:8px 12px; font-size:12px; cursor:default;">
                            &#10003; Disetujui
                        </div>
                    <?php else: ?>
                        <div class="sdm-btn-soft-red" style="padding:8px 12px; font-size:12px; cursor:default;">
                            &times; Ditolak
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

    <!-- Tab 2: Koreksi Absen Manual -->
    <section id="panel-koreksi" style="<?= $activeTab === 'koreksi' ? '' : 'display:none;' ?>">
        <form action="<?= base_url('operator/persetujuan') ?>" method="get" class="sdm-card" style="display:flex; align-items:center; gap:10px;">
            <input type="hidden" name="tab" value="koreksi">
            <input type="date" name="tanggal" value="<?= esc($tanggal) ?>" class="sdm-input" style="flex:1;">
            <button type="submit" class="sdm-btn-primary" style="width:auto; padding:0 16px; height:40px;">Tampilkan</button>
        </form>

        <div class="sdm-card">
            <div class="sdm-card-header">
                <h2 class="sdm-card-title">Daftar absensi (<?= esc(format_tanggal_pendek($tanggal)) ?>)</h2>
            </div>

            <?php foreach ($guruStatuses as $row): ?>
                <?php $st = $row['status'] ?: 'Belum absen'; ?>
                <div class="sdm-list-item">
                    <div class="sdm-list-left">
                        <div class="sdm-avatar"><?= esc(inisial_nama($row['nama_lengkap'])) ?></div>
                        <div style="min-width:0;">
                            <div class="sdm-item-title"><?= esc($row['nama_lengkap']) ?></div>
                            <div class="sdm-item-sub">
                                <?= ! empty($row['waktu_masuk']) ? esc(substr($row['waktu_masuk'], 0, 5) . ' WIB') : 'Belum ada catatan' ?>
                            </div>
                        </div>
                    </div>
                    <div style="display:flex; align-items:center; gap:6px;">
                        <span class="sdm-pill <?= esc(badge_status_class($st)) ?>"><?= esc($st) ?></span>
                        <button type="button" class="sdm-pill sdm-pill-gray" style="border:none; cursor:pointer;" onclick='openKoreksi(<?= json_encode($row, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>Ubah</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<!-- Modal Koreksi Absensi -->
<div id="modal-koreksi" class="sdm-modal-backdrop">
    <div class="sdm-modal-sheet">
        <div class="sdm-card-header">
            <h2 id="koreksi-nama" class="sdm-card-title">Koreksi Absensi</h2>
            <button type="button" onclick="App.closeModal('modal-koreksi')" style="border:none; background:none; font-size:20px; cursor:pointer; color:var(--sdm-muted);">&times;</button>
        </div>

        <form action="<?= base_url('operator/persetujuan/koreksi') ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="id_guru" id="k_id_guru">
            <input type="hidden" name="tanggal" value="<?= esc($tanggal) ?>">

            <div class="sdm-form-group">
                <label for="k_status" class="sdm-label">Status kehadiran</label>
                <select id="k_status" name="status" class="sdm-select" required>
                    <option value="Hadir">Hadir</option>
                    <option value="Terlambat">Terlambat</option>
                    <option value="Izin">Izin</option>
                    <option value="Sakit">Sakit</option>
                    <option value="Alpa">Alpa</option>
                </select>
            </div>
            <div class="sdm-form-group">
                <label for="k_waktu" class="sdm-label">Jam masuk (opsional)</label>
                <input type="time" id="k_waktu" name="waktu_masuk" class="sdm-input">
            </div>
            <div class="sdm-form-group">
                <label for="k_ket" class="sdm-label">Catatan koreksi</label>
                <textarea id="k_ket" name="keterangan" rows="2" class="sdm-textarea" placeholder="Contoh: Koreksi karena kendala GPS"></textarea>
            </div>

            <button type="submit" class="sdm-btn-primary">Simpan koreksi</button>
        </form>
    </div>
</div>

<?= view('partials/nav_operator', ['activeNav' => $activeNav]) ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.querySelectorAll('.sdm-segment-btn[data-panel]').forEach((btn) => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.sdm-segment-btn[data-panel]').forEach((b) => b.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('panel-izin').style.display    = btn.dataset.panel === 'panel-izin' ? '' : 'none';
        document.getElementById('panel-koreksi').style.display = btn.dataset.panel === 'panel-koreksi' ? '' : 'none';
    });
});

function openKoreksi(row) {
    document.getElementById('koreksi-nama').textContent = 'Koreksi: ' + row.nama_lengkap;
    document.getElementById('k_id_guru').value = row.id_guru;
    document.getElementById('k_status').value = row.status || 'Hadir';
    document.getElementById('k_waktu').value = row.waktu_masuk ? row.waktu_masuk.slice(0, 5) : '';
    document.getElementById('k_ket').value = row.keterangan || '';
    App.openModal('modal-koreksi');
}
</script>
<?= $this->endSection() ?>
