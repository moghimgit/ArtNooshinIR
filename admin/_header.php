<?php require_once __DIR__ . '/auth.php'; ?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle ?? 'مدیریت') ?> | مدیریت سایت</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<nav class="admin-nav">
    <strong>مدیریت نوشین آرت</strong>
    <a href="index.php">داشبورد</a>
    <a href="artworks.php">آثار</a>
    <a href="articles.php">مقالات</a>
    <a href="orders.php">سفارش‌ها</a>
    <span class="spacer"></span>
    <a href="../index.php" target="_blank">مشاهده سایت</a>
    <a href="logout.php">خروج</a>
</nav>
<main class="admin-main">
<?php $flash = get_flash(); ?>
<?php if ($flash): ?>
    <div class="flash <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
<?php endif; ?>