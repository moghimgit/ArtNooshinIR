<?php $active = $active ?? basename($_SERVER['PHP_SELF']); ?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle ?? SITE_NAME) ?> | <?= e(SITE_NAME) ?></title>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="index.php">نوشین انیسی<span>نقاشی و هنرهای دکوراتیو</span></a>
        <button class="nav-toggle" id="navToggle" aria-label="منو">☰</button>
        <nav class="main-nav" id="mainNav">
            <a class="<?= $active === 'index.php' ? 'active' : '' ?>" href="index.php">خانه</a>
            <a class="<?= $active === 'gallery.php' ? 'active' : '' ?>" href="gallery.php">گالری</a>
            <a class="<?= $active === 'about.php' ? 'active' : '' ?>" href="about.php">درباره نوشین</a>
            <a class="<?= $active === 'order.php' ? 'active' : '' ?>" href="order.php">سفارش اختصاصی</a>
            <a class="<?= $active === 'articles.php' ? 'active' : '' ?>" href="articles.php">آموزش و مقالات</a>
            <a class="<?= $active === 'contact.php' ? 'active' : '' ?>" href="contact.php">تماس</a>
        </nav>
    </div>
</header>
<main>
<?php $flash = get_flash(); ?>
<?php if ($flash): ?>
    <div class="container">
        <div class="flash <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
    </div>
<?php endif; ?>