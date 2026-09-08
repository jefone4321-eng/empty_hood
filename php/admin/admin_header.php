<?php
session_start();
if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    header("Location: admin_login.php");
    exit;
}
require_once __DIR__ . '/../../database/config.php';
$pdo = getConnection();

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — Empty Hood</title>
    <link rel="stylesheet" href="../../css/style-admin.css">
    <link rel="stylesheet" href="../../fontawesome/css/all.min.css">
</head>

<body class="admin-body">

    <div class="admin-layout">
        <aside class="admin-sidebar">
            <div class="admin-sidebar-brand">
                <img src="../../images/hLogo.svg" alt="EH">
                <span>EH ADMIN</span>
            </div>

            <nav class="admin-sidebar-nav">
                <a href="dashboard.php" class="<?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-gauge"></i> Dashboard
                </a>
                <a href="products.php"
                    class="<?php echo in_array($currentPage, ['products.php', 'product_form.php']) ? 'active' : ''; ?>">
                    <i class="fa-solid fa-shirt"></i> Products
                </a>
                <a href="customers.php" class="<?php echo $currentPage === 'customers.php' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-users"></i> Customers
                </a>
                <a href="reviews.php" class="<?php echo $currentPage === 'reviews.php' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-star"></i> Reviews
                </a>

                <a href="inventory.php" class="<?php echo $currentPage === 'inventory.php' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-boxes-stacked"></i> Inventory
                </a>

                <a href="orders.php"
                    class="<?php echo in_array($currentPage, ['orders.php', 'order_detail.php']) ? 'active' : ''; ?>">
                    <i class="fa-solid fa-chart-line"></i> Sales Report
                </a>
            </nav>

            <div class="admin-sidebar-footer">
                <a href="../index.php"><i class="fa-solid fa-arrow-left"></i> View Site</a>
                <a href="../logout.php"><i class="fa-solid fa-right-from-bracket"></i> Log Out</a>
            </div>
        </aside>

        <main class="admin-main">