<?php
session_start();
require_once __DIR__ . '/../database/config.php';
$pdo = getConnection();

$action = $_POST['action'] ?? 'add';
$id = $_POST['id'] ?? null;
$isAjax = isset($_POST['ajax']); // flag we'll send from JavaScript

if ($id) {
  $productId = (int) str_replace('p', '', $id);

  
  $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
  $stmt->execute([$productId]);
  $row = $stmt->fetch();
  $availableStock = $row ? (int) $row['stock'] : 0;

  switch ($action) {
    case 'add':
      $currentQty = $_SESSION['cart'][$id] ?? 0;
      if ($currentQty < $availableStock) {
        $_SESSION['cart'][$id] = $currentQty + 1;
      }
      break;

    case 'buy_now':
      
      $quantity = max(1, (int) ($_POST['quantity'] ?? 1));

      if ($quantity <= $availableStock) {
        $_SESSION['buy_now'] = ['id' => $id, 'qty' => $quantity];
      }
      break;

    case 'increase':
      $currentQty = $_SESSION['cart'][$id] ?? 0;
      if ($currentQty < $availableStock) {
        $_SESSION['cart'][$id] = $currentQty + 1;
      }
      break;

    case 'decrease':
      if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]--;
        if ($_SESSION['cart'][$id] <= 0) {
          unset($_SESSION['cart'][$id]);
        }
      }
      break;

    case 'remove':
      unset($_SESSION['cart'][$id]);
      break;
  }
}

if ($isAjax) {
  // Respond with JSON instead of redirecting
  $newQty = $_SESSION['cart'][$id] ?? 0;
  $cartCount = array_sum($_SESSION['cart'] ?? []);

  header('Content-Type: application/json');
  echo json_encode([
    'newQty' => $newQty,
    'cartCount' => $cartCount,
  ]);
  exit;
}

$redirectTo = $_POST['redirect'] ?? 'index.php';
header("Location: $redirectTo");
exit;
?>