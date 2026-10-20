<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

$count = (int)db()->query("SELECT COUNT(*) FROM admin_users")->fetchColumn();
if ($count > 0) {
    exit('مدیر قبلاً ساخته شده است. این فایل را حذف کنید.');
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($username === '' || strlen($password) < 8) {
        $message = 'نام کاربری و رمز عبور حداقل ۸ کاراکتری الزامی است.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = db()->prepare("INSERT INTO admin_users (username, password_hash) VALUES (?, ?)");
        $stmt->execute([$username, $hash]);
        $message = 'مدیر ساخته شد. فایل create_admin.php را حذف کنید و از admin/login.php وارد شوید.';
    }
}
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head><meta charset="utf-8"><title>ساخت مدیر</title></head>
<body style="font-family:Tahoma;padding:30px">
<h2>ساخت مدیر اولیه</h2>
<?php if ($message): ?><p><?= e($message) ?></p><?php endif; ?>
<form method="post">
    <?= csrf_field() ?>
    <p><label>نام کاربری</label><br><input name="username" required></p>
    <p><label>رمز عبور</label><br><input type="password" name="password" required></p>
    <button type="submit">ساخت مدیر</button>
</form>
</body>
</html>