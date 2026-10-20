<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';
$pageTitle = 'تماس';
include __DIR__ . '/header.php';
?>
<section class="section">
    <div class="container prose">
        <h1>تماس با من</h1>
        <p>برای سفارش اختصاصی، هماهنگی نمایشگاه یا هر پرسش هنری می‌توانید از راه‌های زیر در ارتباط باشید.</p>
        <ul>
            <li>اینستاگرام: <a href="https://instagram.com/art.nooshin" target="_blank" rel="noopener">صفحه اینستاگرام نوشین</a></li>
            <li>ایمیل: <a href="mailto:<?= e(ADMIN_EMAIL) ?>"><?= e(ADMIN_EMAIL) ?></a></li>
        </ul>
        <p>برای ثبت سفارش، سریع‌تر از فرم <a href="order.php">سفارش اختصاصی</a> استفاده کنید.</p>
    </div>
</section>
<?php include __DIR__ . '/footer.php'; ?>