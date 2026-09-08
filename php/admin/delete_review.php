<?php
  session_start();
  if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    header("Location: ../login.php");
    exit;
  }
  require_once __DIR__ . '/../../database/config.php';
  $pdo = getConnection();

  $id = $_POST['id'] ?? null;
  if ($id) {
    $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
    $stmt->execute([$id]);
  }

  header("Location: reviews.php");
  exit;
?>