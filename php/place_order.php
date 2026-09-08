<?php
session_start();
require_once '../database/config.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$buyNow = $_SESSION['buy_now'] ?? null;
$isBuyNow = $buyNow !== null;

if (!$isBuyNow && empty($_SESSION['cart'])) {
  header("Location: cart.php");
  exit;
}

$address = trim($_POST['address'] ?? '');
$paymentMethod = $_POST['payment_method'] ?? '';

if ($address === '' || $paymentMethod === '') {
  header("Location: checkout.php");
  exit;
}

$pdo = getConnection();

// Build the list of what we're trying to buy, from whichever source
// (Buy Now reservation or the regular cart) applies to this checkout.
$requestedItems = [];

if ($isBuyNow) {
  $productId = (int) str_replace('p', '', $buyNow['id']);
  $requestedItems[] = ['product_id' => $productId, 'qty' => (int) $buyNow['qty']];
} else {
  foreach ($_SESSION['cart'] as $id => $qty) {
    $productId = (int) str_replace('p', '', $id);
    $requestedItems[] = ['product_id' => $productId, 'qty' => (int) $qty];
  }
}

$pdo->beginTransaction();

try {
  $lineItems = [];
  $subtotal = 0;
  $insufficient = [];

  foreach ($requestedItems as $item) {
   
    $stmt = $pdo->prepare("SELECT name, price, stock FROM products WHERE id = ? FOR UPDATE");
    $stmt->execute([$item['product_id']]);
    $product = $stmt->fetch();

    if (!$product || $product['stock'] < $item['qty']) {
      $insufficient[] = $product['name'] ?? 'An item';
      continue;
    }

    $update = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
    $update->execute([$item['qty'], $item['product_id']]);

    $priceNumber = (float) $product['price'];
    $lineItems[] = ['name' => $product['name'], 'price' => $priceNumber, 'qty' => $item['qty']];
    $subtotal += $priceNumber * $item['qty'];
  }

  if (!empty($insufficient)) {
    $pdo->rollBack();
    $_SESSION['checkout_error'] = "Sorry, some items sold out while you were checking out: "
      . implode(', ', array_map('htmlspecialchars', $insufficient))
      . ". Please review your order below.";
    header("Location: checkout.php");
    exit;
  }

  $shipping = 120.00;
  $total = $subtotal + $shipping;

  $insertOrder = $pdo->prepare("INSERT INTO orders (user_id, address, payment_method, total) VALUES (?, ?, ?, ?)");
  $insertOrder->execute([$_SESSION['user_id'], $address, $paymentMethod, $total]);
  $orderId = $pdo->lastInsertId();

  $insertItem = $pdo->prepare("INSERT INTO order_items (order_id, product_name, price, quantity) VALUES (?, ?, ?, ?)");
  foreach ($lineItems as $item) {
    $insertItem->execute([$orderId, $item['name'], $item['price'], $item['qty']]);
  }

  $updateAddress = $pdo->prepare("UPDATE accounts SET address = ? WHERE id = ?");
  $updateAddress->execute([$address, $_SESSION['user_id']]);

  $pdo->commit();

  // Clear only the source this order actually came from — a Buy Now
  // purchase should never wipe out unrelated items in the regular bag.
  if ($isBuyNow) {
    unset($_SESSION['buy_now']);
  } else {
    unset($_SESSION['cart']);
  }

  header("Location: order_success.php?id=" . $orderId);
  exit;

} catch (Exception $e) {
  $pdo->rollBack();
  $_SESSION['checkout_error'] = "Something went wrong placing your order. Please try again.";
  header("Location: checkout.php");
  exit;
}
?>