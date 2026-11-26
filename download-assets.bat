@echo off
REM ================================================================
REM  download-assets.bat
REM  Script untuk mengunduh semua library/aset frontend yang dibutuhkan
REM  Jalankan dari root proyek: download-assets.bat
REM ================================================================

echo ======================================
echo  Downloading Frontend Assets
echo  Absensi SD Muhammadiyah 25
echo ======================================
echo.

REM === 1. AdminLTE 3.2.0 ===
echo [1/5] Downloading AdminLTE 3.2.0...
echo   Unduh manual dari: https://github.com/ColorlibHQ/AdminLTE/releases/tag/v3.2.0
echo   Ekstrak isi folder dist/ ke: public\assets\vendor\adminlte\
echo   Salin juga folder plugins/ yang dibutuhkan ke: public\assets\vendor\adminlte\plugins\
echo.

REM === 2. Bootstrap 5.3.x (sudah termasuk di AdminLTE) ===
echo [2/5] Bootstrap 5...
echo   Sudah disertakan dalam AdminLTE 3.2 (plugins/bootstrap/)
echo   Atau unduh terpisah dari: https://getbootstrap.com/docs/5.3/getting-started/download/
echo.

REM === 3. Font Awesome 5/6 (sudah termasuk di AdminLTE) ===
echo [3/5] Font Awesome...
echo   Sudah disertakan dalam AdminLTE 3.2 (plugins/fontawesome-free/)
echo   Atau unduh dari: https://fontawesome.com/download
echo.

REM === 4. face-api.js ===
echo [4/5] Downloading face-api.js...
echo   Unduh dari CDN atau GitHub:
echo.
echo   face-api.min.js:
echo     https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js
echo     Simpan ke: public\assets\vendor\face-api\face-api.min.js
echo.
echo   Model files (simpan ke public\assets\vendor\face-api\models\):
echo     https://github.com/justadudewhohacks/face-api.js/tree/master/weights
echo     - ssd_mobilenetv1_model-weights_manifest.json + shard files
echo     - face_landmark_68_model-weights_manifest.json + shard files
echo     - face_recognition_model-weights_manifest.json + shard files
echo     - face_expression_model-weights_manifest.json + shard files (opsional)
echo.
echo   Atau unduh semua model sekaligus dari:
echo     https://github.com/justadudewhohacks/face-api.js-models
echo.

REM === 5. SweetAlert2 ===
echo [5/5] SweetAlert2...
echo   Sudah disertakan dalam AdminLTE 3.2 (plugins/sweetalert2/)
echo   Atau dari CDN: https://cdn.jsdelivr.net/npm/sweetalert2@11
echo.

echo ======================================
echo  INSTRUKSI:
echo ======================================
echo.
echo  Struktur folder yang harus diisi:
echo.
echo  public/assets/vendor/
echo    adminlte/
echo      css/adminlte.min.css
echo      js/adminlte.min.js
echo      plugins/
echo        bootstrap/
echo        fontawesome-free/
echo        sweetalert2/
echo        datatables-bs4/ (opsional, untuk tabel interaktif)
echo    face-api/
echo      face-api.min.js
echo      models/
echo        ssd_mobilenetv1_model-weights_manifest.json
echo        ssd_mobilenetv1_model-shard1
echo        ssd_mobilenetv1_model-shard2
echo        face_landmark_68_model-weights_manifest.json
echo        face_landmark_68_model-shard1
echo        face_recognition_model-weights_manifest.json
echo        face_recognition_model-shard1
echo        face_recognition_model-shard2
echo.
echo  Setelah semua file ditempatkan, jalankan:
echo    php spark serve --port 8080
echo.
echo ======================================
pause
