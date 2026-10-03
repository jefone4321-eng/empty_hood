<?php
include 'admin_header.php';
$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
?>

<div class="admin-page-header">
    <h1>Products</h1>
    <a href="product_form.php" class="admin-btn-primary"><i class="fa-solid fa-plus"></i> Add Product</a>
</div>

<div class="admin-table-wrapper">
    <table class="admin-table">
        <tr>
            <th>Image</th>
            <th>Name</th>
            <th>Price</th>
            <th>Category</th>
            <th>Collection</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><img src="../../<?php echo htmlspecialchars($product['image']); ?>" class="admin-thumb"></td>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td>₱<?php echo number_format($product['price'], 2); ?></td>
                <td><?php echo htmlspecialchars($product['category']); ?></td>
                <td><?php echo htmlspecialchars($product['collection']); ?></td>
                <td>
                    <a href="product_form.php?id=<?php echo $product['id']; ?>">Edit</a>
                    <form method="post" action="delete_product.php" onsubmit="return confirm('Delete this product?');"
                        style="display:inline">
                          <?php echo csrf_field(); ?>
                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                        <button type="submit" class="admin-delete-btn">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php include 'admin_footer.php'; ?>