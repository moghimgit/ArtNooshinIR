<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

$articles = db()->query("SELECT * FROM articles ORDER BY created_at DESC")->fetchAll();
$pageTitle = 'آموزش و مقالات';
include __DIR__ . '/header.php';
?>
<section class="section">
    <div class="container">
        <h1 class="section-title">آموزش و مقالات هنری</h1>
        <p class="section-sub">نکته‌ها، تجربه‌ها و اطلاعاتی درباره سبک‌های مختلف هنری</p>
        <?php if ($articles): ?>
            <div class="grid">
                <?php foreach ($articles as $article): ?>
                    <a class="card" href="article.php?slug=<?= e($article['slug']) ?>">
                        <?php if ($article['cover_image']): ?>
                            <img src="<?= e(UPLOAD_URL . $article['cover_image']) ?>" alt="<?= e($article['title']) ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <h3><?= e($article['title']) ?></h3>
                            <p class="meta"><?= e(date('Y/m/d', strtotime($article['created_at']))) ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty">هنوز مقاله‌ای منتشر نشده است.</div>
        <?php endif; ?>
    </div>
</section>
<?php include __DIR__ . '/footer.php'; ?>