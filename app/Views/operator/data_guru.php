<?= $this->extend('layouts/mobile') ?>

<?= $this->section('content') ?>
<header class="sdm-header">
    <div class="sdm-header-row">
        <div>
            <div class="sdm-eyebrow">Operator</div>
            <h1 class="sdm-header-title">Data guru</h1>
        </div>
        <button type="button" class="sdm-icon-btn" onclick="openTambahGuru()" title="Tambah Guru">
            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                <path d="M12 5v14M5 12h14"/>
            </svg>
        </button>
    </div>
</header>

<main class="sdm-content">
    <?= view('partials/flash') ?>

    <form action="<?= base_url('operator/guru') ?>" method="get" style="margin-bottom: 14px;">
        <div class="sdm-input-wrap">
            <span class="sdm-input-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="7"/><path d="m21 21-4.35-4.35"/>
                </svg>
            </span>
            <input
                type="search"
                id="search-guru"
                name="q"
                value="<?= esc($search) ?>"
                class="sdm-input with-icon"
                style="background:#FFFFFF; border-radius:14px;"
                placeholder="Cari nama guru..."
            >
        </div>
    </form>

    <div class="sdm-card">
        <?php if (empty($gurus)): ?>
            <p style="text-align:center; font-size:13px; color:var(--sdm-muted); margin:16px 0;">Data guru tidak ditemukan.</p>
        <?php else: ?>
            <?php foreach ($gurus as $g): ?>
                <?php
                $wajahAda = ! empty($g['face_descriptor']);
                $statusWajah = $wajahAda ? 'Wajah terdaftar' : 'Belum daftar wajah';
                $subInfo = trim(($g['nip'] ?: 'Tanpa NIP') . ' • ' . ($g['jabatan'] ?: 'Guru'));
                ?>
                <div class="sdm-list-item guru-row" data-name="<?= esc(strtolower($g['nama_lengkap'] . ' ' . ($g['nip'] ?? ''))) ?>" onclick='openEditGuru(<?= json_encode($g, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' style="cursor:pointer;">
                    <div class="sdm-list-left">
                        <div class="sdm-avatar"><?= esc(inisial_nama($g['nama_lengkap'])) ?></div>
                        <div style="min-width:0;">
                            <div class="sdm-item-title"><?= esc($g['nama_lengkap']) ?></div>
                            <div class="sdm-item-sub"><?= esc($subInfo) ?></div>
                        </div>
                    </div>
                    <span class="sdm-pill <?= esc(badge_status_class($statusWajah)) ?>"><?= esc($statusWajah) ?></span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<!-- Modal Tambah / Edit Guru -->
