<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('DB_HOST', 'localhost');
define('DB_NAME', 'pofilmoi_art');
define('DB_USER', 'pofilmoi_artuser');
define('DB_PASS', 'Nooshin@@1367');

define('SITE_NAME', 'نوش آرت');
define('ADMIN_EMAIL', 'info@artnooshin.ir');
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('UPLOAD_URL', 'uploads/');

// SMTP اختیاری برای PHPMailer
// SMTP
define('SMTP_HOST', 'mail.artnooshin.ir');
define('SMTP_USER', 'hello@artnooshin.ir');
define('SMTP_PASS', '3s4GjqR2q1GbfMO1');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');

// ایمیل مدیر (همان ایمیل هاست، یا ایمیل شخصی نوشین)
define('ADMIN_EMAIL', 'hello@artnooshin.ir');

date_default_timezone_set('Asia/Tehran');