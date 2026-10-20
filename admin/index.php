<?php
require_once __DIR__ . '/auth.php';
require_admin();

$counts = [
    'artworks' => (int)db()->query("SELECT COUNT(*) FROM artworks")->fetchColumn(),
    'articles' => (int)db()->query("SELECT COUNT(*) FROM articles")->fetchColumn(),
    'orders' => (int)db()->query("SELECT COUNT(*) FROM custom_orders")->fetchColumn(),
    'pending' => (int)db()->query("SELECT COUNT(*) FROM custom_orders WHERE status = 'pending'")->fetchColumn(),
];

$pageTitle = 'داشبورد';
include __DIR__ . '/_header.php';
?>
<h1 class="section-title">داشبورد</h1>
<div class="grid">
    <div class="card"><div class="card-body"><h3>آثار</h3><p><?= $counts['artworks'] ?> اثر</p></div></div>
    <div class="card"><div class="card-body"><h3>مقالات</h3><p><?= $counts['articles'] ?> مقاله</p></div></div>
    <div class="card"><div class="card-body"><h3>سفارش‌ها</h3><p><?= $counts['orders'] ?> سفارش / <?= $counts['pending'] ?> در انتظار</p></div></div>
</div>
<?php include __DIR__ . '/_footer.php'; ?>