<div id="modal-guru" class="sdm-modal-backdrop">
    <div class="sdm-modal-sheet">
        <div class="sdm-card-header">
            <h2 id="modal-guru-title" class="sdm-card-title">Tambah Guru</h2>
            <button type="button" onclick="App.closeModal('modal-guru')" style="border:none; background:none; font-size:20px; cursor:pointer; color:var(--sdm-muted);">&times;</button>
        </div>

        <form action="<?= base_url('operator/guru/simpan') ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="id_guru" id="f_id_guru" value="0">

            <div class="sdm-form-group">
                <label for="f_nama" class="sdm-label">Nama lengkap &amp; gelar</label>
                <input type="text" id="f_nama" name="nama_lengkap" class="sdm-input" required>
            </div>
            <div class="sdm-time-grid">
                <div>
                    <label for="f_nip" class="sdm-label">NIP / NBM</label>
                    <input type="text" id="f_nip" name="nip" class="sdm-input">
                </div>
                <div>
                    <label for="f_hp" class="sdm-label">No. HP</label>
                    <input type="text" id="f_hp" name="no_hp" class="sdm-input">
                </div>
            </div>
            <div class="sdm-form-group">
                <label for="f_jabatan" class="sdm-label">Jabatan / Mapel</label>
                <input type="text" id="f_jabatan" name="jabatan" class="sdm-input" placeholder="Misal: Guru Kelas 1">
            </div>
            <div class="sdm-time-grid">
                <div>
                    <label for="f_username" class="sdm-label">Nama pengguna</label>
                    <input type="text" id="f_username" name="username" class="sdm-input" required>
                </div>
                <div>
                    <label for="f_password" class="sdm-label">Kata sandi <span id="pw-hint" style="font-weight:400; color:var(--sdm-muted);"></span></label>
                    <input type="password" id="f_password" name="password" class="sdm-input">
                </div>
            </div>
            <div class="sdm-form-group" id="f_aktif_wrap" style="display:none;">
                <label for="f_aktif" class="sdm-label">Status akun</label>
                <select id="f_aktif" name="status_aktif" class="sdm-select">
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>

            <button type="submit" class="sdm-btn-primary">Simpan data guru</button>
        </form>

        <div id="guru-extra-actions" style="display:none; margin-top:10px; gap:8px;">
            <form id="form-reset-wajah" method="post" style="flex:1;">
                <?= csrf_field() ?>
                <button type="submit" class="sdm-btn-soft-green">Reset wajah</button>
            </form>
            <form id="form-hapus-guru" method="post" style="flex:1;" onsubmit="return confirm('Hapus akun guru ini beserta riwayat absensinya?');">
                <?= csrf_field() ?>
                <button type="submit" class="sdm-btn-soft-red">Hapus guru</button>
            </form>
        </div>
    </div>
</div>

<?= view('partials/nav_operator', ['activeNav' => $activeNav]) ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const searchInput = document.getElementById('search-guru');
searchInput.addEventListener('input', () => {
    const q = searchInput.value.trim().toLowerCase();
    document.querySelectorAll('.guru-row').forEach((row) => {
        row.style.display = row.dataset.name.includes(q) ? 'flex' : 'none';
    });
});

function openTambahGuru() {
    document.getElementById('modal-guru-title').textContent = 'Tambah Guru Baru';
    document.getElementById('f_id_guru').value = '0';
    document.getElementById('f_nama').value = '';
    document.getElementById('f_nip').value = '';
    document.getElementById('f_hp').value = '';
    document.getElementById('f_jabatan').value = '';
    document.getElementById('f_username').value = '';
    document.getElementById('f_password').value = '';
    document.getElementById('f_password').required = true;
    document.getElementById('pw-hint').textContent = '(wajib)';
    document.getElementById('f_aktif_wrap').style.display = 'none';
    document.getElementById('guru-extra-actions').style.display = 'none';
    App.openModal('modal-guru');
}

function openEditGuru(g) {
    document.getElementById('modal-guru-title').textContent = 'Detail & Ubah Guru';
    document.getElementById('f_id_guru').value = g.id_guru;
    document.getElementById('f_nama').value = g.nama_lengkap || '';
    document.getElementById('f_nip').value = g.nip || '';
    document.getElementById('f_hp').value = g.no_hp || '';
    document.getElementById('f_jabatan').value = g.jabatan || '';
    document.getElementById('f_username').value = g.username || '';
    document.getElementById('f_password').value = '';
    document.getElementById('f_password').required = false;
    document.getElementById('pw-hint').textContent = '(kosongkan jika tetap)';
    document.getElementById('f_aktif').value = String(g.status_aktif ?? 1);
    document.getElementById('f_aktif_wrap').style.display = 'block';
    document.getElementById('form-reset-wajah').action = App.baseUrl + 'operator/guru/reset-wajah/' + g.id_guru;
    document.getElementById('form-hapus-guru').action = App.baseUrl + 'operator/guru/hapus/' + g.id_guru;
    document.getElementById('guru-extra-actions').style.display = 'flex';
    App.openModal('modal-guru');
}
</script>
<?= $this->endSection() ?>
