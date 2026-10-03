<?php
session_start();

if (isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/../database/config.php';
    $pdo = getConnection();
    $pdo->prepare("UPDATE accounts SET remember_token = NULL, remember_expires = NULL WHERE id = ?")
        ->execute([$_SESSION['user_id']]);
}

session_destroy();
setcookie('remember_me', '', time() - 3600, '/');

header("Location: index.php");
exit;
?>