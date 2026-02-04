<?php
/**
 * Database Setup Script
 * يتم تشغيل هذا السكريبت تلقائياً لإنشاء قاعدة البيانات واستيرادها
 */

require_once __DIR__ . '/db.php';

function setupDatabase($conn) {
    $setupFlagFile = __DIR__ . '/.db_setup_complete';
    
    // التحقق من أن قاعدة البيانات تم إعدادها مسبقاً
    if (file_exists($setupFlagFile)) {
        return ['status' => 'already_exists', 'message' => 'قاعدة البيانات تم إعدادها مسبقاً'];
    }
    
    try {
        // التحقق من وجود الجداول
        $result = $conn->query("SHOW TABLES");
        $tableCount = $result->num_rows;
        
        if ($tableCount > 0) {
            // الجداول موجودة، تم إنشاء ملف العلامة
            file_put_contents($setupFlagFile, date('Y-m-d H:i:s'));
            return ['status' => 'exists', 'message' => 'قاعدة البيانات موجودة مسبقاً'];
        }
        
        // قراءة ملف SQL
        $sqlFile = dirname(__DIR__) . '/DataBase.sql';
        
        if (!file_exists($sqlFile)) {
            return ['status' => 'error', 'message' => 'ملف DataBase.sql غير موجود'];
        }
        
        $sqlContent = file_get_contents($sqlFile);
        
        if ($sqlContent === false) {
            return ['status' => 'error', 'message' => 'فشل في قراءة ملف DataBase.sql'];
        }
        
        // إزالة التعليقات والأسطر الفارغة
        $sqlContent = preg_replace('/^--.*$/m', '', $sqlContent);
        $sqlContent = preg_replace('/^\/\*.*?\*\//ms', '', $sqlContent);
        
        // تقسيم الاستعلامات
        $queries = explode(';', $sqlContent);
        
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        
        // تعطيل فحص المفاتيح الأجنبية مؤقتاً
        $conn->query("SET FOREIGN_KEY_CHECKS=0");
        $conn->query("SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO'");
        
        foreach ($queries as $query) {
            $query = trim($query);
            
            // تجاهل الاستعلامات الفارغة
            if (empty($query)) {
                continue;
            }
            
            // تجاهل استعلامات إنشاء قاعدة البيانات واستخدامها
            if (stripos($query, 'CREATE DATABASE') !== false || 
                stripos($query, 'USE ') === 0) {
                continue;
            }
            
            if ($conn->query($query) === TRUE) {
                $successCount++;
            } else {
                $errorCount++;
                $errors[] = [
                    'query' => substr($query, 0, 100) . '...',
                    'error' => $conn->error
                ];
                
                // إذا كان الخطأ ليس بسبب وجود الجدول مسبقاً، سجل الخطأ
                if (stripos($conn->error, 'already exists') === false) {
                    error_log("SQL Error: " . $conn->error . " | Query: " . substr($query, 0, 200));
                }
            }
        }
        
        // إعادة تفعيل فحص المفاتيح الأجنبية
        $conn->query("SET FOREIGN_KEY_CHECKS=1");
        
        // إنشاء ملف علامة للإشارة إلى اكتمال الإعداد
        file_put_contents($setupFlagFile, date('Y-m-d H:i:s'));
        
        return [
            'status' => 'success',
            'message' => "تم استيراد قاعدة البيانات بنجاح",
            'success_count' => $successCount,
            'error_count' => $errorCount,
            'errors' => $errors
        ];
        
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => 'خطأ أثناء إعداد قاعدة البيانات: ' . $e->getMessage()
        ];
    }
}

// تشغيل الإعداد إذا تم استدعاء هذا الملف مباشرة
if (php_sapi_name() === 'cli' || basename($_SERVER['PHP_SELF']) === 'setup.php') {
    $result = setupDatabase($conn);
    
    if (php_sapi_name() === 'cli') {
        // عرض النتيجة في سطر الأوامر
        echo "=== Database Setup ===\n";
        echo "Status: " . $result['status'] . "\n";
        echo "Message: " . $result['message'] . "\n";
        
        if (isset($result['success_count'])) {
            echo "Successful queries: " . $result['success_count'] . "\n";
            echo "Failed queries: " . $result['error_count'] . "\n";
        }
        
        if (!empty($result['errors'])) {
            echo "\nErrors:\n";
            foreach ($result['errors'] as $error) {
                echo "- " . $error['error'] . "\n";
            }
        }
    } else {
        // عرض النتيجة في المتصفح
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
    
    exit($result['status'] === 'error' ? 1 : 0);
}
?>
