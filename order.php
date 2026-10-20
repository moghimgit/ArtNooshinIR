<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';
$pageTitle = 'سفارش اختصاصی';
include __DIR__ . '/header.php';
?>
<section class="section">
    <div class="container">
        <h1 class="section-title">سفارش تابلوی اختصاصی</h1>
        <p class="section-sub">اثری متناسب با سلیقه شما؛ گاهی یک تابلو فقط برای تکمیل دکوراسیون انتخاب نمی‌شود، بلکه قرار است حس، خاطره یا داستانی شخصی را در خود داشته باشد.</p>

        <form class="form-card" method="post" action="process_order.php">
            <?= csrf_field() ?>
            <div class="form-row">
                <div>
                    <label for="name">نام و نام خانوادگی *</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div>
                    <label for="email">ایمیل *</label>
                    <input type="email" id="email" name="email" required>
                </div>
            </div>

            <div class="form-row">
                <div>
                    <label for="phone">شماره تماس</label>
                    <input type="text" id="phone" name="phone">
                </div>
                <div>
                    <label for="style">سبک مورد نظر</label>
                    <select id="style" name="style">
                        <option value="">انتخاب کنید</option>
                        <option value="مینیمال عاشقانه">مینیمال عاشقانه</option>
                        <option value="دکوراتیو و بافت‌دار">دکوراتیو و بافت‌دار</option>
                        <option value="خلاقانه">خلاقانه</option>
                        <option value="سایر">سایر</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div>
                    <label for="colors">رنگ‌بندی دلخواه</label>
                    <input type="text" id="colors" name="colors" placeholder="مثلاً کرم، خاکستری گرم، سبز زیتونی">
                </div>
                <div>
                    <label for="dimensions">ابعاد موردنظر</label>
                    <input type="text" id="dimensions" name="dimensions" placeholder="مثلاً ۷۰×۱۰۰ سانتی‌متر">
                </div>
            </div>

            <label for="description">توضیحات سفارش *</label>
            <textarea id="description" name="description" required placeholder="ایده، فضا، حس مورد نظر و هر جزئیاتی که مهم است..."></textarea>

            <p><button class="btn" type="submit">ثبت سفارش</button></p>
        </form>
    </div>
</section>
<?php include __DIR__ . '/footer.php'; ?>