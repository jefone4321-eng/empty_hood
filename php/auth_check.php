<?php
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
    require_once __DIR__ . '/../database/config.php';
    $pdo = getConnection();

    list($userId, $token) = array_pad(explode(':', $_COOKIE['remember_me'], 2), 2, null);

    if ($userId && $token) {
        $hashedToken = hash('sha256', $token);

        $stmt = $pdo->prepare("SELECT id, name, is_admin FROM accounts
                                WHERE id = ? AND remember_token = ? AND remember_expires > NOW()");
        $stmt->execute([$userId, $hashedToken]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['is_admin'] = $user['is_admin'];
        } else {
            // Invalid/expired cookie - clear it
            setcookie('remember_me', '', time() - 3600, '/');
        }
    }
}