<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

$slides = db()->query("SELECT * FROM artworks WHERE is_featured = 1 ORDER BY created_at DESC LIMIT 5")->fetchAll();
if (!$slides) {
    $slides = db()->query("SELECT * FROM artworks ORDER BY created_at DESC LIMIT 5")->fetchAll();
}
$latest = db()->query("SELECT * FROM artworks ORDER BY created_at DESC LIMIT 6")->fetchAll();

$pageTitle = 'خانه';
include __DIR__ . '/header.php';
?>
<section class="hero">
    <div class="container">
        <h1>خلق آثاری با محوریت احساس، سادگی و زیبایی</h1>
        <p>من نوشین انیسی هستم؛ هنرمند نقاشی که در زمینه خلق تابلوهای دکوراتیو، آثار مینیمال عاشقانه و نقاشی با تکنیک‌های متنوع فعالیت می‌کنم.</p>
        <a class="btn" href="gallery.php">مشاهده گالری آثار</a>
        <a class="btn btn-outline" href="order.php">ثبت سفارش اختصاصی</a>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2 class="section-title">آثار شاخص</h2>
        <p class="section-sub">منتخبی از تابلوهایی که از نظر اجرا، ترکیب‌بندی و هویت هنری نماینده کار من هستند.</p>
        <?php if ($slides): ?>
            <div class="slider" id="heroSlider">
                <?php foreach ($slides as $i => $art): ?>
                    <a class="slide <?= $i === 0 ? 'active' : '' ?>" href="artwork.php?id=<?= (int)$art['id'] ?>">
                        <img src="<?= e(UPLOAD_URL . $art['main_image']) ?>" alt="<?= e($art['title']) ?>">
                        <div class="slide-caption">
                            <h3><?= e($art['title']) ?></h3>
                            <p><?= e(category_label($art['category'])) ?> — <?= e($art['technique']) ?></p>
                        </div>
                    </a>
                <?php endforeach; ?>
                <button class="slider-btn prev" type="button">‹</button>
                <button class="slider-btn next" type="button">›</button>
            </div>
        <?php else: ?>
            <div class="empty">هنوز اثری ثبت نشده است.</div>
        <?php endif; ?>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2 class="section-title">آخرین آثار</h2>
        <p class="section-sub">جدیدترین تابلوهای اضافه‌شده به گالری</p>
        <div class="grid">
            <?php foreach ($latest as $art): ?>
                <a class="card" href="artwork.php?id=<?= (int)$art['id'] ?>">
                    <img src="<?= e(UPLOAD_URL . $art['main_image']) ?>" alt="<?= e($art['title']) ?>">
                    <div class="card-body">
                        <h3><?= e($art['title']) ?></h3>
                        <p class="meta"><?= e(category_label($art['category'])) ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container prose">
        <h2>درباره من</h2>
        <p>در آثارم تلاش می‌کنم با ترکیب رنگ، بافت و فرم، ایده‌ها و احساسات را به آثاری منحصربه‌فرد تبدیل کنم؛ آثاری که بتوانند بخشی از هویت و زیبایی فضای زندگی شما باشند.</p>
        <p><a class="btn btn-outline" href="about.php">بیشتر درباره نوشین بخوانید</a></p>
    </div>
</section>
<?php include __DIR__ . '/footer.php'; ?>