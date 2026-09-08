<?php
  session_start();
  $cartCount = array_sum($_SESSION['cart'] ?? []);
  $navStyle = "";
  include 'header.php';
  include 'data.php';

  $query = trim($_GET['q'] ?? '');

  $results = [];

  if ($query !== '') {
    foreach ($products as $product) {
      if (stripos($product['name'], $query) !== false) {
        $results[] = $product;
      }
    }
  }
?>

<section class="shop-page">
  <h1>SEARCH RESULTS</h1>
  <p style="color:#888; margin-bottom:32px;">
    <?php if ($query !== ''): ?>
      Showing results for "<?php echo htmlspecialchars($query); ?>"
    <?php else: ?>
      Enter a search term above to find products.
    <?php endif; ?>
  </p>

  <?php if ($query !== '' && empty($results)): ?>
    <p style="color:#999;">No products matched your search. Try a different term.</p>
  <?php elseif (!empty($results)): ?>
    <div class="product-grid">
      <?php foreach ($results as $product): ?>
        <article class="product-card">
          <img src="<?php echo $product['images']; ?>" alt="<?php echo $product['name']; ?>">
          <h3><?php echo $product['name']; ?></h3>
          <p class="product-price"><?php echo $product['price']; ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<?php include 'footer.php'; ?>