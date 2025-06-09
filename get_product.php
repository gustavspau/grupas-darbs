<?php
require_once 'auth.php';
require_once 'config.php';

// Only allow warehouse workers to access this
if (!hasRole('warehouse')) {
    http_response_code(403);
    echo json_encode(['error' => 'Nav atļauts piekļūt šai funkcijai']);
    exit;
}

// Get product code from query string
$code = isset($_GET['code']) ? trim($_GET['code']) : '';

if (empty($code)) {
    http_response_code(400);
    echo json_encode(['error' => 'Produkta kods nav norādīts']);
    exit;
}

try {
    // Look up product by code
    $stmt = $pdo->prepare("
        SELECT id, product_code as code, product_name as name, category, barcode 
        FROM products 
        WHERE product_code = ? OR barcode = ?
    ");
    $stmt->execute([$code, $code]);
    $product = $stmt->fetch();

    if (!$product) {
        http_response_code(404);
        echo json_encode(['error' => 'Produkts nav atrasts']);
        exit;
    }

    echo json_encode($product);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Kļūda meklējot produktu: ' . $e->getMessage()]);
}
?> 