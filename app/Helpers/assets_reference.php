<?php

namespace App\Helpers;

/**
 * ============================================================
 * Download Frontend Assets Script
 * Jalankan: php spark download:assets
 * Atau gunakan file download-assets.bat
 * ============================================================
 *
 * CATATAN: File ini hanya referensi. Unduh manual sesuai instruksi di
 * download-assets.bat atau gunakan CDN.
 *
 * DAFTAR LIBRARY/ASET YANG DIBUTUHKAN:
 *
 * 1. AdminLTE 3.2.0
 *    - URL: https://github.com/ColorlibHQ/AdminLTE/releases/tag/v3.2.0
 *    - Target: public/assets/vendor/adminlte/
 *    - File penting:
 *      - dist/css/adminlte.min.css
 *      - dist/js/adminlte.min.js
 *      - plugins/bootstrap/js/bootstrap.bundle.min.js
 *      - plugins/jquery/jquery.min.js
 *      - plugins/fontawesome-free/css/all.min.css
 *      - plugins/sweetalert2/sweetalert2.min.js
 *      - plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css
 *      - plugins/datatables-bs4/ (opsional)
 *
 * 2. face-api.js v0.22.2
 *    - URL: https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js
 *    - Target: public/assets/vendor/face-api/face-api.min.js
 *    - Model: https://github.com/justadudewhohacks/face-api.js-models
 *      - ssd_mobilenetv1 (deteksi wajah)
 *      - face_landmark_68 (landmark wajah)
 *      - face_recognition (pengenalan wajah - 128-d descriptor)
 *    - Target Model: public/assets/vendor/face-api/models/
 *
 * 3. SweetAlert2 (sudah termasuk AdminLTE plugins)
 *
 * 4. jQuery 3.x (sudah termasuk AdminLTE plugins)
 *
 * 5. Bootstrap 5 (sudah termasuk AdminLTE plugins)
 *
 * 6. Font Awesome 5 (sudah termasuk AdminLTE plugins)
 */
