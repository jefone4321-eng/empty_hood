<?php
  include 'admin_header.php';

 if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $orderId = $_POST['order_id'];
    $newStatus = $_POST['status'];

    // Check the order's current status before changing it
    $current = $pdo->prepare("SELECT status FROM orders WHERE id = ?");
    $current->execute([$orderId]);
    $oldStatus = $current->fetchColumn();

    // If it's being cancelled now, and wasn't already cancelled, return the stock
    if ($newStatus === 'Cancelled' && $oldStatus !== 'Cancelled') {
        $items = $pdo->prepare("SELECT product_name, quantity FROM order_items WHERE order_id = ?");
        $items->execute([$orderId]);

        foreach ($items->fetchAll() as $item) {
            $restock = $pdo->prepare("UPDATE products SET stock = stock + ? WHERE name = ?");
            $restock->execute([$item['quantity'], $item['product_name']]);
        }
    }

    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$newStatus, $orderId]);

    header("Location: orders.php");
    exit;
}
  // Summary stats
  $totalRevenue = $pdo->query("SELECT SUM(total) FROM orders")->fetchColumn() ?? 0;
  $totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
  $pendingCount = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'Pending'")->fetchColumn();
  $todayRevenue = $pdo->query("SELECT SUM(total) FROM orders WHERE DATE(created_at) = CURDATE()")->fetchColumn() ?? 0;
  $totalRevenue = $pdo->query("SELECT SUM(total) FROM orders WHERE status != 'Cancelled'")->fetchColumn() ?? 0;
  $todayRevenue = $pdo->query("
    SELECT SUM(total) FROM orders
    WHERE DATE(created_at) = CURDATE() AND status != 'Cancelled'
")->fetchColumn() ?? 0;

  
  $orders = $pdo->query("
    SELECT orders.*, accounts.name AS customer_name, accounts.email AS customer_email
    FROM orders
    JOIN accounts ON orders.user_id = accounts.id
    ORDER BY orders.created_at DESC
  ")->fetchAll();

  $statusOptions = ["Pending", "Processing", "Shipped", "Delivered", "Cancelled"];
?>

<h1>Sales Report</h1>
<p style="color:#888; margin-bottom:24px;">Overview of orders and revenue.</p>

<div class="admin-stats">
    <div class="admin-stat">
        <div class="admin-stat-value">₱<?php echo number_format($totalRevenue, 2); ?></div>
        <div class="admin-stat-label">Total Revenue</div>
    </div>
    <div class="admin-stat">
        <div class="admin-stat-value">₱<?php echo number_format($todayRevenue, 2); ?></div>
        <div class="admin-stat-label">Revenue Today</div>
    </div>
    <div class="admin-stat">
        <div class="admin-stat-value"><?php echo $totalOrders; ?></div>
        <div class="admin-stat-label">Total Orders</div>
    </div>
    <div class="admin-stat">
        <div class="admin-stat-value"><?php echo $pendingCount; ?></div>
        <div class="admin-stat-label">Pending Orders</div>
    </div>
</div>

<div class="admin-table-wrapper">
<table class="admin-table">
    <tr>
        <th>Order #</th>
        <th>Customer</th>
        <th>Date</th>
        <th>Payment</th>
        <th>Total</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    <?php if (empty($orders)): ?>
        <tr><td colspan="7" class="admin-empty">No orders yet.</td></tr>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td>#<?php echo $order['id']; ?></td>
                <td>
                    <?php echo htmlspecialchars($order['customer_name']); ?><br>
                    <span style="color:#777; font-size:0.78rem;"><?php echo htmlspecialchars($order['customer_email']); ?></span>
                </td>
                <td><?php echo date("M j, Y", strtotime($order['created_at'])); ?></td>
                <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                <td>₱<?php echo number_format($order['total'], 2); ?></td>
                <td>
                    <form method="post" style="display:flex; gap:6px; align-items:center;">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <select name="status" onchange="this.form.submit()"
                                style="background:#0d0d0d; border:1px solid #2a2a2a; color:#fff; padding:6px; border-radius:4px; font-size:0.8rem;">
                            <?php foreach ($statusOptions as $status): ?>
                                <option value="<?php echo $status; ?>" <?php echo $order['status'] === $status ? 'selected' : ''; ?>>
                                    <?php echo $status; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </td>
                <td><a href="order_detail.php?id=<?php echo $order['id']; ?>">View</a></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</table>
</div>

<?php include 'admin_footer.php'; ?>