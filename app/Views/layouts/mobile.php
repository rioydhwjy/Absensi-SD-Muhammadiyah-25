<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="base-url" content="<?= base_url() ?>">
    <meta name="X-CSRF-TOKEN" content="<?= csrf_hash() ?>">
    <meta name="X-CSRF-NAME" content="<?= csrf_token() ?>">
    <title><?= esc($title ?? 'Absensi SD Muhammadiyah 25') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/custom.css') ?>">
</head>
<body>
<div class="sdm-shell <?= ! empty($darkShell) ? 'sdm-shell-dark' : '' ?>">
    <?= $this->renderSection('content') ?>
</div>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
