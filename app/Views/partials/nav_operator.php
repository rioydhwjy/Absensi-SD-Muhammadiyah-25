<?php $active = $activeNav ?? 'beranda'; ?>
<nav class="sdm-bottom-nav" aria-label="Navigasi Operator">
    <a href="<?= base_url('operator/beranda') ?>" class="sdm-nav-item <?= $active === 'beranda' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 10.5 12 3l9 7.5"/><path d="M5 10v10a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1V10"/>
        </svg>
        <span>Beranda</span>
    </a>
    <a href="<?= base_url('operator/guru') ?>" class="sdm-nav-item <?= $active === 'guru' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="10" cy="7" r="4"/><path d="M21 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        </svg>
        <span>Guru</span>
    </a>
    <a href="<?= base_url('operator/jadwal') ?>" class="sdm-nav-item <?= $active === 'jadwal' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="3"/><path d="M16 2v4M8 2v4M3 10h18"/>
        </svg>
        <span>Jadwal</span>
    </a>
    <a href="<?= base_url('operator/persetujuan') ?>" class="sdm-nav-item <?= $active === 'persetujuan' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="16" rx="2"/><path d="m8 12 2.5 2.5L16 9"/>
        </svg>
        <span>Persetujuan</span>
    </a>
    <a href="<?= base_url('operator/rekap') ?>" class="sdm-nav-item <?= $active === 'rekap' ? 'active' : '' ?>">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 20V10M12 20V4M6 20v-6"/>
        </svg>
        <span>Rekap</span>
    </a>
</nav>
