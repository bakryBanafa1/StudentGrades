<?php
/**
 * Database Connection
 * Configures the connection to the MySQL database.
 */

$host = 'localhost';
$dbname = 'student_grades';
$username = 'root'; // Default XAMPP username
$password = '';     // Default XAMPP password (empty)

try {
    $conn = new mysqli($host, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Set character set to utf8mb4 for Arabic support
    $conn->set_charset("utf8mb4");

} catch (Exception $e) {
    die("Database Connection Error: " . $e->getMessage());
}
?>
