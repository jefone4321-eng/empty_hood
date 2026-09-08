<?php
  include 'admin_header.php';

  $editing = isset($_GET['id']);
  $product = ['name' => '', 'price' => '', 'category' => '', 'collection' => '', 'image' => ''];

  if ($editing) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $product = $stmt->fetch();
  }

  // Fixed list of categories
  $categoryOptions = ["T-Shirts", "Hoodies", "Bottoms", "Headwear", "Accessories"];

  // Collections pulled from what already exists in the database, so the list grows naturally
  $collectionOptions = $pdo->query("SELECT DISTINCT collection FROM products ORDER BY collection ASC")
                            ->fetchAll(PDO::FETCH_COLUMN);

  $errors = [];

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $price = $_POST['price'] ?? '';
    $category = trim($_POST['category'] ?? '');
    $collection = trim($_POST['new_collection'] ?? '') !== ''
        ? trim($_POST['new_collection'])
        : trim($_POST['collection'] ?? '');

    $imagePath = $product['image']; // keep existing image unless a new one is uploaded

    if ($name === '') $errors['name'] = "Name is required.";
    if (!is_numeric($price) || $price <= 0) $errors['price'] = "Enter a valid price.";
    if ($category === '') $errors['category'] = "Please select a category.";
    if ($collection === '') $errors['collection'] = "Please select or enter a collection.";

    // Handle image upload
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
      $file = $_FILES['image_file'];
      $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
      $maxSize = 3 * 1024 * 1024; // 3MB

      if (!in_array($file['type'], $allowedTypes)) {
        $errors['image_file'] = "Only JPG, PNG, or WEBP images are allowed.";
      } elseif ($file['size'] > $maxSize) {
        $errors['image_file'] = "Image must be smaller than 3MB.";
      } else {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $safeName = preg_replace('/[^a-zA-Z0-9]/', '', pathinfo($file['name'], PATHINFO_FILENAME));
        $newFilename = $safeName . "_" . time() . "." . $ext;
        $destination = "../../images/products/" . $newFilename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
          $imagePath = "images/products/" . $newFilename;
        } else {
          $errors['image_file'] = "Could not save the uploaded image.";
        }
      }
    } elseif (!$editing && $imagePath === '') {
      $errors['image_file'] = "Please upload a product image.";
    }

    if (empty($errors)) {
      if ($editing) {
        $update = $pdo->prepare("UPDATE products SET name=?, price=?, category=?, collection=?, image=? WHERE id=?");
        $update->execute([$name, $price, $category, $collection, $imagePath, $_GET['id']]);
      } else {
        $insert = $pdo->prepare("INSERT INTO products (name, price, category, collection, image) VALUES (?, ?, ?, ?, ?)");
        $insert->execute([$name, $price, $category, $collection, $imagePath]);
      }
      header("Location: products.php");
      exit;
    }

    $product = ['name' => $name, 'price' => $price, 'category' => $category, 'collection' => $collection, 'image' => $imagePath];
  }
?>

<h1><?php echo $editing ? 'Edit Product' : 'Add Product'; ?></h1>

<form method="post" enctype="multipart/form-data" class="auth-form admin-form" novalidate>
    <label>Name
        <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>"
               class="<?php echo isset($errors['name']) ? 'has-error' : ''; ?>">
        <?php if (isset($errors['name'])): ?><span class="field-error"><?php echo $errors['name']; ?></span><?php endif; ?>
    </label>

    <label>Price (₱)
        <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($product['price']); ?>"
               class="<?php echo isset($errors['price']) ? 'has-error' : ''; ?>">
        <?php if (isset($errors['price'])): ?><span class="field-error"><?php echo $errors['price']; ?></span><?php endif; ?>
    </label>

    <label>Category
        <select name="category" class="<?php echo isset($errors['category']) ? 'has-error' : ''; ?>">
            <option value="">Select a category</option>
            <?php foreach ($categoryOptions as $cat): ?>
                <option value="<?php echo htmlspecialchars($cat); ?>"
                    <?php echo $product['category'] === $cat ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['category'])): ?><span class="field-error"><?php echo $errors['category']; ?></span><?php endif; ?>
    </label>

    <label>Collection
        <select name="collection" class="<?php echo isset($errors['collection']) ? 'has-error' : ''; ?>">
            <option value="">Select an existing collection</option>
            <?php foreach ($collectionOptions as $col): ?>
                <option value="<?php echo htmlspecialchars($col); ?>"
                    <?php echo $product['collection'] === $col ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($col); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>Or create a new collection
        <input type="text" name="new_collection" placeholder="Leave blank to use the dropdown above">
    </label>

    <label>Product Image
        <?php if ($product['image']): ?>
            <img src="../../<?php echo htmlspecialchars($product['image']); ?>" style="width:80px; height:100px; object-fit:cover; margin-bottom:10px; border-radius:4px;">
        <?php endif; ?>
        <input type="file" name="image_file" accept="image/jpeg,image/png,image/webp">
        <?php if (isset($errors['image_file'])): ?><span class="field-error"><?php echo $errors['image_file']; ?></span><?php endif; ?>
    </label>

    <button type="submit" class="btn-primary"><?php echo $editing ? 'SAVE CHANGES' : 'ADD PRODUCT'; ?> →</button>
</form>

<?php include 'admin_footer.php'; ?>