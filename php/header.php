<?php require_once __DIR__ . '/auth_check.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empty Hood</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style-base.css">
    <link rel="stylesheet" href="../css/style-about.css">
    <link rel="stylesheet" href="../css/style-responsive.css">
    <link rel="stylesheet" href="../css/style-account.css">
    <link rel="stylesheet" href="../css/style-auth.css">
    <link rel="stylesheet" href="../css/style-footer.css">
    <link rel="stylesheet" href="../css/style-hero.css">
    <link rel="stylesheet" href="../css/style-product.css">
    <link rel="stylesheet" href="../css/style-lookbook-ourStory-reviews.css">
    <link rel="stylesheet" href="../css/style-trust.css">
    <link rel="stylesheet" href="../css/style-nav.css">
    <link rel="stylesheet" href="../css/style-reviews.css">
    <link rel="stylesheet" href="../css/style-admin.css">
    <link rel="stylesheet" href="../css/checkout.css">
    <link rel="stylesheet" href="../css/buyNow.css">
    <link rel="stylesheet" href="../css/orderHistory.css">
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="https://cloudfare.com">
</head>

<body class="<?php echo $bodyClass ?? ''; ?>">

   <header class="nav <?php echo $navStyle ?? ''; ?>">
    <div class="nav-brand">
        <a href="index.php"><img src="../images/hLogo.svg"></a>
    </div>
    <nav class="nav-links">
        <a href="index.php">Home</a>
        <a href="shop.php">Shop</a>
        <a href="collections.php">Collections</a>
        <a href="about.php">About</a>
        <a href="lookbook.php">Lookbook</a>
        <a href="contact.php">Contact</a>
    </nav>
    <button class="hamburger-btn" id="hamburgerBtn" aria-label="Open menu">
        <i class="fa-solid fa-bars"></i>
    </button>
</header>

<!-- Slide-out drawer -->
<div class="drawer-overlay" id="drawerOverlay"></div>
<aside class="drawer" id="drawer">
    <button class="drawer-close" id="drawerClose" aria-label="Close menu">&times;</button>

    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="account.php"><i class="fa-solid fa-user"></i> My Account</a>
        <a href="order.php"><i class="fa-solid fa-box"></i> Orders</a>
        <a href="cart.php">
            <i class="fa-solid fa-bag-shopping"></i> Bag
            <?php if ($cartCount > 0): ?>
                <span class="drawer-cart-badge"><?php echo $cartCount; ?></span>
            <?php endif; ?>
        </a>
        <div class="account-section">
            <a href="logout.php" class="logout-link">Log Out</a>
        </div>
    <?php else: ?>
        <a href="login.php"><i class="fa-solid fa-user"></i> Log In</a>
    <?php endif; ?>
    
</aside>