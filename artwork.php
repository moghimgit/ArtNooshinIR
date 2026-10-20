<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare("SELECT * FROM artworks WHERE id = ?");
$stmt->execute([$id]);
$art = $stmt->fetch();

if (!$art) {
    http_response_code(404);
    exit('اثر مورد نظر یافت نشد.');
}

$imgStmt = db()->prepare("SELECT * FROM artwork_images WHERE artwork_id = ?");
$imgStmt->execute([$id]);
$images = $imgStmt->fetchAll();

$relStmt = db()->prepare("SELECT * FROM artworks WHERE category = ? AND id != ? ORDER BY created_at DESC LIMIT 3");
$relStmt->execute([$art['category'], $id]);
$related = $relStmt->fetchAll();

$pageTitle = $art['title'];
include __DIR__ . '/header.php';
?>
<section class="container artwork-layout">
    <div>
        <div class="artwork-main">
            <img id="mainArtworkImage" src="<?= e(UPLOAD_URL . $art['main_image']) ?>" alt="<?= e($art['title']) ?>">
        </div>
        <?php if ($images): ?>
            <div class="gallery-thumbs">
                <?php foreach ($images as $img): ?>
                    <img class="thumb" src="<?= e(UPLOAD_URL . $img['image_path']) ?>" alt="جزئیات اثر">
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <aside class="artwork-info prose">
        <h1><?= e($art['title']) ?></h1>
        <p class="meta"><?= e(category_label($art['category'])) ?></p>
        <ul class="artwork-details">
            <?php if ($art['technique']): ?><li><span>تکنیک</span><strong><?= e($art['technique']) ?></strong></li><?php endif; ?>
            <?php if ($art['dimensions']): ?><li><span>ابعاد</span><strong><?= e($art['dimensions']) ?></strong></li><?php endif; ?>
            <?php if ($art['year_created']): ?><li><span>سال خلق</span><strong><?= e($art['year_created']) ?></strong></li><?php endif; ?>
        </ul>
        <?php if ($art['description']): ?>
            <p><?= nl2br(e($art['description'])) ?></p>
        <?php endif; ?>
        <p><a class="btn" href="order.php">ثبت سفارش مشابه / اختصاصی</a></p>
    </aside>
</section>

<?php if ($related): ?>
<section class="section">
    <div class="container">
        <h2 class="section-title">آثار مرتبط</h2>
        <div class="grid">
            <?php foreach ($related as $rel): ?>
                <a class="card" href="artwork.php?id=<?= (int)$rel['id'] ?>">
                    <img src="<?= e(UPLOAD_URL . $rel['main_image']) ?>" alt="<?= e($rel['title']) ?>">
                    <div class="card-body"><h3><?= e($rel['title']) ?></h3></div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<script>
document.querySelectorAll('.thumb').forEach(img => {
    img.addEventListener('click', () => {
        const main = document.getElementById('mainArtworkImage');
        if (main) main.src = img.src;
    });
});
</script>
<?php include __DIR__ . '/footer.php'; ?>