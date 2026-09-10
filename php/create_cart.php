 <?php require_once __DIR__ . '/error_handler.php'; ?>
<?php
session_start();
require_once __DIR__ . '/../database/config.php';
require_once __DIR__ .'/csrf.php';
require_once __DIR__ . '/inventory_log.php';
$pdo = getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_verify()) {
  http_response_code(403);
  die('Invalid request.');
}

$action = $_POST['action'] ?? 'add';
$id = $_POST['id'] ?? null;
$isAjax = isset($_POST['ajax']);

if ($id) {
  $productId = (int) str_replace('p', '', $id);

  switch ($action) {
    case 'add':
      $stmt = $pdo->prepare("UPDATE products SET stock = stock - 1 WHERE id = ? AND stock >= 1");
      $stmt->execute([$productId]);

      if ($stmt->rowCount() > 0) {
        $currentQty = $_SESSION['cart'][$id] ?? 0;
        $_SESSION['cart'][$id] = $currentQty + 1;
      }
      break;

    case 'add_multiple':
      $quantity = max(1, (int) ($_POST['quantity'] ?? 1));

      $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
      $stmt->execute([$quantity, $productId, $quantity]);

      if ($stmt->rowCount() > 0) {
        $currentQty = $_SESSION['cart'][$id] ?? 0;
        $_SESSION['cart'][$id] = $currentQty + $quantity;
      }
      break;

      case 'buy_now':
  $quantity = max(1, (int) ($_POST['quantity'] ?? 1));
  $_SESSION['buy_now'] = ['id' => $id, 'qty' => $quantity];
  break;

    case 'increase':
      $stmt = $pdo->prepare("UPDATE products SET stock = stock - 1 WHERE id = ? AND stock >= 1");
      $stmt->execute([$productId]);

      if ($stmt->rowCount() > 0) {
        $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
      }
      break;

    case 'decrease':
      if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]--;

        $update = $pdo->prepare("UPDATE products SET stock = stock + 1 WHERE id = ?");
        $update->execute([$productId]);

        if ($_SESSION['cart'][$id] <= 0) {
          unset($_SESSION['cart'][$id]);
        }
      }
      break;

    case 'remove':
      $qty = $_SESSION['cart'][$id] ?? 0;
      if ($qty > 0) {
        $update = $pdo->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
        $update->execute([$qty, $productId]);
      }
      unset($_SESSION['cart'][$id]);
      break;
  }
}

if ($isAjax) {
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