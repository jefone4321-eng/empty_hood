<?php
  session_start ();
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
          <?php if (isset($_SESSION['user_id'])): ?>
  <form method="post" action="create_cart.php">
    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
    <input type="hidden" name="action" value="add">
    <input type="hidden" name="redirect" value="shop.php">
    <button type="submit" class="add-to-cart-btn">Add to Bag</button>
  </form>
<?php else: ?>
  <a href="signup.php" class="add-to-cart-btn">Add to Bag</a>
<?php endif; ?>
        </article>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
</section>

<?php include 'footer.php'; ?>