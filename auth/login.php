<?php
/**
 * Login Logic
 * Handles student authentication.
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

    // Direct comparison as requested (Plaintext password)
    // Using prepared statements to prevent SQL injection despite plaintext passwords
    $stmt = $conn->prepare("SELECT id, name_ar FROM students WHERE student_id = ? AND password = ?");
    $stmt->bind_param("ss", $student_id, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        
        // Login Success
        $_SESSION['student_id'] = $student_id;
        $_SESSION['student_name'] = $row['name_ar']; // Store name for display
        
        header("Location: ../pages/grades.php");
        exit();
    } else {
        // Login Failed
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
