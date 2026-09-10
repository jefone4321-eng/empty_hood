<?php
  session_start();
  require_once __DIR__ .'/csrf.php';

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_verify()) {
  http_response_code(403);
  die('Invalid request.');
}

  if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    header("Location: ../login.php");
    exit;
  }
  require_once __DIR__ . '/../../database/config.php';
  $pdo = getConnection();

  $id = $_POST['id'] ?? null;
  if ($id && $id != $_SESSION['user_id']) {
    $stmt = $pdo->prepare("DELETE FROM accounts WHERE id = ?");
    $stmt->execute([$id]);
  }

  header("Location: customers.php");
  exit;
?>