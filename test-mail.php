<?php
// test_mail.php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/lib/PHPMailer/src/Exception.php';
require_once __DIR__ . '/lib/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/lib/PHPMailer/src/SMTP.php';

$mail = new \PHPMailer\PHPMailer\PHPMailer(true);
$mail->SMTPDebug = 2; // نمایش کامل مکالمه با سرور (برای تست)
$mail->Debugoutput = 'html';

try {
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

    $mail->setFrom(SMTP_USER, 'نوشین آرت');
    $mail->addAddress(SMTP_USER); // به خودت ایمیل تستی می‌فرستد
    $mail->Subject = 'تست SMTP سایت';
    $mail->Body    = 'اگر این ایمیل را می‌بینید، تنظیمات SMTP درست است ✅';
    $mail->send();
    echo '<h3 style="color:green">✅ ایمیل با موفقیت ارسال شد.</h3>';
} catch (\Throwable $e) {
    echo '<h3 style="color:red">❌ خطا در ارسال</h3>';
    echo '<p>ErrorInfo: ' . htmlspecialchars($mail->ErrorInfo) . '</p>';
}