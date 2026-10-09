<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Counterpart POS') ?> | Counterpart</title>
    <meta name="description" content="A secure POS customer and staff account ledger.">
    <link rel="stylesheet" href="<?= base_url('assets/app.css') ?>">
    <script src="<?= base_url('assets/app.js') ?>" defer></script>
</head>
<body>
<a class="skip-link" href="#main">Skip to content</a>
<?= $this->include('partials/header') ?>
<main id="main" class="shell main-content"><?= $this->include('partials/flash') ?><?= $this->renderSection('content') ?></main>
<footer class="site-footer"><div class="shell"><strong>Counterpart POS</strong><span>Educational account-management system</span></div></footer>
</body>
</html>
