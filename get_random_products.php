<?php
require_once 'auth.php';
require_once 'config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) {
    http_response_code(401); 
    echo json_encode(['error' => 'Nav autentificēts']);
    exit;
}
try {
    $stmt = $pdo->query("
        SELECT id, product_code, product_name, category, barcode 
        FROM products 
        ORDER BY RAND() 
        LIMIT 5
    ");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($products)) {
        echo json_encode([]);
        exit;
    }
    $formattedProducts = array_map(function($product) {
        return [
            'id' => $product['id'],
            'code' => $product['product_code'],
            'name' => $product['product_name'],
            'category' => $product['category'],
            'barcode' => $product['barcode']
        ];
    }, $products);
    echo json_encode($formattedProducts);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Datubāzes kļūda: ' . $e->getMessage()]);
}
