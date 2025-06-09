<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require_once 'config.php';

try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY product_code");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($products)) {
        echo json_encode(['message' => 'No products found', 'products' => []]);
    } else {
        echo json_encode(['products' => $products]);
    }
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 