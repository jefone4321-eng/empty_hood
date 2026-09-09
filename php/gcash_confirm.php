<?php
  session_start();
  require_once '../database/config.php';

  if (!isset($_SESSION['user_id']) || !isset($_SESSION['pending_order'])) {
    header("Location: cart.php");
    exit;
  }

  $address = $_SESSION['pending_order']['address'];
  $paymentMethod = $_SESSION['pending_order']['payment_method'];

  $pdo = getConnection();
  include 'data.php';

  function findProductForConfirm($products, $id) {
    foreach ($products as $product) {
      if ($product['id'] === $id) return $product;
    }
    return null;
  }

  $buyNow = $_SESSION['buy_now'] ?? null;
  $isBuyNow = $buyNow !== null;

  $lineItems = [];
  $subtotal = 0;

  if ($isBuyNow) {
    $product = findProductForConfirm($products, $buyNow['id']);
    if ($product) {
      $priceNumber = (float) str_replace(["₱", ","], "", $product['price']);
      $subtotal += $priceNumber * $buyNow['qty'];
      $lineItems[] = ['name' => $product['name'], 'price' => $priceNumber, 'qty' => $buyNow['qty']];
    }
  } else {
    $cartItems = $_SESSION['cart'] ?? [];
    foreach ($cartItems as $id => $qty) {
      $product = findProductForConfirm($products, $id);
      if ($product) {
        $priceNumber = (float) str_replace(["₱", ","], "", $product['price']);
        $subtotal += $priceNumber * $qty;
        $lineItems[] = ['name' => $product['name'], 'price' => $priceNumber, 'qty' => $qty];
      }
    }
  }

  $shipping = 120.00;
  $total = $subtotal + $shipping;

  $pdo->beginTransaction();

  try {

    $insertOrder = $pdo->prepare("INSERT INTO orders (user_id, address, payment_method, total, status) VALUES (?, ?, ?, ?, 'Processing')");
    $insertOrder->execute([$_SESSION['user_id'], $address, $paymentMethod, $total]);
    $orderId = $pdo->lastInsertId();

    $insertItem = $pdo->prepare("INSERT INTO order_items (order_id, product_name, price, quantity) VALUES (?, ?, ?, ?)");
    foreach ($lineItems as $item) {
      $insertItem->execute([$orderId, $item['name'], $item['price'], $item['qty']]);
    }

    $updateAddress = $pdo->prepare("UPDATE accounts SET address = ? WHERE id = ?");
    $updateAddress->execute([$address, $_SESSION['user_id']]);

    $pdo->commit();

    if ($isBuyNow) {
      unset($_SESSION['buy_now']);
    } else {
      unset($_SESSION['cart']);
    }
    unset($_SESSION['pending_order']);

    header("Location: order_success.php?id=" . $orderId);
    exit;

  } catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['checkout_error'] = "Something went wrong placing your order. Please try again.";
    header("Location: checkout.php");
    exit;
  }
?>