<?php
require_once 'auth.php';
require_once 'config.php';

// Only allow admin users to edit products
if (!isAdmin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Nav atļauts rediģēt produktus']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$required_fields = ['id', 'product_code', 'product_name', 'category', 'unit_price', 'min_stock_level'];
foreach ($required_fields as $field) {
    if (!isset($data[$field]) || $data[$field] === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Visiem laukiem jābūt aizpildītiem']);
        exit;
    }
}

try {
    // Check if product code already exists for other products
    $stmt = $pdo->prepare("SELECT id FROM products WHERE product_code = ? AND id != ?");
    $stmt->execute([$data['product_code'], $data['id']]);
    if ($stmt->rowCount() > 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Šāds produkta kods jau eksistē']);
        exit;
    }

    // Check if barcode already exists for other products (if provided)
    if (!empty($data['barcode'])) {
        $stmt = $pdo->prepare("SELECT id FROM products WHERE barcode = ? AND id != ?");
        $stmt->execute([$data['barcode'], $data['id']]);
        if ($stmt->rowCount() > 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Šāds svītrkods jau eksistē']);
            exit;
        }
    }

    // Update product
    $stmt = $pdo->prepare("
        UPDATE products 
        SET product_code = ?, product_name = ?, category = ?, barcode = ?, 
            unit_price = ?, min_stock_level = ?, description = ?
        WHERE id = ?
    ");
    
    $stmt->execute([
        $data['product_code'],
        $data['product_name'],
        $data['category'],
        $data['barcode'] ?? null,
        $data['unit_price'],
        $data['min_stock_level'],
        $data['description'] ?? null,
        $data['id']
    ]);

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Produkts veiksmīgi atjaunināts'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Kļūda atjauninot produktu: ' . $e->getMessage()]);
}
?> 