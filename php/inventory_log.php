<?php
function logInventoryChange(
  PDO $pdo,
  int $productId,
  string $changeType,
  int $quantityChange,
  int $previousStock,
  int $newStock,
  ?int $referenceId = null,
  ?string $note = null
): void {
  $stmt = $pdo->prepare(
    "INSERT INTO inventory_log (product_id, change_type, quantity_change, previous_stock, new_stock, reference_id, note)
     VALUES (?, ?, ?, ?, ?, ?, ?)"
  );
  $stmt->execute([$productId, $changeType, $quantityChange, $previousStock, $newStock, $referenceId, $note]);
}