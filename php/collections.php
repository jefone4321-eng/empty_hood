<?php
$cartCount = 0;
$navStyle = "";
include 'header.php';
include 'data.php';

// Group products by their collection name
$grouped = [];
foreach ($products as $product) {
  $grouped[$product['collection']][] = $product;
}
?>

<section class="shop-page">
  <h1>COLLECTIONS</h1>

  <?php foreach ($grouped as $collectionName => $items): ?>
    <div class="collection-group">
      <div class="section-header">
        <h2><?php echo strtoupper($collectionName); ?></h2>
        <a href="shop.php" class="view-all">SHOP ALL →</a>
      </div>
      <div class="product-grid">
        <?php foreach ($items as $product): ?>
          <article class="product-card">
            <img src="<?php echo $product['images']; ?>" alt="<?php echo $product['name']; ?>">
            <h3><?php echo $product['name']; ?></h3>
            <p class="product-price"><?php echo $product['price']; ?></p>
            <form method="post" action="create_cart.php">
              <input type="hidden" name="redirect" value="shop.php">
             <a href="shop.php" class="add-to-cart-btn">Go to Shop</a>
            </form>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endforeach; ?>
</section>

<?php include 'footer.php'; ?>