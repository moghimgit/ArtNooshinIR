<?php
require_once __DIR__ . '/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    csrf_check();
    $id = (int)($_POST['id'] ?? 0);
    $stmt = db()->prepare("SELECT main_image FROM artworks WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row && $row['main_image'] && file_exists(UPLOAD_DIR . $row['main_image'])) {
        @unlink(UPLOAD_DIR . $row['main_image']);
    }
    $stmt = db()->prepare("DELETE FROM artworks WHERE id = ?");
    $stmt->execute([$id]);
    set_flash('success', 'اثر حذف شد.');
    redirect('artworks.php');
}

$artworks = db()->query("SELECT * FROM artworks ORDER BY created_at DESC")->fetchAll();
$pageTitle = 'مدیریت آثار';
include __DIR__ . '/_header.php';
?>
<h1 class="section-title">آثار</h1>
<p><a class="btn" href="artwork_edit.php">افزودن اثر جدید</a></p>
<table class="admin-table">
    <thead><tr><th>تصویر</th><th>عنوان</th><th>دسته</th><th>شاخص</th><th>عملیات</th></tr></thead>
    <tbody>
    <?php foreach ($artworks as $art): ?>
        <tr>
            <td><img src="../<?= e(UPLOAD_URL . $art['main_image']) ?>" alt="" style="width:80px;height:60px;object-fit:cover;border-radius:8px"></td>
            <td><?= e($art['title']) ?></td>
            <td><?= e(category_label($art['category'])) ?></td>
            <td><?= $art['is_featured'] ? 'بله' : 'خیر' ?></td>
            <td class="admin-actions">
                <a class="btn btn-small btn-outline" href="artwork_edit.php?id=<?= (int)$art['id'] ?>">ویرایش</a>
                <form method="post" onsubmit="return confirm('حذف شود؟')">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= (int)$art['id'] ?>">
                    <button class="btn btn-small btn-danger" type="submit">حذف</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php include __DIR__ . '/_footer.php'; ?>