<?php
require_once __DIR__ . '/auth.php';
require_admin();

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$art = [
    'title' => '', 'category' => 'minimal', 'technique' => '',
    'dimensions' => '', 'year_created' => '', 'description' => '',
    'main_image' => '', 'is_featured' => 0,
];
$images = [];

if ($id) {
    $stmt = db()->prepare("SELECT * FROM artworks WHERE id = ?");
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) exit('اثر یافت نشد.');
    $art = $found;

    $stmt = db()->prepare("SELECT * FROM artwork_images WHERE artwork_id = ?");
    $stmt->execute([$id]);
    $images = $stmt->fetchAll();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete_gallery') {
    csrf_check();
    $imgId = (int)($_POST['image_id'] ?? 0);
    $stmt = db()->prepare("SELECT * FROM artwork_images WHERE id = ? AND artwork_id = ?");
    $stmt->execute([$imgId, $id]);
    $img = $stmt->fetch();
    if ($img) {
        if (file_exists(UPLOAD_DIR . $img['image_path'])) @unlink(UPLOAD_DIR . $img['image_path']);
        db()->prepare("DELETE FROM artwork_images WHERE id = ?")->execute([$imgId]);
    }
    set_flash('success', 'تصویر حذف شد.');
    redirect('artwork_edit.php?id=' . $id);
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save') {
    csrf_check();
    $title = trim($_POST['title'] ?? '');
    $category = $_POST['category'] ?? 'minimal';
    $technique = trim($_POST['technique'] ?? '');
    $dimensions = trim($_POST['dimensions'] ?? '');
    $year = trim($_POST['year_created'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $mainImage = $art['main_image'] ?? '';

    if ($title === '') $error = 'عنوان الزامی است.';
    if (!isset(get_categories()[$category])) $category = 'minimal';

    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        [$ok, $result] = upload_image($_FILES['main_image'], 'art');
        if ($ok) $mainImage = $result;
        else $error = $result;
    }
    if (!$mainImage) $error = 'تصویر اصلی الزامی است.';

    if (!$error) {
        if ($id) {
            $stmt = db()->prepare("UPDATE artworks SET title=?, category=?, technique=?, dimensions=?, year_created=?, description=?, main_image=?, is_featured=? WHERE id=?");
            $stmt->execute([$title, $category, $technique, $dimensions, $year ?: null, $description, $mainImage, $isFeatured, $id]);
        } else {
            $stmt = db()->prepare("INSERT INTO artworks (title, category, technique, dimensions, year_created, description, main_image, is_featured) VALUES (?,?,?,?,?,?,?,?)");
            $stmt->execute([$title, $category, $technique, $dimensions, $year ?: null, $description, $mainImage, $isFeatured]);
            $id = (int)db()->lastInsertId();
        }

        if (!empty($_FILES['gallery']['name'][0])) {
            $names = upload_images_array($_FILES['gallery'], 'gal');
            $stmt = db()->prepare("INSERT INTO artwork_images (artwork_id, image_path) VALUES (?, ?)");
            foreach ($names as $name) {
                $stmt->execute([$id, $name]);
            }
        }

        set_flash('success', 'اثر ذخیره شد.');
        redirect('artworks.php');
    }
}

$pageTitle = $id ? 'ویرایش اثر' : 'افزودن اثر';
include __DIR__ . '/_header.php';
?>
<h1 class="section-title"><?= e($pageTitle) ?></h1>
<?php if ($error): ?><div class="flash error"><?= e($error) ?></div><?php endif; ?>

<form class="form-card" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?= $id ?>">

    <label>عنوان اثر *</label>
    <input type="text" name="title" value="<?= e($art['title']) ?>" required>

    <div class="form-row">
        <div>
            <label>دسته‌بندی</label>
            <select name="category">
                <?php foreach (get_categories() as $key => $label): ?>
                    <option value="<?= e($key) ?>" <?= $art['category'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label>تکنیک</label>
            <input type="text" name="technique" value="<?= e($art['technique']) ?>">
        </div>
    </div>

    <div class="form-row">
        <div>
            <label>ابعاد</label>
            <input type="text" name="dimensions" value="<?= e($art['dimensions']) ?>">
        </div>
        <div>
            <label>سال خلق</label>
            <input type="number" name="year_created" value="<?= e($art['year_created']) ?>" min="1300" max="1500">
        </div>
    </div>

    <label>توضیحات</label>
    <textarea name="description"><?= e($art['description']) ?></textarea>

    <label style="margin-top:14px">تصویر اصلی <?= $id ? '(در صورت انتخاب، جایگزین می‌شود)' : '*' ?></label>
    <input type="file" name="main_image" accept="image/jpeg,image/png,image/webp">
    <?php if (!empty($art['main_image'])): ?>
        <p><img src="../<?= e(UPLOAD_URL . $art['main_image']) ?>" alt="" style="width:180px;border-radius:12px"></p>
    <?php endif; ?>

    <label>تصاویر تکمیلی (چند فایل)</label>
    <input type="file" name="gallery[]" accept="image/jpeg,image/png,image/webp" multiple>

    <label style="margin-top:14px"><input type="checkbox" name="is_featured" value="1" <?= $art['is_featured'] ? 'checked' : '' ?>> نمایش در اسلایدر آثار شاخص</label>

    <p><button class="btn" type="submit">ذخیره</button> <a class="btn btn-outline" href="artworks.php">بازگشت</a></p>
</form>

<?php if ($images): ?>
    <h2 class="section-title" style="margin-top:40px">تصاویر تکمیلی</h2>
    <div class="grid">
        <?php foreach ($images as $img): ?>
            <div class="card">
                <img src="../<?= e(UPLOAD_URL . $img['image_path']) ?>" alt="">
                <div class="card-body">
                    <form method="post" onsubmit="return confirm('حذف شود؟')">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="delete_gallery">
                        <input type="hidden" name="id" value="<?= $id ?>">
                        <input type="hidden" name="image_id" value="<?= (int)$img['id'] ?>">
                        <button class="btn btn-small btn-danger" type="submit">حذف تصویر</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php include __DIR__ . '/_footer.php'; ?>