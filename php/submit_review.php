<?php
  session_start();
  require_once '../database/config.php';

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_verify()) {
  http_response_code(403);
  die('Invalid request.');
}

  if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
  }

  $rating = (int) ($_POST['rating'] ?? 0);
  $reviewText = trim($_POST['review_text'] ?? '');

  if ($rating >= 1 && $rating <= 5 && $reviewText !== '') {
    $pdo = getConnection();
    $stmt = $pdo->prepare("INSERT INTO reviews (user_id, rating, review_text) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $rating, $reviewText]);
  }

  header("Location: index.php");
  exit;
?>