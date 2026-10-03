<?php
include 'admin_header.php';

$productCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$customerCount = $pdo->query("SELECT COUNT(*) FROM accounts")->fetchColumn();
$reviewCount = $pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn();
$inventoryCount = $pdo->query("SELECT SUM(stock) FROM products")->fetchColumn() ?? 0;
$totalRevenue = $pdo->query("SELECT SUM(total) FROM orders")->fetchColumn() ?? 0;
$totalSales = $pdo->query("SELECT SUM(total) FROM orders WHERE status != 'Cancelled'")->fetchColumn() ?? 0;
?>

<h1>Dashboard</h1>
<p style="color:#888; margin-bottom:28px;">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>.</p>

<div class="admin-stats">
    <div class="admin-stat">
  n.ss="admin-stat-value"><?php echo $productCount; ?></div>
        <div class="admin-stat-label">Total Products</div>
    </div>
    <div class="admin-stat">
        <div class="admin-stat-value"><?php echo $customerCount; ?></div>
        <div class="admin-stat-label">Total Customers</div>
    </div>
    <div class="admin-stat">
        <div class="admin-stat-value"><?php echo $reviewCount; ?></div>
        <div class="admin-stat-label">Total Reviews</div>
    </div>
    <div class="admin-stat">
        <div class="admin-stat-value"><?php echo $inventoryCount; ?></div>
        <div class="admin-stat-label">Total Inventory</div>
    </div>
    <div class="admin-stat">
        <div class="admin-stat-value">₱<?php echo number_format($totalSales, 2); ?></div>
        <div class="admin-stat-label">Total Sales</div>
    </div>
</div>

<div class="admin-cards">
    <a href="products.php" class="admin-card">
        <div class="admin-card-icon"><i class="fa-solid fa-shirt"></i></div>
        <h2>Products</h2>
        <p>Add, edit, or remove items from the store</p>
    </a>
    <a href="customers.php" class="admin-card">
        <div class="admin-card-icon"><i class="fa-solid fa-users"></i></div>
        <h2>Customers</h2>
        <p>View and manage registered accounts</p>
    </a>
    <a href="reviews.php" class="admin-card">
        <div class="admin-card-icon"><i class="fa-solid fa-star"></i></div>
        <h2>Reviews</h2>
        <p>Moderate customer reviews</p>
    </a>
    <a href="inventory.php" class="admin-card">
        <div class="admin-card-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
        <h2>Inventory</h2>
        <p>Update stock levels for each product</p>
    </a>
    <a href="orders.php" class="admin-card">
        <div class="admin-card-icon"><i class="fa-solid fa-chart-line"></i></div>
        <h2>Sales Report</h2>
        <p>View orders, revenue, and manage order status</p>
    </a>
</div>

<?php include 'admin_footer.php'; ?>