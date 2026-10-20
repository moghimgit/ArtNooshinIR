<?php
require_once __DIR__ . '/auth.php';
require_admin();

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$article = ['title' => '', 'slug' => '', 'content' => '', 'cover_image' => ''];
if ($id) {
    $stmt = db()->prepare("SELECT * FROM articles WHERE id = ?");
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) exit('مقاله یافت نشد.');
    $article = $found;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save') {
    csrf_check();
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $cover = $article['cover_image'] ?? '';

    if ($title === '') $error = 'عنوان الزامی است.';
    if ($content === '') $error = 'متن مقاله الزامی است.';
    if ($slug === '') $slug = slugify($title);

    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        [$ok, $result] = upload_image($_FILES['cover_image'], 'cover');
        if ($ok) $cover = $result;
        else $error = $result;
    }

    if (!$error) {
        try {
            if ($id) {
                $stmt = db()->prepare("UPDATE articles SET title=?, slug=?, content=?, cover_image=? WHERE id=?");
                $stmt->execute([$title, $slug, $content, $cover, $id]);
            } else {
                $stmt = db()->prepare("INSERT INTO articles (title, slug, content, cover_image) VALUES (?,?,?,?)");
                $stmt->execute([$title, $slug, $content, $cover]);
            }
            set_flash('success', 'مقاله ذخیره شد.');
            redirect('articles.php');
        } catch (PDOException $e) {
            $error = 'اسلاگ تکراری است یا خطایی رخ داد.';
        }
    }
}

$pageTitle = $id ? 'ویرایش مقاله' : 'افزودن مقاله';
include __DIR__ . '/_header.php';
?>
<h1 class="section-title"><?= e($pageTitle) ?></h1>
<?php if ($error): ?><div class="flash error"><?= e($error) ?></div><?php endif; ?>

<form class="form-card" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?= $id ?>">

    <label>عنوان *</label>
    <input type="text" name="title" value="<?= e($article['title']) ?>" required>

    <label>اسلاگ (اختیاری)</label>
    <input type="text" name="slug" value="<?= e($article['slug']) ?>" placeholder="مثلاً minimal-art">

    <label>متن مقاله *</label>
    <textarea name="content" style="min-height:280px" required><?= e($article['content']) ?></textarea>

    <label>تصویر کاور</label>
    <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp">
    <?php if ($article['cover_image']): ?>
        <p><img src="../<?= e(UPLOAD_URL . $article['cover_image']) ?>" alt="" style="width:180px;border-radius:12px"></p>
    <?php endif; ?>

    <p><button class="btn" type="submit">ذخیره</button> <a class="btn btn-outline" href="articles.php">بازگشت</a></p>
</form>
<?php include __DIR__ . '/_footer.php'; ?>