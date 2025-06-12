<?php
require_once 'auth.php';
require_once 'config.php';

// Only allow admin and warehouse workers to add products
if (!isAdmin() && !hasRole('warehouse')) {
    http_response_code(403);
    echo json_encode(['error' => 'Nav atļauts pievienot produktus']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$required_fields = ['product_code', 'product_name', 'category', 'unit_price', 'min_stock_level'];
foreach ($required_fields as $field) {
    if (!isset($data[$field]) || $data[$field] === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Visiem obligātajiem laukiem jābūt aizpildītiem']);
        exit;
    }
}

try {
    // Check if product code already exists
    $stmt = $pdo->prepare("SELECT id FROM products WHERE product_code = ?");
    $stmt->execute([$data['product_code']]);
    if ($stmt->rowCount() > 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Šāds produkta kods jau eksistē']);
        exit;
    }

    // Check if barcode already exists (if provided)
    if (!empty($data['barcode'])) {
        $stmt = $pdo->prepare("SELECT id FROM products WHERE barcode = ?");
        $stmt->execute([$data['barcode']]);
        if ($stmt->rowCount() > 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Šāds svītrkods jau eksistē']);
            exit;
        }
    }

    // Insert new product
    $stmt = $pdo->prepare("
        INSERT INTO products (product_code, product_name, category, barcode, description, unit_price, min_stock_level) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $data['product_code'],
        $data['product_name'],
        $data['category'],
        $data['barcode'] ?? null,
        $data['description'] ?? null,
        $data['unit_price'],
        $data['min_stock_level']
    ]);

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Produkts veiksmīgi pievienots',
        'id' => $pdo->lastInsertId()
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Kļūda pievienojot produktu: ' . $e->getMessage()]);
}
?> 