 <?php require_once __DIR__ . '/error_handler.php'; ?>
<?php
  session_start();
  require_once '../database/config.php';
  require_once 'csrf.php';

  if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
  }

  $pdo = getConnection();
  include 'data.php';

  function findProduct($products, $id) {
    foreach ($products as $product) {
      if ($product['id'] === $id) return $product;
    }
    return null;
  }

  // A Buy Now reservation (if present) takes priority over the regular
  // cart — checkout shows whichever one the person is actually here for.
  $buyNow = $_SESSION['buy_now'] ?? null;
  $isBuyNow = $buyNow !== null;

  $lineItems = [];
  $subtotal = 0;
  $stockWarnings = [];

  if ($isBuyNow) {
    $product = findProduct($products, $buyNow['id']);

    if (!$product) {
      unset($_SESSION['buy_now']);
      header("Location: shop.php");
      exit;
    }

    $qty = $buyNow['qty'];

   
    if ($product['stock'] <= 0) {
      unset($_SESSION['buy_now']);
      header("Location: shop.php?soldout=" . urlencode($product['name']));
      exit;
    }

    if ($qty > $product['stock']) {
      $qty = $product['stock'];
      $_SESSION['buy_now']['qty'] = $qty;
      $stockWarnings[] = htmlspecialchars($product['name']) . " — only {$qty} left, quantity was adjusted.";
    }

    $priceNumber = (float) str_replace(["₱", ","], "", $product['price']);
    $lineTotal = $priceNumber * $qty;
    $subtotal += $lineTotal;
    $lineItems[] = ['product' => $product, 'qty' => $qty, 'lineTotal' => $lineTotal];

  } else {
    $cartItems = $_SESSION['cart'] ?? [];

    if (empty($cartItems)) {
      header("Location: cart.php");
      exit;
    }

    foreach ($cartItems as $id => $qty) {
      $product = findProduct($products, $id);
      if (!$product) continue;

      if ($product['stock'] <= 0) {
        unset($_SESSION['cart'][$id]);
        $stockWarnings[] = htmlspecialchars($product['name']) . " sold out and was removed from your bag.";
        continue;
      }

      if ($qty > $product['stock']) {
        $qty = $product['stock'];
        $_SESSION['cart'][$id] = $qty;
        $stockWarnings[] = htmlspecialchars($product['name']) . " — only {$qty} left, quantity was adjusted.";
      }

      $priceNumber = (float) str_replace(["₱", ","], "", $product['price']);
      $lineTotal = $priceNumber * $qty;
      $subtotal += $lineTotal;
      $lineItems[] = ['product' => $product, 'qty' => $qty, 'lineTotal' => $lineTotal];
    }

    if (empty($lineItems)) {
      header("Location: cart.php");
      exit;
    }
  }

  $shipping = 120.00;
  $total = $subtotal + $shipping;

  // Pull saved address to pre-fill the form
  $stmt = $pdo->prepare("SELECT address FROM accounts WHERE id = ?");
  $stmt->execute([$_SESSION['user_id']]);
  $account = $stmt->fetch();

  
  $orderError = $_SESSION['checkout_error'] ?? null;
  unset($_SESSION['checkout_error']);

  $errors = [];

  $cartCount = array_sum($_SESSION['cart'] ?? []);
  $navStyle = "";
  include 'header.php';
?>

<section class="shop-page checkout-page">
  <h1>CHECKOUT</h1>

  <?php if ($isBuyNow): ?>
    <a href="cancel_buy_now.php" class="checkout-cancel-link">&larr; Cancel</a>
  <?php else: ?>
    <a href="cart.php" class="checkout-cancel-link">&larr; Back to Bag</a>
  <?php endif; ?>

  <?php if ($orderError): ?>
    <div class="form-errors">
      <p><?php echo htmlspecialchars($orderError); ?></p>
    </div>
  <?php endif; ?>

  <?php if (!empty($stockWarnings)): ?>
    <div class="form-errors">
      <?php foreach ($stockWarnings as $warning): ?>
        <p><?php echo $warning; ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($errors)): ?>
    <div class="form-errors">
      <?php foreach ($errors as $error): ?>
        <p><?php echo $error; ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <div class="checkout-layout">
    <form method="post" action="place_order.php" class="checkout-form">
      <h2>Delivery Address</h2>
      <label>
        Shipping Address
        <textarea name="address" rows="3" required placeholder="Street, city, province"><?php echo htmlspecialchars($account['address'] ?? ''); ?></textarea>
      </label>

      <h2>Payment Method</h2>
      <label class="payment-option">
        <input type="radio" name="payment_method" value="Cash on Delivery" checked>
        <span><i class="fa-solid fa-money-bill-wave"></i> Cash on Delivery</span>
      </label>
      <label class="payment-option">
        <input type="radio" name="payment_method" value="GCash">
        <span><i class="fa-solid fa-mobile-screen-button"></i> GCash</span>
      </label>

      <button type="submit" class="btn-primary btn-full">PLACE ORDER →</button>
    </form>

    <div class="checkout-summary">
      <h2>Order Summary</h2>
      <?php foreach ($lineItems as $item): ?>
        <div class="checkout-item">
          <img src="<?php echo $item['product']['images']; ?>" alt="<?php echo $item['product']['name']; ?>">
          <div>
            <p class="checkout-item-name"><?php echo $item['product']['name']; ?> × <?php echo $item['qty']; ?></p>
            <p class="checkout-item-price">₱<?php echo number_format($item['lineTotal'], 2); ?></p>
          </div>
        </div>
      <?php endforeach; ?>

      <div class="checkout-totals">
        <p><span>Subtotal</span><span>₱<?php echo number_format($subtotal, 2); ?></span></p>
        <p><span>Shipping</span><span>₱<?php echo number_format($shipping, 2); ?></span></p>
        <p class="checkout-grand-total"><span>Total</span><span>₱<?php echo number_format($total, 2); ?></span></p>
      </div>
    </div>
  </div>
</section>

<?php include 'footer.php'; ?>