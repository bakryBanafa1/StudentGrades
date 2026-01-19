<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول | نظام الدرجات</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="login-page">

    <div class="login-container">
        <div class="logo-container">
            <img src="assets/images/11.jpg" alt="شعار الجامعة" class="university-logo">
        </div>
        
        <h1>نظام نتائج الطلاب</h1>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="error-message">
                <?php
                if ($_GET['error'] == "empty") {
                    echo "الرجاء إدخال جميع البيانات";
                } elseif ($_GET['error'] == "invalid") {
                    echo "رقم القيد أو كلمة المرور غير صحيحة";
                }
                ?>
            </div>
        <?php endif; ?>

        <form action="auth/login.php" method="POST" onsubmit="return validateLogin()">
            <div class="form-group">
                <label for="student_id">رقم القيد</label>
                <input type="text" id="student_id" name="student_id" placeholder="أدخل رقم القيد" required>
            </div>
            
            <div class="form-group">
                <label for="password">كلمة المرور</label>
                <input type="password" id="password" name="password" placeholder="أدخل كلمة المرور" required>
            </div>
            
            <button type="submit" class="btn-login">دخول</button>
        </form>
        
        <div class="footer-note">
            <p>جميع الحقوق محفوظة &copy; <?php echo date("Y"); ?></p>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>
