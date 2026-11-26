<?= $this->extend('layouts/mobile') ?>

<?= $this->section('content') ?>
<header class="sdm-header">
    <div class="sdm-header-row" style="justify-content: flex-start;">
        <a href="<?= base_url('guru/beranda') ?>" class="sdm-icon-btn" aria-label="Kembali">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="m15 18-6-6 6-6"/>
            </svg>
        </a>
        <div>
            <div class="sdm-eyebrow">Langkah 1 dari 2</div>
            <h1 class="sdm-header-title">Cek lokasi</h1>
        </div>
    </div>
</header>

<main class="sdm-content">
    <?php if ($absenHariIni): ?>
        <div class="sdm-flash sdm-flash-success">
            <span>&#10003;</span>
            <span>Anda sudah melakukan absen masuk hari ini pada pukul <?= esc(substr($absenHariIni['waktu_masuk'] ?? '00:00', 0, 5)) ?> WIB.</span>
        </div>
    <?php endif; ?>

    <!-- Peta Geofence Interaktif -->
    <div class="sdm-map-box">
        <svg viewBox="0 0 400 245" width="100%" height="100%" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
            <rect width="400" height="245" fill="#DCEAE1"/>
            <!-- Jalan & Blok Lingkungan Sekolah -->
            <path d="M0 78 H400 M0 168 H400 M95 0 V245 M200 0 V245 M305 0 V245" stroke="#EEF5F0" stroke-width="10"/>
            <rect x="20" y="16" width="60" height="46" rx="6" fill="#CBE0D2"/>
            <rect x="115" y="16" width="70" height="46" rx="6" fill="#D2E4D8"/>
            <rect x="218" y="16" width="72" height="46" rx="6" fill="#CBE0D2"/>
            <rect x="320" y="16" width="62" height="46" rx="6" fill="#D2E4D8"/>
            <rect x="20" y="96" width="60" height="56" rx="6" fill="#D2E4D8"/>
            <rect x="320" y="96" width="62" height="130" rx="6" fill="#CBE0D2"/>
            <rect x="115" y="182" width="70" height="48" rx="6" fill="#CBE0D2"/>
            <rect x="218" y="182" width="72" height="48" rx="6" fill="#D2E4D8"/>

            <!-- Lingkaran Radius Geofence -->
            <circle cx="200" cy="122" r="76" fill="rgba(19, 107, 61, 0.14)" stroke="#136B3D" stroke-width="1.8" stroke-dasharray="5 4"/>

            <!-- Pin Sekolah -->
            <g transform="translate(200, 122)">
                <circle cx="0" cy="0" r="14" fill="#136B3D" opacity="0.18"/>
                <path d="M0 -14 C-6 -14 -10 -9.5 -10 -4 C-10 3.5 0 12 0 12 C0 12 10 3.5 10 -4 C10 -9.5 6 -14 0 -14 Z" fill="#136B3D"/>
                <circle cx="0" cy="-4" r="3.5" fill="#FFFFFF"/>
            </g>

            <!-- Titik Posisi Guru (Dinamis via JS) -->
            <g id="user-map-dot" transform="translate(200, 122)">
                <circle cx="0" cy="0" r="13" fill="rgba(37, 99, 235, 0.25)"/>
                <circle cx="0" cy="0" r="6" fill="#2563EB" stroke="#FFFFFF" stroke-width="2.2"/>
            </g>
        </svg>
        <div class="sdm-map-badge">Radius <?= (int) $aturan['radius_meter'] ?> m</div>
    </div>

    <!-- Kartu Status Lokasi -->
    <div class="sdm-card">
        <div class="sdm-card-header" style="margin-bottom: 14px;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div id="geo-icon-circle" class="sdm-avatar" style="background:#EDF3EF; color:#136B3D;">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4">
                        <path d="m5 12 5 5L20 7"/>
                    </svg>
                </div>
                <div>
                    <div id="geo-title" style="font-size:14px; font-weight:700;">Mendeteksi lokasi GPS...</div>
                    <div id="geo-subtitle" style="font-size:12px; color:var(--sdm-muted); margin-top:2px;">Mengambil koordinat perangkat Anda</div>
                </div>
            </div>
            <span id="geo-pill" class="sdm-pill sdm-pill-amber">Memuat</span>
        </div>

        <button type="button" id="btn-lanjut-muka" class="sdm-btn-primary" disabled>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/>
            </svg>
            Lanjut rekam muka
        </button>

        <button type="button" id="btn-refresh-gps" class="sdm-btn-soft-green" style="margin-top:8px; padding:9px;">
            Muat ulang lokasi GPS
        </button>
    </div>

    <!-- Kartu Peringatan Luar Radius (tampil otomatis jika di luar radius / GPS gagal) -->
    <div id="geo-out-alert" class="sdm-error-box" style="display: none;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:4px;">
            <strong style="display:flex; align-items:center; gap:6px;">
                <span>&#9888;</span>
                <span id="geo-alert-title">Lokasi di luar batas radius</span>
            </strong>
            <span class="sdm-pill sdm-pill-red">Di luar</span>
        </div>
        <div id="geo-alert-desc">
            Anda berada di luar radius sekolah (<?= (int) $aturan['radius_meter'] ?> m). Silakan mendekat ke area SD Muhammadiyah 25 untuk melanjutkan verifikasi wajah.
        </div>
    </div>
