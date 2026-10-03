<?php
  session_start();
require_once __DIR__ . '/csrf.php';
if (!csrf_verify()) {
  http_response_code(403);
  echo json_encode(['success' => false, 'message' => 'Invalid request.']);
  exit;
}
require_once __DIR__ . '/../database/config.php';
  header('Content-Type: application/json');

  $email = trim($_POST['email'] ?? '');

  if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
  }

  $pdo = getConnection();

  $check = $pdo->prepare("SELECT id FROM newsletter_subscribers WHERE email = ?");
  $check->execute([$email]);

  if ($check->fetch()) {
    echo json_encode(['success' => false, 'message' => 'You\'re already subscribed!']);
    exit;
  }

  $insert = $pdo->prepare("INSERT INTO newsletter_subscribers (email) VALUES (?)");
  $insert->execute([$email]);

  echo json_encode(['success' => true, 'message' => 'You\'re in! Watch your inbox for new drops.']);
  exit;
?>