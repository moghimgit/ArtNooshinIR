<?php
require_once __DIR__ . '/auth.php';

if (is_admin()) {
    redirect('index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = db()->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        redirect('index.php');
    }
    $error = 'نام کاربری یا رمز عبور اشتباه است.';
}

$pageTitle = 'ورود مدیریت';
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ورود مدیریت</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<main class="container" style="max-width:460px;margin-top:70px">
    <div class="form-card">
        <h1 class="section-title">ورود به مدیریت</h1>
        <?php if ($error): ?><div class="flash error"><?= e($error) ?></div><?php endif; ?>
        <form method="post">
            <?= csrf_field() ?>
            <label for="username">نام کاربری</label>
            <input type="text" id="username" name="username" required>
            <label for="password" style="margin-top:14px">رمز عبور</label>
            <input type="password" id="password" name="password" required>
            <p><button class="btn" type="submit">ورود</button></p>
        </form>
    </div>
</main>
</body>
</html>