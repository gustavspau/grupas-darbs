<?php
require_once 'config.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$shelfCode = $input['shelf_code'] ?? '';
$productQuantity = $input['product_quantity'] ?? '';
$shelfStatus = $input['shelf_status'] ?? '';
$shelfComment = $input['shelf_comment'] ?? '';

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
    // Find shelf_id by shelf_code
    $stmt = $pdo->prepare("SELECT id FROM shelves WHERE shelf_code = ?");
    $stmt->execute([$shelfCode]);
    $shelf = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$shelf) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Plaukts ar norādīto kodu netika atrasts.']);
        exit();
    }

    $shelfId = $shelf['id'];

    // Update the shelves table
    $stmt = $pdo->prepare("
        UPDATE shelves
        SET
            status = ?,
            comment = ?,
            current_product_count = ?
        WHERE id = ?
    ");
    $stmt->execute([$shelfStatus, $shelfComment, $productQuantity, $shelfId]);

    echo json_encode(['success' => true, 'message' => 'Dati veiksmīgi saglabāti!']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Kļūda datubāzē: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Neparedzēta kļūda: ' . $e->getMessage()]);
}
?> 