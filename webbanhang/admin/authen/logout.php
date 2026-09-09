<?php
session_start();
$logoutRole = (int)($_SESSION['role_id'] ?? 2);
$rememberToken = $_COOKIE['remember_token'] ?? '';
if ($rememberToken !== '') {
    require_once('../../database/dbhelper.php');
    $conn = getDB();
    $tokenHash = hash('sha256', $rememberToken);
    $deleteToken = mysqli_prepare($conn, 'DELETE FROM auth_tokens WHERE token_hash = ?');
    if ($deleteToken) {
        mysqli_stmt_bind_param($deleteToken, 's', $tokenHash);
        mysqli_stmt_execute($deleteToken);
        mysqli_stmt_close($deleteToken);
    }
}

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}

session_destroy();
setcookie('remember_token', '', time() - 3600, '/');
header('Location: ' . ($logoutRole === 1 ? 'admin_login.php' : 'loginregister.php'));
exit;