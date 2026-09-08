<?php
  include 'admin_header.php';

  $message = null;

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['stock_updates'])) {
    foreach ($_POST['stock_updates'] as $productId => $newStock) {
      $newStock = max(0, (int) $newStock); // never allow negative stock
      $stmt = $pdo->prepare("UPDATE products SET stock = ? WHERE id = ?");
      $stmt->execute([$newStock, $productId]);
    }
    $message = "Inventory updated successfully.";
  }

  $products = $pdo->query("SELECT id, name, image, stock FROM products ORDER BY stock ASC")->fetchAll();
?>

<h1>Inventory</h1>
<p style="color:#888; margin-bottom:24px;">Update stock levels for each product.</p>

<?php if ($message): ?>
  <p class="form-success" style="margin-bottom:20px;"><?php echo $message; ?></p>
<?php endif; ?>

<form method="post">
  <div class="admin-table-wrapper">
    <table class="admin-table">
      <tr>
        <th>Image</th>
        <th>Product</th>
        <th>Status</th>
        <th>Stock</th>
      </tr>
      <?php foreach ($products as $product): ?>
        <tr>
          <td><img src="../../<?php echo htmlspecialchars($product['image']); ?>" class="admin-thumb"></td>
          <td><?php echo htmlspecialchars($product['name']); ?></td>
          <td>
            <?php if ($product['stock'] <= 0): ?>
              <span class="admin-badge" style="background:rgba(192,57,43,0.18); color:#e08080;">Out of Stock</span>
            <?php elseif ($product['stock'] <= 5): ?>
              <span class="admin-badge" style="background:rgba(201,162,39,0.18); color:#d9b84a;">Low Stock</span>
            <?php else: ?>
              <span class="admin-badge" style="background:rgba(92,140,87,0.18); color:#8fc98a;">In Stock</span>
            <?php endif; ?>
          </td>
          <td>
            <input type="number" min="0"
                   name="stock_updates[<?php echo $product['id']; ?>]"
                   value="<?php echo $product['stock']; ?>"
                   style="width:80px; background:#0d0d0d; border:1px solid #2a2a2a; color:#fff; padding:8px; border-radius:4px;">
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>
  <button type="submit" class="admin-btn-primary" style="margin-top:20px;">SAVE ALL CHANGES</button>
</form>

<?php include 'admin_footer.php'; ?>