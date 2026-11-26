<?= $this->extend('layouts/mobile') ?>

<?= $this->section('content') ?>
<div class="sdm-login-hero">
    <div class="sdm-logo-circle">SM</div>
    <h1 class="sdm-login-title">Absensi Guru</h1>
    <p class="sdm-login-sub">SD Muhammadiyah 25 Medan</p>
    <p class="sdm-login-tag">Sistem Presensi &amp; Kelola Piket</p>
</div>

<div class="sdm-login-sheet">
    <?= view('partials/flash') ?>

    <form action="<?= base_url('login') ?>" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="login_tab" id="login_tab" value="<?= esc($activeTab) ?>">

        <div class="sdm-segment">
            <button type="button" class="sdm-segment-btn <?= $activeTab === 'guru' ? 'active' : '' ?>" data-tab="guru">Guru</button>
            <button type="button" class="sdm-segment-btn <?= $activeTab === 'operator' ? 'active' : '' ?>" data-tab="operator">Operator</button>
        </div>

        <div class="sdm-form-group">
            <label for="username" class="sdm-label">NIP / Nama pengguna</label>
            <div class="sdm-input-wrap">
                <span class="sdm-input-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                    </svg>
                </span>
                <input
                    type="text"
                    id="username"
                    name="username"
                    class="sdm-input with-icon"
                    value="<?= esc(old('username')) ?>"
                    placeholder="Masukkan NIP atau nama pengguna"
                    required
                    autocomplete="username"
                >
            </div>
        </div>

        <div class="sdm-form-group">
            <label for="password" class="sdm-label">Kata sandi</label>
            <div class="sdm-input-wrap">
                <span class="sdm-input-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </span>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="sdm-input with-icon"
                    placeholder="Masukkan kata sandi"
                    required
                    autocomplete="current-password"
                >
            </div>
        </div>

        <button type="submit" class="sdm-btn-primary" style="margin-top: 6px;">Masuk</button>
    </form>

    <p style="text-align: center; font-size: 11.5px; color: var(--sdm-muted); margin: 18px 0 0;">
        Lupa kata sandi? Hubungi operator sekolah.
    </p>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.querySelectorAll('.sdm-segment-btn[data-tab]').forEach((btn) => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.sdm-segment-btn[data-tab]').forEach((b) => b.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('login_tab').value = btn.dataset.tab;
    });
});
</script>
<?= $this->endSection() ?>
