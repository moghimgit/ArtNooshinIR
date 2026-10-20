require_once __DIR__ . '/lib/simple_smtp.php';
$mail->addAddress(ADMIN_EMAIL);
$mail->addCC('noshin-personal@gmail.com', 'نوشین');
if (SMTP_HOST) {
    @send_smtp_mail(
        ADMIN_EMAIL,
        'سفارش جدید از سایت: ' . $name,
        '<h3>سفارش جدید</h3><p>نام: ' . e($name) . '</p><p>توضیحات: ' . nl2br(e($description)) . '</p>',
        $email
    );
}