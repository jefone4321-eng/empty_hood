<?php
  session_start();
  require_once '../database/config.php';

  if (!isset($_SESSION['user_id']) || !isset($_SESSION['pending_order'])) {
    header("Location: cart.php");
    exit;
  }

  include 'data.php';

  function findProductForGcash($products, $id) {
    foreach ($products as $product) {
      if ($product['id'] === $id) return $product;
    }
    return null;
  }

  $buyNow = $_SESSION['buy_now'] ?? null;
  $isBuyNow = $buyNow !== null;

  $subtotal = 0;

  if ($isBuyNow) {
    $product = findProductForGcash($products, $buyNow['id']);
    if ($product) {
      $priceNumber = (float) str_replace(["₱", ","], "", $product['price']);
      $subtotal += $priceNumber * $buyNow['qty'];
    }
  } else {
    $cartItems = $_SESSION['cart'] ?? [];
    foreach ($cartItems as $id => $qty) {
      $product = findProductForGcash($products, $id);
      if ($product) {
        $priceNumber = (float) str_replace(["₱", ","], "", $product['price']);
        $subtotal += $priceNumber * $qty;
      }
    }
  }

  $shipping = 120.00;
  $total = $subtotal + $shipping;

  $cartCount = array_sum($_SESSION['cart'] ?? []);
  $navStyle = "";
  include 'header.php';
?>



<section class="shop-page checkout-page">
  <div class="gcash-payment">
    <i class="fa-solid fa-mobile-screen-button gcash-icon"></i>
    <h1>Scan to Pay with GCash</h1>
    <p style="color:#999;">Open your GCash app and scan the QR code below.</p>

    <img src="../images/gcash-qr.jpg" alt="GCash QR Code" class="gcash-qr">

    <p class="gcash-amount">Amount to pay: <strong>₱<?php echo number_format($total, 2); ?></strong></p>

    <form method="post" action="gcash_confirm.php">
      <button type="submit" class="btn-primary btn-full">I'VE PAID →</button>
    </form>

    <p class="gcash-note">This is a simulated payment for demo purposes.</p>
  </div>
</section>

<?php include 'footer.php'; ?>