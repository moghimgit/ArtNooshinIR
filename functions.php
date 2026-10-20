<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_check(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        exit('درخواست نامعتبر است.');
    }
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function is_admin(): bool
{
    return !empty($_SESSION['admin_id']);
}

function require_admin(): void
{
    if (!is_admin()) {
        redirect('login.php');
    }
}

function category_label(string $category): string
{
    $labels = [
        'minimal' => 'مینیمال عاشقانه',
        'decorative' => 'دکوراتیو و بافت‌دار',
        'creative' => 'خلاقانه و سایر تکنیک‌ها',
    ];
    return $labels[$category] ?? $category;
}

function get_categories(): array
{
    return [
        'minimal' => 'مینیمال عاشقانه',
        'decorative' => 'دکوراتیو و بافت‌دار',
        'creative' => 'خلاقانه و سایر تکنیک‌ها',
    ];
}

function upload_image(array $file, string $prefix = 'img'): array
{
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return [false, 'خطا در آپلود فایل.'];
    }
    if ($file['size'] > 5 * 1024 * 1024) {
        return [false, 'حجم تصویر نباید بیشتر از ۵ مگابایت باشد.'];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    if (!isset($allowed[$mime])) {
        return [false, 'فرمت تصویر مجاز نیست. jpg، png یا webp استفاده کنید.'];
    }

    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }

    $name = $prefix . '_' . bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
    if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $name)) {
        return [false, 'انتقال فایل انجام نشد.'];
    }

    return [true, $name];
}

function upload_images_array(array $files, string $prefix = 'gal'): array
{
    $names = [];
    if (empty($files['name'][0])) {
        return $names;
    }

    foreach ($files['name'] as $i => $name) {
        if (($files['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        $single = [
            'name' => $name,
            'type' => $files['type'][$i],
            'tmp_name' => $files['tmp_name'][$i],
            'error' => $files['error'][$i],
            'size' => $files['size'][$i],
        ];
        [$ok, $result] = upload_image($single, $prefix);
        if ($ok) {
            $names[] = $result;
        }
    }
    return $names;
}

function slugify(string $text): string
{
    $text = trim($text);
    $text = preg_replace('/[^\p{L}\p{N}]+/u', '-', $text);
    $text = trim($text, '-');
    return $text ?: 'article-' . time();
}