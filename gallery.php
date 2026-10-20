<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

$categories = get_categories();
$cat = $_GET['cat'] ?? '';
if ($cat && !isset($categories[$cat])) {
    $cat = '';
}

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 9;
$offset = ($page - 1) * $perPage;

$where = '';
$params = [];
if ($cat) {
    $where = 'WHERE category = :cat';
    $params[':cat'] = $cat;
}

$countStmt = db()->prepare("SELECT COUNT(*) FROM artworks $where");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();
$pages = max(1, (int)ceil($total / $perPage));

$stmt = db()->prepare("SELECT * FROM artworks $where ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$artworks = $stmt->fetchAll();

$pageTitle = 'گالری آثار';
include __DIR__ . '/header.php';
?>
<section class="section">
    <div class="container">
        <h1 class="section-title">گالری آثار</h1>
        <p class="section-sub">کاتالوگ تابلوهای دکوراتیو، مینیمال و تجربه‌های هنری</p>

        <div class="filters">
            <a class="<?= $cat === '' ? 'active' : '' ?>" href="gallery.php">همه</a>
            <?php foreach ($categories as $key => $label): ?>
                <a class="<?= $cat === $key ? 'active' : '' ?>" href="gallery.php?cat=<?= e($key) ?>"><?= e($label) ?></a>
            <?php endforeach; ?>
        </div>

        <?php if ($artworks): ?>
            <div class="grid">
                <?php foreach ($artworks as $art): ?>
                    <a class="card" href="artwork.php?id=<?= (int)$art['id'] ?>">
                        <img src="<?= e(UPLOAD_URL . $art['main_image']) ?>" alt="<?= e($art['title']) ?>">
                        <div class="card-body">
                            <h3><?= e($art['title']) ?></h3>
                            <p class="meta"><?= e(category_label($art['category'])) ?><?= $art['dimensions'] ? ' — ' . e($art['dimensions']) : '' ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if ($pages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $pages; $i++): ?>
                        <?php if ($i === $page): ?>
                            <span class="current"><?= $i ?></span>
                        <?php else: ?>
                            <a href="gallery.php?<?= $cat ? 'cat=' . e($cat) . '&' : '' ?>page=<?= $i ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty">در این دسته‌بندی اثری ثبت نشده است.</div>
        <?php endif; ?>
    </div>
</section>
<?php include __DIR__ . '/footer.php'; ?>