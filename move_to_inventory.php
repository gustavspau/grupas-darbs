<?php
// Include database connection
require_once 'config.php';

// Set content type to JSON
header('Content-Type: application/json');

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

$productName = $input['product_name'] ?? '';
$quantity = $input['quantity'] ?? 0;

// Log received data for debugging
error_log("Received productName: " . $productName);
error_log("Received quantity: " . $quantity);

// Validate input
if (empty($productName) || !is_numeric($quantity) || $quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product name or quantity.']);
    exit();
}

try {
    // Start a transaction
    $pdo->beginTransaction();

    // 1. Get product_id from products table
    $stmt = $pdo->prepare('SELECT id FROM products WHERE product_name = :product_name');
    $stmt->execute(['product_name' => $productName]);
    $product = $stmt->fetch();

    if (!$product) {
        // If product not found in products table, create it (this is a simplified approach, usually products are pre-registered)
        $stmt = $pdo->prepare('INSERT INTO products (product_name, product_code) VALUES (:product_name, :product_code)');
        $stmt->execute([
            'product_name' => $productName,
            'product_code' => 'AUTO-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8)) // Generate a simple unique code
        ]);
        $productId = $pdo->lastInsertId();
    } else {
        $productId = $product['id'];
    }

    // 2. Define a shelf_id for the inventory (using a default shelf for simplicity)
    // In a real system, you'd determine the shelf based on user input, logic, or existing data.
    $shelfId = 1; // Assuming shelf with ID 1 exists. You might want to make this dynamic.

    // Check if shelf_id exists, otherwise, you would need to create a default shelf or handle the error
    $stmt = $pdo->prepare('SELECT id FROM shelves WHERE id = :shelf_id');
    $stmt->execute(['shelf_id' => $shelfId]);
    if (!$stmt->fetch()) {
        // If shelf doesn't exist, try to create it or pick another default
        $stmt = $pdo->prepare('INSERT INTO shelves (shelf_code, section) VALUES (:shelf_code, :section)');
        $stmt->execute(['shelf_code' => 'DEFAULT-SHELF-01', 'section' => 'A']);
        $shelfId = $pdo->lastInsertId();
    }


    // 3. Update or insert into inventory table
    // Check if product already exists in inventory for this shelf
    $stmt = $pdo->prepare('SELECT quantity FROM inventory WHERE product_id = :product_id AND shelf_id = :shelf_id');
    $stmt->execute(['product_id' => $productId, 'shelf_id' => $shelfId]);
    $inventoryItem = $stmt->fetch();

    if ($inventoryItem) {
        // Update existing quantity
        $newQuantity = $inventoryItem['quantity'] + $quantity;
        $stmt = $pdo->prepare('UPDATE inventory SET quantity = :quantity WHERE product_id = :product_id AND shelf_id = :shelf_id');
        $stmt->execute(['quantity' => $newQuantity, 'product_id' => $productId, 'shelf_id' => $shelfId]);
    } else {
        // Insert new inventory item
        $stmt = $pdo->prepare('INSERT INTO inventory (product_id, shelf_id, quantity) VALUES (:product_id, :shelf_id, :quantity)');
        $stmt->execute(['product_id' => $productId, 'shelf_id' => $shelfId, 'quantity' => $quantity]);
    }

    // Commit the transaction
    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Product moved to inventory successfully.']);

} catch (PDOException $e) {
    // Rollback transaction on error
    $pdo->rollBack();
    error_log('Database error in move_to_inventory.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    // Catch any other exceptions
    $pdo->rollBack();
    error_log('General error in move_to_inventory.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred: ' . $e->getMessage()]);
}
?> 