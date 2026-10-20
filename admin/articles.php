<?php
require_once __DIR__ . '/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    csrf_check();
    $id = (int)($_POST['id'] ?? 0);
    $stmt = db()->prepare("SELECT cover_image FROM articles WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row && $row['cover_image'] && file_exists(UPLOAD_DIR . $row['cover_image'])) {
        @unlink(UPLOAD_DIR . $row['cover_image']);
    }
    db()->prepare("DELETE FROM articles WHERE id = ?")->execute([$id]);
    set_flash('success', 'مقاله حذف شد.');
    redirect('articles.php');
}

$articles = db()->query("SELECT * FROM articles ORDER BY created_at DESC")->fetchAll();
$pageTitle = 'مدیریت مقالات';
include __DIR__ . '/_header.php';
?>
<h1 class="section-title">مقالات</h1>
<p><a class="btn" href="article_edit.php">افزودن مقاله جدید</a></p>
<table class="admin-table">
    <thead><tr><th>عنوان</th><th>اسلاگ</th><th>تاریخ</th><th>عملیات</th></tr></thead>
    <tbody>
    <?php foreach ($articles as $article): ?>
        <tr>
            <td><?= e($article['title']) ?></td>
            <td><?= e($article['slug']) ?></td>
            <td><?= e(date('Y/m/d', strtotime($article['created_at']))) ?></td>
            <td class="admin-actions">
                <a class="btn btn-small btn-outline" href="article_edit.php?id=<?= (int)$article['id'] ?>">ویرایش</a>
                <form method="post" onsubmit="return confirm('حذف شود؟')">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= (int)$article['id'] ?>">
                    <button class="btn btn-small btn-danger" type="submit">حذف</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php include __DIR__ . '/_footer.php'; ?>