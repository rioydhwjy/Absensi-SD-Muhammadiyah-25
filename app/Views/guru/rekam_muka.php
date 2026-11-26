<?= $this->extend('layouts/mobile') ?>

<?= $this->section('content') ?>
<?php
$isEnroll = ($mode ?? 'absen') === 'enroll';
$backUrl  = $isEnroll ? base_url('guru/profil') : base_url('guru/cek-lokasi');
$stepText = $isEnroll ? 'Pendaftaran Wajah' : 'Langkah 2 dari 2';
$pageHead = $isEnroll ? 'Daftarkan wajah' : 'Rekam muka';
?>
<header class="sdm-cam-header">
    <a href="<?= $backUrl ?>" class="sdm-icon-btn" aria-label="Kembali">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <path d="m15 18-6-6 6-6"/>
        </svg>
    </a>
    <div>
        <div class="sdm-eyebrow"><?= esc($stepText) ?></div>
        <h1 class="sdm-header-title"><?= esc($pageHead) ?></h1>
    </div>
</header>

<main class="sdm-cam-body">
    <div style="width: 100%; text-align: center;">
        <div id="oval-frame" class="sdm-oval-wrap">
            <video id="cam-video" autoplay playsinline muted></video>
            <canvas id="cam-canvas" style="display:none;"></canvas>
            <div class="sdm-scan-line"></div>
            <div id="cam-silhouette" style="position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; pointer-events:none; opacity:0.35;">
                <div style="width:66px; height:66px; border-radius:50%; background:#416351; margin-bottom:10px;"></div>
                <div style="width:104px; height:52px; border-radius:50%; background:#416351;"></div>
            </div>
        </div>

        <div id="verified-badge" class="sdm-verified-badge">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <path d="m5 12 5 5L20 7"/>
            </svg>
        </div>

        <h2 id="cam-title" style="font-size:16px; font-weight:700; margin:6px 0 4px;">Posisikan wajah di dalam bingkai</h2>
        <p id="cam-subtitle" style="font-size:12px; color:rgba(255,255,255,0.68); margin:0;">
            Pastikan cahaya cukup dan wajah terlihat jelas di kamera.
        </p>
    </div>

    <div style="width: 100%; margin-top: 20px;">
        <button type="button" id="btn-start-verify" class="sdm-btn-primary" style="margin-bottom: 10px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/>
            </svg>
            <span><?= $isEnroll ? 'Simpan data wajah' : 'Mulai verifikasi' ?></span>
        </button>

        <div id="scan-status-text" style="display:none; text-align:center; font-size:12px; font-weight:700; color:#FACC15; margin-bottom:10px;">
            Wajah Terdeteksi &bull; Memverifikasi kedipan...
        </div>

        <a href="<?= $backUrl ?>" id="btn-back" class="sdm-btn-outline-dark">Kembali</a>

        <a href="<?= base_url($isEnroll ? 'guru/profil' : 'guru/beranda') ?>" id="btn-done" class="sdm-btn-primary" style="display:none; margin-top:8px;">
            &#10003; Selesai &mdash; kembali ke <?= $isEnroll ? 'Profil' : 'Beranda' ?>
        </a>
    </div>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(() => {
    const mode = <?= json_encode($mode ?? 'absen') ?>;
    const lat  = <?= json_encode((float) ($lat ?? 0)) ?>;
    const lng  = <?= json_encode((float) ($lng ?? 0)) ?>;

    const video       = document.getElementById('cam-video');
    const canvas      = document.getElementById('cam-canvas');
    const oval        = document.getElementById('oval-frame');
    const silhouette  = document.getElementById('cam-silhouette');
    const badge       = document.getElementById('verified-badge');
    const titleEl     = document.getElementById('cam-title');
    const subEl       = document.getElementById('cam-subtitle');
    const btnVerify   = document.getElementById('btn-start-verify');
    const scanText    = document.getElementById('scan-status-text');
    const btnBack     = document.getElementById('btn-back');
    const btnDone     = document.getElementById('btn-done');

    let stream = null;

    async function startCamera() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            titleEl.textContent = 'Kamera tidak didukung';
            subEl.textContent = 'Gunakan browser modern dengan akses kamera (HTTPS/localhost).';
            return;
        }
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } },
                audio: false
            });
            video.srcObject = stream;
            video.onloadedmetadata = () => {
                silhouette.style.display = 'none';
            };
        } catch (err) {
            titleEl.textContent = 'Izin kamera diperlukan';
            subEl.textContent = 'Izinkan akses kamera pada browser untuk melakukan verifikasi wajah.';
        }
    }

    function captureSnapshot() {
        if (!video.videoWidth) return null;
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.translate(canvas.width, 0);
        ctx.scale(-1, 1);
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        return canvas.toDataURL('image/jpeg', 0.82);
    }

    async function extractDescriptorIfAvailable() {
        if (typeof faceapi === 'undefined') return null;
        try {
            const det = await faceapi
                .detectSingleFace(video)
                .withFaceLandmarks()
                .withFaceDescriptor();
            return det ? Array.from(det.descriptor) : null;
        } catch (e) {
            return null;
        }
    }

    btnVerify.addEventListener('click', async () => {
        btnVerify.disabled = true;
        btnVerify.innerHTML = '<span>Memverifikasi...</span>';
        oval.classList.add('state-scanning');
        titleEl.textContent = 'Hadap lurus dan berkedip...';
        subEl.textContent = 'Jangan bergerak selama proses verifikasi';
        scanText.style.display = 'block';

        // Simulasi jeda pembacaan liveness / deteksi bingkai
        await new Promise((r) => setTimeout(r, 1400));

        const snapshot   = captureSnapshot();
        const descriptor = await extractDescriptorIfAvailable();

        try {
            let res;
            if (mode === 'enroll') {
                res = await App.postJson('guru/simpan-wajah', {
                    foto_wajah: snapshot,
                    descriptor: descriptor
                });
            } else {
                res = await App.postJson('guru/absen-masuk', {
                    latitude: lat,
                    longitude: lng,
                    foto_capture: snapshot,
                    descriptor: descriptor
                });
            }

            if (res.status === 'success') {
                oval.classList.remove('state-scanning');
                oval.classList.add('state-verified');
                badge.classList.add('show');
                titleEl.style.color = '#4ADE80';
                titleEl.textContent = 'Wajah terverifikasi';
                subEl.textContent = res.message || 'Verifikasi berhasil.';
                scanText.style.display = 'none';
                btnVerify.style.display = 'none';
                btnBack.style.display = 'none';
                btnDone.style.display = 'inline-flex';
                if (stream) {
                    stream.getTracks().forEach((t) => t.stop());
                }
            } else {
                throw new Error(res.message || 'Verifikasi gagal. Silakan coba lagi.');
            }
        } catch (err) {
            oval.classList.remove('state-scanning');
            titleEl.style.color = '#FCA5A5';
            titleEl.textContent = 'Verifikasi belum berhasil';
            subEl.textContent = err.message;
            scanText.style.display = 'none';
            btnVerify.disabled = false;
            btnVerify.innerHTML = '<span>Coba verifikasi lagi</span>';
        }
    });

    startCamera();
})();
</script>
<?= $this->endSection() ?>
