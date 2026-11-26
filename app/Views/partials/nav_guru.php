<?php $active = $activeNav ?? 'beranda'; ?>
<nav class="sdm-bottom-nav" aria-label="Navigasi Guru">
    <a href="<?= base_url('guru/beranda') ?>" class="sdm-nav-item <?= $active === 'beranda' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 10.5 12 3l9 7.5"/><path d="M5 10v10a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1V10"/>
        </svg>
        <span>Beranda</span>
    </a>
    <a href="<?= base_url('guru/cek-lokasi') ?>" class="sdm-nav-item <?= $active === 'absen' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 21s7-6.2 7-11.5A7 7 0 0 0 5 9.5C5 14.8 12 21 12 21Z"/><circle cx="12" cy="9.5" r="2.5"/>
        </svg>
        <span>Absen</span>
    </a>
    <a href="<?= base_url('guru/riwayat') ?>" class="sdm-nav-item <?= $active === 'riwayat' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>
        </svg>
        <span>Riwayat</span>
    </a>
    <a href="<?= base_url('guru/profil') ?>" class="sdm-nav-item <?= $active === 'profil' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="10" cy="7" r="4"/><path d="M21 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
        <span>Profil</span>
    </a>
</nav>
