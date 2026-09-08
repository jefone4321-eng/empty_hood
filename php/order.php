<?php
  session_start();
  require_once '../database/config.php';

  if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
  }

  $pdo = getConnection();

  $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
  $stmt->execute([$_SESSION['user_id']]);
  $orders = $stmt->fetchAll();

  $cartCount = array_sum($_SESSION['cart'] ?? []);
  $navStyle = "";
  include 'header.php';
?>

<section class="account-page">
  <div class="account-header">
    <div>
      <h1>Order History</h1>
      <p class="account-since"><a href="account.php">&larr; Back to Account</a></p>
    </div>
  </div>

  <div class="account-section">
    <?php if (empty($orders)): ?>
      <p style="color:#888;">You haven't placed any orders yet. <a href="shop.php">Start shopping →</a></p>
    <?php else: ?>
      <?php foreach ($orders as $order): ?>
        <div class="order-history-row">
          <div class="order-history-info">
            <p class="checkout-item-name">Order #<?php echo $order['id']; ?></p>
            <p class="account-since">
              <?php echo date("M j, Y g:i A", strtotime($order['created_at'])); ?>
              &middot; <?php echo htmlspecialchars($order['payment_method']); ?>
            </p>
          </div>

          <span class="order-status-badge order-status-<?php echo strtolower(htmlspecialchars($order['status'])); ?>">
            <?php echo htmlspecialchars($order['status']); ?>
          </span>

          <p class="order-history-total">₱<?php echo number_format($order['total'], 2); ?></p>

          <a href="order_success.php?id=<?php echo $order['id']; ?>" class="view-all">View Details →</a>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>

<?php include 'footer.php'; ?>