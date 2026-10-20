<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('order.php');
}
csrf_check();

$name        = trim($_POST['name'] ?? '');
$email       = trim($_POST['email'] ?? '');
$phone       = trim($_POST['phone'] ?? '');
$style       = trim($_POST['style'] ?? '');
$colors      = trim($_POST['colors'] ?? '');
$dimensions  = trim($_POST['dimensions'] ?? '');
$description = trim($_POST['description'] ?? '');

$errors = [];
if ($name === '') $errors[] = 'نام الزامی است.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'ایمیل معتبر نیست.';
if ($description === '') $errors[] = 'توضیحات سفارش الزامی است.';

if ($errors) {
    set_flash('error', implode(' | ', $errors));
    redirect('order.php');
}

// ذخیره در دیتابیس
$stmt = db()->prepare("
    INSERT INTO custom_orders
    (customer_name, customer_email, customer_phone, preferred_style, preferred_colors, dimensions, description)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");
$stmt->execute([$name, $email, $phone, $style, $colors, $dimensions, $description]);

// ارسال ایمیل با PHPMailer (بدون Composer)
$src = __DIR__ . '/lib/PHPMailer/src/';
if (SMTP_HOST && file_exists($src . 'PHPMailer.php')) {
    require_once $src . 'Exception.php';
    require_once $src . 'PHPMailer.php';
    require_once $src . 'SMTP.php';

    try {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = SMTP_SECURE === 'ssl'
            ? \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS
            : \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom(SMTP_USER, SITE_NAME);
        $mail->addAddress(ADMIN_EMAIL);
        $mail->addReplyTo($email, $name);
        $mail->isHTML(true);
        $mail->Subject = 'سفارش جدید از سایت: ' . $name;
        $mail->Body    = '
            <div style="font-family:Tahoma;direction:rtl">
            <h3>سفارش اختصاصی جدید</h3>
            <p><strong>نام:</strong> ' . e($name) . '</p>
            <p><strong>ایمیل:</strong> ' . e($email) . '</p>
            <p><strong>تلفن:</strong> ' . e($phone) . '</p>
            <p><strong>سبک:</strong> ' . e($style) . '</p>
            <p><strong>رنگ‌بندی:</strong> ' . e($colors) . '</p>
            <p><strong>ابعاد:</strong> ' . e($dimensions) . '</p>
            <p><strong>توضیحات:</strong><br>' . nl2br(e($description)) . '</p>
            </div>
        ';
        $mail->send();
    } catch (\Throwable $e) {
        // خطا را لاگ کن (سفارش در دیتابیس ذخیره شده و از دست نمی‌رود)
        error_log('PHPMailer error: ' . $mail->ErrorInfo);
    }
}

set_flash('success', 'سفارش شما ثبت شد. به‌زودی برای هماهنگی با شما تماس گرفته می‌شود.');
redirect('order.php');