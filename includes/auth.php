<?php
// Session + auth helpers (CSRF + timeout)
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

define('SESSION_TIMEOUT', 1800); // 30 minutes

if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
    session_unset();
    session_destroy();
    header('Location: login.php?timeout=1');
    exit;
}
$_SESSION['last_activity'] = time();

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

function require_login() {
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}
function csrf_token() { return $_SESSION['csrf']; }
function csrf_check($token) { return hash_equals($_SESSION['csrf'] ?? '', $token ?? ''); }
function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
