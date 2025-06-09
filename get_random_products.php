<?php
require_once 'auth.php';
require_once 'config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set proper headers
header('Content-Type: application/json');

// Check if user is a warehouse worker
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Noliktavas darbinieks') {
    http_response_code(403);
    echo json_encode(['error' => 'Nav atļauts']);
    exit;
}

try {
    // Get random products from the database
    $stmt = $pdo->query("
        SELECT p.*, c.name as category_name 
        FROM products p 
        JOIN categories c ON p.category_id = c.id 
        ORDER BY RAND() 
        LIMIT 5
    ");
    
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($products)) {
        echo json_encode([]);
        exit;
    }
    
    // Format the response
    $formattedProducts = array_map(function($product) {
        return [
            'id' => $product['id'],
            'code' => $product['code'],
            'name' => $product['name'],
            'category' => $product['category_name'],
            'barcode' => $product['barcode']
        ];
    }, $products);
    
    echo json_encode($formattedProducts);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Datubāzes kļūda: ' . $e->getMessage()]);
} 