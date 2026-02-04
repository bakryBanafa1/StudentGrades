<?php
$host = getenv('DB_HOST');
$dbname = getenv('DB_DATABASE');
$username = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');
$port = getenv('DB_PORT') ?: 3306;

if (!$host || !$dbname || !$username) {
    die("❌ Database ENV variables not set");
}

$conn = new mysqli($host, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    error_log("Database Connection Error: " . $conn->connect_error); // تسجيل الخطأ
    die("Database Error");
}

$conn->set_charset("utf8mb4");
?>
