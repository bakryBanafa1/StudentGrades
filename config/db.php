<?php
/**
 * Database Connection Test (Coolify)
 */

$host = getenv('DB_HOST');
$dbname = getenv('DB_DATABASE');
$username = getenv('DB_USERNAME');
$password = getenv('DB_PASSWORD');
$port = getenv('DB_PORT') ?: 3306;

if (!$host || !$dbname || !$username) {
    die("❌ Database ENV variables not set");
}

try {
    $conn = new mysqli($host, $username, $password, $dbname, $port);

    if ($conn->connect_error) {
        throw new Exception($conn->connect_error);
    }

    $conn->set_charset("utf8mb4");

    echo "✅ Connected to Database Successfully";

} catch (Exception $e) {
    echo "❌ Database Connection Failed: " . $e->getMessage();
}
?>
