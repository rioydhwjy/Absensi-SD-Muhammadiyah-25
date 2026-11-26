/**
 * app.js - Absensi SD Muhammadiyah 25 Medan
 * Helper GPS Haversine, CSRF API wrapper, dan interaksi UI.
 */

'use strict';

const App = {
    baseUrl: document.querySelector('meta[name="base-url"]')?.getAttribute('content') || '/',

    haversineDistance(lat1, lon1, lat2, lon2) {
        const R = 6371000;
        const toRad = (deg) => (deg * Math.PI) / 180;
        const dLat = toRad(lat2 - lat1);
        const dLon = toRad(lon2 - lon1);
        const a =
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    },

    getCurrentPosition() {
        return new Promise((resolve, reject) => {
            if (!navigator.geolocation) {
                reject(new Error('Geolocation tidak didukung oleh browser Anda.'));
                return;
            }
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    resolve({
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude,
                        accuracy: position.coords.accuracy,
                    });
                },
                (error) => {
                    let msg = 'Gagal mendapatkan lokasi GPS.';
                    switch (error.code) {
                        case error.PERMISSION_DENIED:
                            msg = 'Izin lokasi ditolak. Aktifkan GPS dan izinkan akses lokasi di browser.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            msg = 'Informasi lokasi tidak tersedia saat ini.';
                            break;
                        case error.TIMEOUT:
                            msg = 'Permintaan lokasi timeout. Silakan coba lagi.';
                            break;
                    }
                    reject(new Error(msg));
                },
                {
                    enableHighAccuracy: true,
                    timeout: 12000,
                    maximumAge: 0,
                }
            );
        });
    },

    async validateLocation(latSekolah, lngSekolah, radiusMeter) {
        try {
            const pos = await this.getCurrentPosition();
            const jarak = this.haversineDistance(pos.latitude, pos.longitude, latSekolah, lngSekolah);
            return {
                valid: jarak <= radiusMeter,
                latitude: pos.latitude,
                longitude: pos.longitude,
                jarak: Math.round(jarak),
                accuracy: Math.round(pos.accuracy || 0),
            };
        } catch (err) {
            return {
                valid: false,
                error: err.message,
                latitude: null,
                longitude: null,
                jarak: null,
            };
        }
    },

    getCsrfToken() {
        return document.querySelector('meta[name="X-CSRF-TOKEN"]')?.getAttribute('content') || '';
    },

    getCsrfName() {
        return document.querySelector('meta[name="X-CSRF-NAME"]')?.getAttribute('content') || 'csrf_test_name';
    },

    async postJson(path, payload) {
        const body = { ...payload };
        body[this.getCsrfName()] = this.getCsrfToken();

        const res = await fetch(this.baseUrl + path.replace(/^\//, ''), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': this.getCsrfToken(),
            },
            body: JSON.stringify(body),
        });
        return res.json();
    },

    openModal(id) {
        const el = document.getElementById(id);
        if (el) el.classList.add('open');
    },

    closeModal(id) {
        const el = document.getElementById(id);
        if (el) el.classList.remove('open');
    },

    init() {
        document.querySelectorAll('.sdm-modal-backdrop').forEach((backdrop) => {
            backdrop.addEventListener('click', (e) => {
                if (e.target === backdrop) {
                    backdrop.classList.remove('open');
                }
            });
        });

        document.querySelectorAll('[data-auto-dismiss]').forEach((el) => {
            setTimeout(() => el.remove(), 5000);
        });
    },
};

document.addEventListener('DOMContentLoaded', () => App.init());
