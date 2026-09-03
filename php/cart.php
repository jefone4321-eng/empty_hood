<?php
  session_start();
  $cartCount = array_sum($_SESSION['cart'] ?? []);
  include 'header.php';
  include 'data.php';

  // Helper: find a product by its id
  function findProduct($products, $id) {
    foreach ($products as $product) {
      if ($product['id'] === $id) {
        return $product;
      }
    }
    return null;
  }

  $cartItems = $_SESSION['cart'] ?? [];
 
  $subtotal = 0;
?>

<section class="shop-page">
  <h1>YOUR BAG</h1>

  <?php if (empty($cartItems)): ?>
    <p class="cart-empty">Your bag is empty. <a href="shop.php">Go shop →</a></p>
  <?php else: ?>
    <div class="cart-list">
      <?php foreach ($cartItems as $id => $qty): ?>
        <?php $product = findProduct($products, $id); ?>
        <?php if ($product): ?>
          <?php
            $priceNumber = (float) str_replace(["₱", ","], "", $product['price']);
            $lineTotal = $priceNumber * $qty;
            $subtotal += $lineTotal;
          ?>
          <div class="cart-row">
            <img src="<?php echo $product['images']; ?>" alt="<?php echo $product['name']; ?>">
            <div class="cart-row-info">
              <h3><?php echo $product['name']; ?></h3>
              <p class="product-price">₱<?php echo number_format($priceNumber, 2); ?></p>
            </div>
            <div class="cart-qty">
              <form method="post" action="create_cart.php">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <input type="hidden" name="action" value="decrease">
                <input type="hidden" name="redirect" value="cart.php">
                <button type="submit">−</button>
              </form>
              <span><?php echo $qty; ?></span>
              <form method="post" action="create_cart.php">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <input type="hidden" name="action" value="increase">
                <input type="hidden" name="redirect" value="cart.php">
                <button type="submit">+</button>
              </form>
            </div>
            <p class="cart-row-total">₱<?php echo number_format($lineTotal, 2); ?></p>
            <form method="post" action="create_cart.php">
              <input type="hidden" name="id" value="<?php echo $id; ?>">
              <input type="hidden" name="action" value="remove">
              <input type="hidden" name="redirect" value="cart.php">
              <button type="submit" class="cart-remove">Remove</button>
            </form>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>

    <div class="cart-summary">
      <p>Subtotal: <strong>₱<?php echo number_format($subtotal, 2); ?></strong></p>
      <button class="btn-primary">CHECKOUT →</button>
    </div>
  <?php endif; ?>
</section>

<?php include 'footer.php'; ?>