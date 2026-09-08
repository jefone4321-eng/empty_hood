<?php
  include 'admin_header.php';

  $orderId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

  $stmt = $pdo->prepare("
    SELECT orders.*, accounts.name AS customer_name, accounts.email AS customer_email
    FROM orders JOIN accounts ON orders.user_id = accounts.id
    WHERE orders.id = ?
  ");
  $stmt->execute([$orderId]);
  $order = $stmt->fetch();

  if (!$order) {
    header("Location: orders.php");
    exit;
  }

  $itemsStmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
  $itemsStmt->execute([$orderId]);
  $items = $itemsStmt->fetchAll();
?>

<h1>Order #<?php echo $order['id']; ?></h1>
<p style="color:#888; margin-bottom:24px;"><a href="orders.php" style="color:#8c9eff;">← Back to Orders</a></p>

<div class="admin-form" style="max-width:600px;">
    <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['customer_name']); ?> (<?php echo htmlspecialchars($order['customer_email']); ?>)</p>
    <p><strong>Date:</strong> <?php echo date("M j, Y g:i A", strtotime($order['created_at'])); ?></p>
    <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
    <p><strong>Payment:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
    <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>

    <hr style="border-color:#2a2a2a; margin:20px 0;">

    <?php foreach ($items as $item): ?>
        <div class="checkout-item">
            <div>
                <p class="checkout-item-name"><?php echo htmlspecialchars($item['product_name']); ?> × <?php echo $item['quantity']; ?></p>
            </div>
            <p class="checkout-item-price">₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
        </div>
    <?php endforeach; ?>

    <div class="checkout-totals">
        <p class="checkout-grand-total"><span>Total: </span><span>₱<?php echo number_format($order['total'], 2); ?></span></p>
    </div>
</div>

<?php include 'admin_footer.php'; ?>