</main>

<?= view('partials/nav_guru', ['activeNav' => $activeNav]) ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(() => {
    const schoolLat = <?= json_encode((float) $aturan['latitude_sekolah']) ?>;
    const schoolLng = <?= json_encode((float) $aturan['longitude_sekolah']) ?>;
    const radiusMeter = <?= json_encode((int) $aturan['radius_meter']) ?>;
    const alreadyAbsen = <?= json_encode((bool) $absenHariIni) ?>;

    const titleEl = document.getElementById('geo-title');
    const subEl   = document.getElementById('geo-subtitle');
    const pillEl  = document.getElementById('geo-pill');
    const iconEl  = document.getElementById('geo-icon-circle');
    const btnGo   = document.getElementById('btn-lanjut-muka');
    const outBox  = document.getElementById('geo-out-alert');
    const outDesc = document.getElementById('geo-alert-desc');
    const dotEl   = document.getElementById('user-map-dot');

    let currentCoords = null;

    function updateMapDot(lat, lng) {
        const dLat = (lat - schoolLat) * 111320;
        const dLng = (lng - schoolLng) * 111320 * Math.cos(schoolLat * Math.PI / 180);
        const pxPerMeter = 76 / Math.max(radiusMeter, 20);
        const dx = Math.max(-160, Math.min(160, dLng * pxPerMeter));
        const dy = Math.max(-95, Math.min(95, -dLat * pxPerMeter));
        dotEl.setAttribute('transform', `translate(${200 + dx}, ${122 + dy})`);
    }

    async function checkGps() {
        titleEl.textContent = 'Mendeteksi lokasi GPS...';
        subEl.textContent = 'Memastikan Anda berada di area sekolah';
        pillEl.className = 'sdm-pill sdm-pill-amber';
        pillEl.textContent = 'Memeriksa';
        btnGo.disabled = true;
        outBox.style.display = 'none';

        const res = await App.validateLocation(schoolLat, schoolLng, radiusMeter);

        if (res.error) {
            titleEl.textContent = 'Lokasi belum tersedia';
            subEl.textContent = res.error;
            pillEl.className = 'sdm-pill sdm-pill-red';
            pillEl.textContent = 'Gagal';
            iconEl.style.background = '#FEECEC';
            iconEl.style.color = '#DC2626';
            outBox.style.display = 'block';
            outDesc.textContent = res.error;
            return;
        }

        currentCoords = { lat: res.latitude, lng: res.longitude, jarak: res.jarak };
        updateMapDot(res.latitude, res.longitude);

        if (res.valid) {
            titleEl.textContent = 'Anda di area sekolah';
            subEl.textContent = `Jarak dari sekolah: ${res.jarak} m (maks ${radiusMeter} m)`;
            pillEl.className = 'sdm-pill sdm-pill-green';
            pillEl.textContent = 'Valid';
            iconEl.style.background = '#E4F6EC';
            iconEl.style.color = '#136B3D';
            btnGo.disabled = alreadyAbsen;
            outBox.style.display = 'none';
        } else {
            titleEl.textContent = 'Anda di luar area sekolah';
            subEl.textContent = `Jarak dari sekolah: ${res.jarak} m (maks ${radiusMeter} m)`;
            pillEl.className = 'sdm-pill sdm-pill-red';
            pillEl.textContent = 'Di luar';
            iconEl.style.background = '#FEECEC';
            iconEl.style.color = '#DC2626';
            btnGo.disabled = true;
            outBox.style.display = 'block';
            outDesc.textContent = `Anda berada ${res.jarak} m dari sekolah (batas ${radiusMeter} m). Mendekatlah ke area SD Muhammadiyah 25 untuk melanjutkan verifikasi wajah.`;
        }
    }

    btnGo.addEventListener('click', () => {
        if (!currentCoords) return;
        window.location.href = `${App.baseUrl}guru/rekam-muka?lat=${encodeURIComponent(currentCoords.lat)}&lng=${encodeURIComponent(currentCoords.lng)}`;
    });

    document.getElementById('btn-refresh-gps').addEventListener('click', checkGps);
    checkGps();
})();
</script>
<?= $this->endSection() ?>
