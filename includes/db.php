<?php
// Database connection (PDO with prepared statements)
$DB_HOST = '127.0.0.1';
$DB_NAME = 'security_incident_db';
$DB_USER = 'root';
$DB_PASS = '';

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    die('DB connection failed: ' . htmlspecialchars($e->getMessage()));
}

// Ensure default admin (admin / admin123) exists with a valid hash
try {
    $stmt = $pdo->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->execute(['admin']);
    $row = $stmt->fetch();
    $needsHash = !$row || !password_verify('admin123', $row['password']);
    if (!$row) {
        $pdo->prepare("INSERT INTO users (username, password, full_name, role) VALUES (?,?,?,?)")
            ->execute(['admin', password_hash('admin123', PASSWORD_DEFAULT), 'System Administrator', 'admin']);
    } elseif ($needsHash) {
        $pdo->prepare("UPDATE users SET password=? WHERE id=?")
            ->execute([password_hash('admin123', PASSWORD_DEFAULT), $row['id']]);
    }
} catch (Exception $e) { /* ignore on first install */ }
