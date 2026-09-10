<?php require_once __DIR__ . '/../error_handler.php'; ?>
<?php
  session_start();
  require_once '../database/config.php';


  if ($_SERVER['REQUEST_METHOD'] === 'POST' && function_exists('csrf_verify') && !csrf_verify()) {
    http_response_code(403);
    die('Invalid request.');
  }

  if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
  }

  $pdo = getConnection();
  include 'data.php';

  function findProductForOrder($products, $id) {
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
    $product = findProductForOrder($products, $buyNow['id']);
    if (!$product) {
      header("Location: shop.php");
      exit;
    }
    $qty = $buyNow['qty'];
    $priceNumber = (float) str_replace(["₱", ","], "", $product['price']);
    $subtotal += $priceNumber * $qty;
    $lineItems[] = ['id' => $buyNow['id'], 'name' => $product['name'], 'price' => $priceNumber, 'qty' => $qty];
  } else {
    $cartItems = $_SESSION['cart'] ?? [];
    if (empty($cartItems)) {
      header("Location: cart.php");
      exit;
    }
    foreach ($cartItems as $id => $qty) {
      $product = findProductForOrder($products, $id);
      if ($product) {
        $priceNumber = (float) str_replace(["₱", ","], "", $product['price']);
        $subtotal += $priceNumber * $qty;
        $lineItems[] = ['id' => $id, 'name' => $product['name'], 'price' => $priceNumber, 'qty' => $qty];
      }
    }
  }

  $address = trim($_POST['address'] ?? '');
  $paymentMethod = $_POST['payment_method'] ?? '';

  if ($address === '' || $paymentMethod === '') {
    header("Location: checkout.php");
    exit;
  }

  $_SESSION['pending_order'] = [
    'address' => $address,
    'payment_method' => $paymentMethod,
  ];

  if ($paymentMethod === 'GCash') {
    header("Location: gcash_payment.php");
    exit;
  }

  createOrder($pdo, $address, $paymentMethod, $lineItems, $subtotal, $isBuyNow);
  exit;

 
  function createOrder($pdo, $address, $paymentMethod, $lineItems, $subtotal, $isBuyNow) {
    require_once __DIR__ . '/inventory_log.php';

    $shipping = 120.00;
    $total = $subtotal + $shipping;

    $pdo->beginTransaction();

    try {
        $insertOrder = $pdo->prepare("INSERT INTO orders (user_id, address, payment_method, total) VALUES (?, ?, ?, ?)");
        $insertOrder->execute([$_SESSION['user_id'], $address, $paymentMethod, $total]);
        $orderId = $pdo->lastInsertId();

        $insertItem = $pdo->prepare("INSERT INTO order_items (order_id, product_name, price, quantity) VALUES (?, ?, ?, ?)");
        foreach ($lineItems as $item) {
            $insertItem->execute([$orderId, $item['name'], $item['price'], $item['qty']]);
        }

        $insufficient = [];

        foreach ($lineItems as $item) {
            $productId = (int) str_replace('p', '', $item['id']);

            $current = $pdo->prepare("SELECT stock FROM products WHERE id = ? FOR UPDATE");
            $current->execute([$productId]);
            $previousStock = (int) $current->fetchColumn();

            $deduct = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
            $deduct->execute([$item['qty'], $productId, $item['qty']]);

            if ($deduct->rowCount() > 0) {
                logInventoryChange(
                    $pdo,
                    $productId,
                    'sale',
                    -$item['qty'],
                    $previousStock,
                    $previousStock - $item['qty'],
                    $orderId,
                    $isBuyNow ? 'Buy Now order placed' : 'Order placed'
                );
            } else {
              
                $insufficient[] = $item['name'];
            }
        }

        if (!empty($insufficient)) {
            $pdo->rollBack();
            $_SESSION['checkout_error'] = "Sorry, some items sold out while you were checking out: "
                . implode(', ', array_map('htmlspecialchars', $insufficient))
                . ". Please review your order below.";
            header("Location: checkout.php");
            exit;
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
        error_log("place_order.php createOrder failed: " . $e->getMessage());
        $_SESSION['checkout_error'] = "Something went wrong placing your order. Please try again.";
        header("Location: checkout.php");
        exit;
    }
}
?>