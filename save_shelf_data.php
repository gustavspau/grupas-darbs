<?php
require_once 'config.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$shelfCode = $input['shelf_code'] ?? '';
$productQuantity = $input['product_quantity'] ?? '';
$shelfStatus = $input['shelf_status'] ?? '';
$shelfComment = $input['shelf_comment'] ?? '';
$productCode = $input['product_code'] ?? '';

$errors = [];

// Validate Shelf Code
if (empty($shelfCode)) {
    $errors['shelfCode'] = 'Lūdzu izvēlieties plauktu.';
}

// Validate Product Quantity
if (empty($productQuantity) || !is_numeric($productQuantity) || $productQuantity <= 0) {
    $errors['productQuantity'] = 'Lūdzu ievadiet derīgu produktu skaitu (jābūt lielākam par 0).';
}

// Validate Shelf Status
$valid_statuses = ['normal', 'low_stock', 'needs_organize', 'maintenance'];
if (empty($shelfStatus) || !in_array($shelfStatus, $valid_statuses)) {
    $errors['shelfStatus'] = 'Lūdzu izvēlieties derīgu stāvokli.';
}

// Validate Product Code
if (empty($productCode)) {
    $errors['productCodeInput'] = 'Lūdzu ievadiet produkta kodu.';
}

// Validate Shelf Comment
if (strlen($shelfComment) > 255) {
    $errors['shelfComment'] = 'Komentārs nedrīkst pārsniegt 255 rakstzīmes.';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Validācijas kļūdas', 'errors' => $errors]);
    exit();
}

try {
    // Start a transaction
    $pdo->beginTransaction();

    // 1. Get shelf_id from shelves table
    $stmt = $pdo->prepare("SELECT id, max_capacity FROM shelves WHERE shelf_code = ?");
    $stmt->execute([$shelfCode]);
    $shelf = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$shelf) {
        $pdo->rollBack();
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Plaukts ar norādīto kodu netika atrasts.']);
        exit();
    }
    $shelfId = $shelf['id'];
    $shelfMaxCapacity = $shelf['max_capacity']; // Get shelf max capacity

    // 2. Get product_id from products table
    $stmt = $pdo->prepare("SELECT id FROM products WHERE product_code = ?");
    $stmt->execute([$productCode]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        // Optionally, create the product if it doesn't exist, or return an error.
        // For now, we'll return an error if the product is not found.
        $pdo->rollBack();
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Produkts ar norādīto kodu netika atrasts.']);
        exit();
    }
    $productId = $product['id'];

    // Check if adding this quantity would exceed the shelf's max capacity
    $stmt = $pdo->prepare("SELECT SUM(quantity) AS total_quantity FROM inventory WHERE shelf_id = ?");
    $stmt->execute([$shelfId]);
    $currentTotalOnShelf = $stmt->fetch(PDO::FETCH_ASSOC)['total_quantity'] ?? 0;

    if (($currentTotalOnShelf + $productQuantity) > $shelfMaxCapacity) {
        $pdo->rollBack();
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Produktu skaits pārsniedz plaukta maksimālo ietilpību (' . $shelfMaxCapacity . ' gab.). Plauktā jau ir ' . $currentTotalOnShelf . ' gab. produktu.']);
        exit();
    }

    // 3. Update the shelves table with status and comment
    $stmt = $pdo->prepare("
        UPDATE shelves
        SET
            status = ?,
            comment = ?
        WHERE id = ?
    ");
    $stmt->execute([$shelfStatus, $shelfComment, $shelfId]);

    // 4. Update or insert into inventory table
    // Check if product already exists in inventory for this shelf
    $stmt = $pdo->prepare('SELECT quantity FROM inventory WHERE product_id = :product_id AND shelf_id = :shelf_id');
    $stmt->execute(['product_id' => $productId, 'shelf_id' => $shelfId]);
    $inventoryItem = $stmt->fetch();

    if ($inventoryItem) {
        // Update existing quantity
        $newQuantity = $inventoryItem['quantity'] + $productQuantity;
        $stmt = $pdo->prepare('UPDATE inventory SET quantity = :quantity WHERE product_id = :product_id AND shelf_id = :shelf_id');
        $stmt->execute(['quantity' => $newQuantity, 'product_id' => $productId, 'shelf_id' => $shelfId]);
    } else {
        // Insert new inventory item
        $stmt = $pdo->prepare('INSERT INTO inventory (product_id, shelf_id, quantity) VALUES (:product_id, :shelf_id, :quantity)');
        $stmt->execute(['product_id' => $productId, 'shelf_id' => $shelfId, 'quantity' => $productQuantity]);
    }

    // Commit the transaction
    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Dati veiksmīgi saglabāti!']);

} catch (PDOException $e) {
    // Rollback transaction on error
    $pdo->rollBack();
    http_response_code(500);
    error_log('Database error in save_shelf_data.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Kļūda datubāzē: ' . $e->getMessage()]);
} catch (Exception $e) {
    // Catch any other exceptions
    $pdo->rollBack();
    http_response_code(500);
    error_log('General error in save_shelf_data.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Neparedzēta kļūda: ' . $e->getMessage()]);
}
?> 