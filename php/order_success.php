<?php
  session_start();
  require_once '../database/config.php';

  if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
  }

  $orderId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

  if (!$orderId) {
    header("Location: index.php");
    exit;
  }

  $pdo = getConnection();

  $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
  $stmt->execute([$orderId, $_SESSION['user_id']]);
  $order = $stmt->fetch();

  if (!$order) {
    header("Location: index.php");
    exit;
  }

  $itemsStmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
  $itemsStmt->execute([$orderId]);
  $items = $itemsStmt->fetchAll();

  $cartCount = array_sum($_SESSION['cart'] ?? []);
  $navStyle = "";
  include 'header.php';
?>

<section class="shop-page checkout-page">
  <div class="order-success">
    <i class="fa-solid fa-circle-check order-success-icon"></i>
    <h1>Order Placed!</h1>
    <p>Thanks for your order — here's your confirmation.</p>

    <div class="checkout-summary" style="text-align:left; max-width:500px; margin:32px auto 0;">
      <p><strong>Order #<?php echo $order['id']; ?></strong></p>
      <p style="color:#999;">Payment: <?php echo htmlspecialchars($order['payment_method']); ?></p>
      <p style="color:#999; margin-bottom:16px;">Delivering to: <?php echo htmlspecialchars($order['address']); ?></p>

      <?php foreach ($items as $item): ?>
        <div class="checkout-item">
          <div>
            <p class="checkout-item-name"><?php echo htmlspecialchars($item['product_name']); ?> × <?php echo $item['quantity']; ?></p>
          </div>
          <p class="checkout-item-price">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
        </div>
      <?php endforeach; ?>

      <div class="checkout-totals">
        <p class="checkout-grand-total"><span>Total Paid</span><span>₱<?php echo number_format($order['total'], 2); ?></span></p>
      </div>
    </div>

    <a href="shop.php" class="btn-primary" style="margin-top:32px; display:inline-block;">CONTINUE SHOPPING →</a>
  </div>
</section>

<?php include 'footer.php'; ?>