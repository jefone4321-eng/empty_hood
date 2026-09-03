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
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
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
    <div class="nav-icons">
        <button aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
       <?php if (isset($_SESSION['user_id'])): ?>
    <a href="account.php" aria-label="My Account"><i class="fa-solid fa-user"></i></a>
<?php else: ?>
            <a href="login.php" aria-label="Log in"><i class="fa-solid fa-user"></i></a>
        <?php endif; ?>
        <a href="cart.php" class="nav-cart" aria-label="Cart">
            <i class="fa-solid fa-bag-shopping"></i>
            <span><?php echo $cartCount; ?></span>
        </a>
    </div>
</header>