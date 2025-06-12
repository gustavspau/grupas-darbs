<?php
require_once 'config.php';
header('Content-Type: application/json');
$input = json_decode(file_get_contents('php:
$productName = $input['product_name'] ?? '';
$quantity = $input['quantity'] ?? 0;
error_log("Received productName: " . $productName);
error_log("Received quantity: " . $quantity);
if (empty($productName) || !is_numeric($quantity) || $quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product name or quantity.']);
    exit();
}
try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare('SELECT id FROM products WHERE product_name = :product_name');
    $stmt->execute(['product_name' => $productName]);
    $product = $stmt->fetch();
    if (!$product) {
        $stmt = $pdo->prepare('INSERT INTO products (product_name, product_code) VALUES (:product_name, :product_code)');
        $stmt->execute([
            'product_name' => $productName,
            'product_code' => 'AUTO-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8)) 
        ]);
        $productId = $pdo->lastInsertId();
    } else {
        $productId = $product['id'];
    }
    $shelfId = 1; 
    $stmt = $pdo->prepare('SELECT id FROM shelves WHERE id = :shelf_id');
    $stmt->execute(['shelf_id' => $shelfId]);
    if (!$stmt->fetch()) {
        $stmt = $pdo->prepare('INSERT INTO shelves (shelf_code, section) VALUES (:shelf_code, :section)');
        $stmt->execute(['shelf_code' => 'DEFAULT-SHELF-01', 'section' => 'A']);
        $shelfId = $pdo->lastInsertId();
    }
    $stmt = $pdo->prepare('SELECT quantity FROM inventory WHERE product_id = :product_id AND shelf_id = :shelf_id');
    $stmt->execute(['product_id' => $productId, 'shelf_id' => $shelfId]);
    $inventoryItem = $stmt->fetch();
    if ($inventoryItem) {
        $newQuantity = $inventoryItem['quantity'] + $quantity;
        $stmt = $pdo->prepare('UPDATE inventory SET quantity = :quantity WHERE product_id = :product_id AND shelf_id = :shelf_id');
        $stmt->execute(['quantity' => $newQuantity, 'product_id' => $productId, 'shelf_id' => $shelfId]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO inventory (product_id, shelf_id, quantity) VALUES (:product_id, :shelf_id, :quantity)');
        $stmt->execute(['product_id' => $productId, 'shelf_id' => $shelfId, 'quantity' => $quantity]);
    }
    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Product moved to inventory successfully.']);
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log('Database error in move_to_inventory.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    $pdo->rollBack();
    error_log('General error in move_to_inventory.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred: ' . $e->getMessage()]);
}
?>
