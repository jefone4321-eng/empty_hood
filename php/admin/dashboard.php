<?php
  include 'admin_header.php';

  $productCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
  $customerCount = $pdo->query("SELECT COUNT(*) FROM accounts")->fetchColumn();
  $reviewCount = $pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn();
?>

<h1>Dashboard</h1>
<p style="color:#888; margin-bottom:28px;">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>.</p>

<div class="admin-stats">
    <div class="admin-stat">
        <div class="admin-stat-value"><?php echo $productCount; ?></div>
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

    
</div>

<?php include 'admin_footer.php'; ?>