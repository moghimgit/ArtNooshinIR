<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

$slug = trim($_GET['slug'] ?? '');
$stmt = db()->prepare("SELECT * FROM articles WHERE slug = ?");
$stmt->execute([$slug]);
$article = $stmt->fetch();

if (!$article) {
    http_response_code(404);
    exit('مقاله مورد نظر یافت نشد.');
}

$pageTitle = $article['title'];
include __DIR__ . '/header.php';
?>
<section class="section">
    <div class="container prose">
        <h1><?= e($article['title']) ?></h1>
        <p class="meta"><?= e(date('Y/m/d', strtotime($article['created_at']))) ?></p>
        <?php if ($article['cover_image']): ?>
            <img src="<?= e(UPLOAD_URL . $article['cover_image']) ?>" alt="<?= e($article['title']) ?>" style="border-radius:20px;margin:20px 0">
        <?php endif; ?>
        <div><?= nl2br(e($article['content'])) ?></div>
        <p><a class="btn btn-outline" href="articles.php">بازگشت به مقالات</a></p>
    </div>
</section>
<?php include __DIR__ . '/footer.php'; ?>