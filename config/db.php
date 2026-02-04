<?php
/**
 * Database Connection
 * Coolify / Production Ready
 */

$host = getenv('DB_HOST') ?: 'mysql';
$dbname = getenv('DB_DATABASE') ?: 'student_grades';
$username = getenv('DB_USERNAME') ?: 'user';
$password = getenv('DB_PASSWORD') ?: 'password';
$port = getenv('DB_PORT') ?: 3306;

try {
    $conn = new mysqli($host, $username, $password, $dbname, $port);

    if ($conn->connect_error) {
        throw new Exception($conn->connect_error);
    }

    $conn->set_charset("utf8mb4");

} catch (Exception $e) {
    die("Database Connection Error: " . $e->getMessage());
}
?>
