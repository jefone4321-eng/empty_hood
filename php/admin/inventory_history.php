 <?php
  include 'admin_header.php';

  // Optional filter by product
  $filterProductId = isset($_GET['product_id']) && $_GET['product_id'] !== ''
    ? (int) $_GET['product_id']
    : null;

  // Optional filter by change type
  $filterType = isset($_GET['change_type']) && $_GET['change_type'] !== ''
    ? $_GET['change_type']
    : null;

  $validTypes = ['cart_reserve', 'cart_release', 'sale', 'restock_cancel', 'manual_adjustment'];

  $where = [];
  $params = [];

  if ($filterProductId) {
    $where[] = "inventory_log.product_id = ?";
    $params[] = $filterProductId;
  }

  if ($filterType && in_array($filterType, $validTypes, true)) {
    $where[] = "inventory_log.change_type = ?";
    $params[] = $filterType;
  }

  $whereClause = $where ? "WHERE " . implode(" AND ", $where) : "";

  $perPage = 50;
  $page = max(1, (int) ($_GET['page'] ?? 1));
  $offset = ($page - 1) * $perPage;

  $countStmt = $pdo->prepare("SELECT COUNT(*) FROM inventory_log $whereClause");
  $countStmt->execute($params);
  $totalRows = (int) $countStmt->fetchColumn();
  $totalPages = max(1, (int) ceil($totalRows / $perPage));

  $sql = "
    SELECT inventory_log.*, products.name AS product_name, products.image AS product_image
    FROM inventory_log
    JOIN products ON inventory_log.product_id = products.id
    $whereClause
    ORDER BY inventory_log.created_at DESC
    LIMIT $perPage OFFSET $offset
  ";
  $stmt = $pdo->prepare($sql);
  $stmt->execute($params);
  $logs = $stmt->fetchAll();

  $products = $pdo->query("SELECT id, name FROM products ORDER BY name ASC")->fetchAll();

  $typeLabels = [
    'cart_reserve'      => 'Added to Bag',
    'cart_release'      => 'Removed from Bag',
    'sale'              => 'Sale (Buy Now)',
    'restock_cancel'    => 'Order Cancelled',
    'manual_adjustment' => 'Manual Edit',
  ];

  $typeColors = [
    'cart_reserve'      => 'background:rgba(140,158,255,0.15); color:#8c9eff;',
    'cart_release'      => 'background:rgba(140,158,255,0.15); color:#8c9eff;',
    'sale'              => 'background:rgba(192,57,43,0.18); color:#e08080;',
    'restock_cancel'    => 'background:rgba(92,140,87,0.18); color:#8fc98a;',
    'manual_adjustment' => 'background:rgba(201,162,39,0.18); color:#d9b84a;',
  ];
?>

<div class="admin-page-header">
    <h1>Inventory History</h1>
</div>
<p style="color:#888; margin-bottom:24px;">Full audit trail of every stock change.</p>

<form method="get" style="display:flex; gap:12px; margin-bottom:20px; flex-wrap:wrap;">
    <select name="product_id" onchange="this.form.submit()"
            style="background:#0d0d0d; border:1px solid #2a2a2a; color:#fff; padding:8px; border-radius:4px;">
        <option value="">All Products</option>
        <?php foreach ($products as $product): ?>
            <option value="<?php echo $product['id']; ?>" <?php echo $filterProductId === (int)$product['id'] ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($product['name']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select name="change_type" onchange="this.form.submit()"
            style="background:#0d0d0d; border:1px solid #2a2a2a; color:#fff; padding:8px; border-radius:4px;">
        <option value="">All Change Types</option>
        <?php foreach ($typeLabels as $value => $label): ?>
            <option value="<?php echo $value; ?>" <?php echo $filterType === $value ? 'selected' : ''; ?>>
                <?php echo $label; ?>
            </option>
        <?php endforeach; ?>
    </select>

    <?php if ($filterProductId || $filterType): ?>
        <a href="inventory_history.php" class="admin-btn-primary" style="text-decoration:none;">Clear Filters</a>
    <?php endif; ?>
</form>

<div class="admin-table-wrapper">
<table class="admin-table">
    <tr>
        <th>Date</th>
        <th>Product</th>
        <th>Change</th>
        <th>Qty</th>
        <th>Previous → New</th>
        <th>Reference</th>
        <th>Note</th>
    </tr>
    <?php if (empty($logs)): ?>
        <tr><td colspan="7" class="admin-empty">No inventory changes recorded yet.</td></tr>
    <?php else: ?>
        <?php foreach ($logs as $log): ?>
            <tr>
                <td><?php echo date("M j, Y g:i A", strtotime($log['created_at'])); ?></td>
                <td>
                    <img src="../../<?php echo htmlspecialchars($log['product_image']); ?>" class="admin-thumb" style="margin-right:8px;">
                    <?php echo htmlspecialchars($log['product_name']); ?>
                </td>
                <td>
                    <span class="admin-badge" style="<?php echo $typeColors[$log['change_type']] ?? ''; ?>">
                        <?php echo $typeLabels[$log['change_type']] ?? htmlspecialchars($log['change_type']); ?>
                    </span>
                </td>
                <td style="color:<?php echo $log['quantity_change'] < 0 ? '#e08080' : '#8fc98a'; ?>;">
                    <?php echo $log['quantity_change'] > 0 ? '+' . $log['quantity_change'] : $log['quantity_change']; ?>
                </td>
                <td><?php echo $log['previous_stock']; ?> → <?php echo $log['new_stock']; ?></td>
                <td><?php echo $log['reference_id'] ? '#' . $log['reference_id'] : '—'; ?></td>
                <td><?php echo htmlspecialchars($log['note'] ?? ''); ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</table>
</div>

<?php if ($totalPages > 1): ?>
    <div style="display:flex; gap:8px; margin-top:20px; align-items:center;">
        <?php
          $baseParams = $_GET;
          for ($p = 1; $p <= $totalPages; $p++):
            $baseParams['page'] = $p;
            $url = 'inventory_history.php?' . http_build_query($baseParams);
        ?>
            <a href="<?php echo htmlspecialchars($url); ?>"
               style="padding:6px 12px; border-radius:4px; text-decoration:none; color:#fff;
                      background:<?php echo $p === $page ? '#8c9eff' : '#0d0d0d'; ?>;
                      border:1px solid #2a2a2a;">
                <?php echo $p; ?>
            </a>
        <?php endfor; ?>
    </div>
<?php endif; ?>

<?php include 'admin_footer.php'; ?>