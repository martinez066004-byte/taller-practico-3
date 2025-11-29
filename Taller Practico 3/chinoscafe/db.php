<?php
// db.php - Conexión local + remota (replicación integrada)
if (session_status() === PHP_SESSION_NONE) session_start();

// LOCAL (XAMPP)
$DB_LOCAL = [
    'host' => '127.0.0.1',
    'port' => 3306,
    'user' => 'root',
    'pass' => '',
    'name' => 'chinoscafe'
];

// REMOTO (VM)
$DB_REMOTE = [
    'host' => '192.168.56.101',
    'port' => 3306,
    'user' => 'syncuser',
    'pass' => 'SyncPass123!',
    'name' => 'chinoscafe'
];

// conexión local
$mysqli = new mysqli(
    $DB_LOCAL['host'],
    $DB_LOCAL['user'],
    $DB_LOCAL['pass'],
    $DB_LOCAL['name'],
    $DB_LOCAL['port']
);
if ($mysqli->connect_error) {
    die("❌ Error conexión local: " . $mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');

// conexión remota (no fatal)
$mysqli_remote = @new mysqli(
    $DB_REMOTE['host'],
    $DB_REMOTE['user'],
    $DB_REMOTE['pass'],
    $DB_REMOTE['name'],
    $DB_REMOTE['port']
);
if ($mysqli_remote && !$mysqli_remote->connect_error) {
    $mysqli_remote->set_charset('utf8mb4');
} else {
    $mysqli_remote = null;
}
?>