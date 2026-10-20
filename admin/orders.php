<?php
require_once __DIR__ . '/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'status') {
    csrf_check();
    $id = (int)($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? 'pending';
    $allowed = ['pending', 'reviewed', 'accepted', 'completed'];
    if (in_array($status, $allowed, true)) {
        db()->prepare("UPDATE custom_orders SET status = ? WHERE id = ?")->execute([$status, $id]);
        set_flash('success', 'وضعیت سفارش به‌روزرسانی شد.');
    }
    redirect('orders.php');
}

$orders = db()->query("SELECT * FROM custom_orders ORDER BY created_at DESC")->fetchAll();
$labels = [
    'pending' => 'در انتظار بررسی',
    'reviewed' => 'بررسی‌شده',
    'accepted' => 'تأییدشده',
    'completed' => 'تکمیل‌شده',
];

$pageTitle = 'سفارش‌ها';
include __DIR__ . '/_header.php';
?>
<h1 class="section-title">سفارش‌های اختصاصی</h1>
<table class="admin-table">
    <thead><tr><th>تاریخ</th><th>نام</th><th>ایمیل</th><th>تلفن</th><th>سبک</th><th>توضیحات</th><th>وضعیت</th></tr></thead>
    <tbody>
    <?php foreach ($orders as $order): ?>
        <tr>
            <td><?= e(date('Y/m/d H:i', strtotime($order['created_at']))) ?></td>
            <td><?= e($order['customer_name']) ?></td>
            <td><?= e($order['customer_email']) ?></td>
            <td><?= e($order['customer_phone']) ?></td>
            <td><?= e($order['preferred_style']) ?></td>
            <td style="max-width:320px"><?= nl2br(e($order['description'])) ?></td>
            <td>
                <form method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="status">
                    <input type="hidden" name="id" value="<?= (int)$order['id'] ?>">
                    <select name="status">
                        <?php foreach ($labels as $key => $label): ?>
                            <option value="<?= e($key) ?>" <?= $order['status'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-small" type="submit">ثبت</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php include __DIR__ . '/_footer.php'; ?>