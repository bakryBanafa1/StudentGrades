<?php
/**
 * Login Logic
 * Handles student authentication.
 * Updated to reverse password before comparison
 */

session_start();
require_once '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $password = $_POST['password'];

    if (empty($student_id) || empty($password)) {
        header("Location: ../index.php?error=empty");
        exit();
    }

    // عكس الباسوورد قبل التحقق (مثل النظام في Laravel)
    $reversedPassword = strrev($password);

    // استخدام prepared statements لمنع حقن SQL
    $stmt = $conn->prepare("SELECT id, name_ar FROM students WHERE student_id = ? AND password = ?");
    $stmt->bind_param("ss", $student_id, $reversedPassword);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        
        // تسجيل الدخول ناجح
        $_SESSION['student_id'] = $student_id;
        $_SESSION['student_name'] = $row['name_ar']; // تخزين الاسم للعرض
        
        header("Location: ../pages/grades.php");
        exit();
    } else {
        // تسجيل الدخول فاشل
        header("Location: ../index.php?error=invalid");
        exit();
    }
    
    $stmt->close();
    $conn->close();
} else {
    header("Location: ../index.php");
    exit();
}
?>