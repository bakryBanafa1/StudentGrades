<?php
/**
 * Auto-initialization script
 * يتم استدعاء هذا الملف تلقائياً لإعداد قاعدة البيانات
 */

// تشغيل سكريبت الإعداد
require_once __DIR__ . '/config/setup.php';

// محاولة إعداد قاعدة البيانات تلقائياً
$result = setupDatabase($conn);

// تسجيل النتيجة
error_log("Database setup result: " . $result['status'] . " - " . $result['message']);

// إذا كان هناك خطأ، اعرض رسالة للمستخدم
if ($result['status'] === 'error') {
    echo "<!DOCTYPE html>
<html dir='rtl' lang='ar'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>خطأ في الإعداد</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .error-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            max-width: 600px;
            text-align: center;
        }
        .error-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
        h1 {
            color: #e74c3c;
            margin: 0 0 20px 0;
        }
        p {
            color: #555;
            line-height: 1.6;
            margin: 10px 0;
        }
        .details {
            background: #f8f9fa;
            border-right: 4px solid #e74c3c;
            padding: 15px;
            margin: 20px 0;
            text-align: right;
            border-radius: 5px;
        }
        .retry-btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
            transition: background 0.3s;
        }
        .retry-btn:hover {
            background: #5568d3;
        }
    </style>
</head>
<body>
    <div class='error-container'>
        <div class='error-icon'>⚠️</div>
        <h1>خطأ في إعداد قاعدة البيانات</h1>
        <p>عذراً، حدث خطأ أثناء محاولة إعداد قاعدة البيانات.</p>
        <div class='details'>
            <strong>تفاصيل الخطأ:</strong><br>
            " . htmlspecialchars($result['message']) . "
        </div>
        <p>الرجاء التحقق من إعدادات قاعدة البيانات ومحاولة مرة أخرى.</p>
        <button class='retry-btn' onclick='location.reload()'>إعادة المحاولة</button>
    </div>
</body>
</html>";
    exit;
}

// إذا تم الإعداد بنجاح، توجيه إلى الصفحة الرئيسية
if ($result['status'] === 'success') {
    header('Location: index.php');
    exit;
}
?>
