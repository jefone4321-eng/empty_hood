<?php
session_start();
$cartCount = array_sum($_SESSION['cart'] ?? []);
$navStyle = "";
include 'header.php';
include 'data.php';


$categories = ["All", "T-Shirts", "Hoodies", "Bottoms", "Headwear", "Accessories"];
$activeCategory = isset($_GET['category']) ? $_GET['category'] : "All";
?>

<section class="shop-page">
  <h1>THE COLLECTION</h1>

  <div class="filters">
    <?php foreach ($categories as $cat): ?>
      <a href="shop.php?category=<?php echo urlencode($cat); ?>"
        class="filter-btn <?php echo $activeCategory === $cat ? 'active' : ''; ?>">
        <?php echo $cat; ?>
      </a>
    <?php endforeach; ?>
  </div>

  <div class="product-grid">
    <?php foreach ($products as $product): ?>
      <?php if ($activeCategory === "All" || $product["category"] === $activeCategory): ?>
        <article class="product-card">
          <img src="<?php echo $product['images']; ?>" alt="<?php echo $product['name']; ?>">
          <h3><?php echo $product['name']; ?></h3>
          <p class="product-price"><?php echo $product['price']; ?></p>
          <?php if (isset($product['stock']) && $product['stock'] <= 0): ?>
            <p class="stock-badge out">Out of Stock</p>
          <?php elseif (isset($product['stock']) && $product['stock'] <= 5): ?>
            <p class="stock-badge low">Only <?php echo $product['stock']; ?> left</p>
          <?php endif; ?>
          <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($product['stock'] > 0): ?>
              <div class="product-actions">
                <form method="post" action="create_cart.php" class="qty-form">
                  <?php echo csrf_field(); ?>
                  <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                  <input type="hidden" name="action" value="add">
                  <input type="hidden" name="redirect" value="shop.php">
                  <button type="submit" class="add-to-cart-btn">Add to Bag</button>
                </form>

                <button type="button" class="buy-now-btn" data-id="<?php echo $product['id']; ?>"
                  data-name="<?php echo htmlspecialchars($product['name']); ?>"
                  data-price="<?php echo htmlspecialchars($product['price']); ?>"
                  data-image="<?php echo htmlspecialchars($product['images']); ?>"
                  data-stock="<?php echo $product['stock']; ?>">
                  Buy Now
                </button>
              </div>
            <?php else: ?>
              <button class="add-to-cart-btn" disabled style="opacity:0.4; cursor:not-allowed;">Sold Out</button>
            <?php endif; ?>
          <?php else: ?>
            <a href="signup.php" class="add-to-cart-btn">Add to Bag</a>


          <?php endif; ?>
        </article>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
</section>

<div class="buy-now-overlay" id="buyNowOverlay">
  <div class="buy-now-modal">
    <button class="buy-now-close" id="buyNowClose" aria-label="Close">&times;</button>

    <img src="" alt="" id="buyNowImage" class="buy-now-image">
    <h3 id="buyNowName"></h3>
    <p class="product-price" id="buyNowPrice"></p>

    <div class="buy-now-qty">
      <button type="button" id="buyNowMinus">−</button>
      <input type="number" id="buyNowQtyInput" value="1" min="1" readonly>
      <button type="button" id="buyNowPlus">+</button>
    </div>

    <form method="post" action="create_cart.php" id="buyNowForm">
      <?php echo csrf_field(); ?>
      <input type="hidden" name="id" id="buyNowProductId">
      <input type="hidden" name="action" value="buy_now">
      <input type="hidden" name="quantity" id="buyNowQtyField" value="1">
      <input type="hidden" name="redirect" value="checkout.php">
      <button type="submit" class="btn-primary btn-full">PROCEED TO CHECKOUT →</button>
    </form>
  </div>
</div>

<?php include 'footer.php'; ?>