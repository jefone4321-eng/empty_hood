<?php require_once __DIR__ . '/error_handler.php'; ?>
<?php
  require_once __DIR__ . '/../database/config.php';
  $pdo = getConnection();

  
  $stmt = $pdo->query("SELECT id, name, price, category, collection, image, stock FROM products ORDER BY id ASC");
  $rows = $stmt->fetchAll();

  $products = [];

  foreach ($rows as $row) {
    $products[] = [
        "id" => "p" . $row['id'],
        "images" => "../" . $row['image'],   
        "name" => $row['name'],
        "price" => "₱" . number_format($row['price'], 2),
        "category" => $row['category'],
        "collection" => $row['collection'],
        "stock" => $row['stock'],
    ];
}
?>