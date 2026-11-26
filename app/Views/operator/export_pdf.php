<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; }
        .kop { border-bottom: 2px solid #136B3D; padding-bottom: 8px; margin-bottom: 14px; }
        .kop h2 { margin: 0; color: #136B3D; font-size: 15px; }
        .kop p { margin: 3px 0 0; font-size: 11px; color: #4b5563; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #d1d5db; padding: 6px 8px; }
        th { background-color: #136B3D; color: #ffffff; text-align: left; }
        td.num, th.num { text-align: center; }
        .footer { margin-top: 24px; text-align: right; font-size: 11px; }
    </style>
</head>
<body>
    <div class="kop">
        <h2>SD MUHAMMADIYAH 25 MEDAN</h2>
        <p>Laporan Rekapitulasi Absensi Guru &mdash; Periode: <?= esc($bulanLabel) ?> (Kehadiran <?= (int) $overallPct ?>%)</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="num">No</th>
                <th>NIP</th>
                <th>Nama Guru</th>
                <th>Jabatan</th>
                <th class="num">Hadir</th>
                <th class="num">Terlambat</th>
                <th class="num">Izin</th>
                <th class="num">Sakit</th>
                <th class="num">Alpa</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rekap as $i => $r): ?>
                <tr>
                    <td class="num"><?= $i + 1 ?></td>
                    <td><?= esc($r['nip'] ?: '-') ?></td>
                    <td><?= esc($r['nama_lengkap']) ?></td>
                    <td><?= esc($r['jabatan'] ?: '-') ?></td>
                    <td class="num"><?= (int) $r['hadir'] ?></td>
                    <td class="num"><?= (int) $r['terlambat'] ?></td>
                    <td class="num"><?= (int) $r['izin'] ?></td>
                    <td class="num"><?= (int) $r['sakit'] ?></td>
                    <td class="num"><?= (int) $r['alpa'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        Medan, <?= esc(format_tanggal_id(date('Y-m-d'), false)) ?><br><br><br>
        <strong>Kepala Sekolah / Operator</strong>
    </div>
</body>
</html>
