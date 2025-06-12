<?php
require_once 'auth.php';
require_once 'config.php';
if (!isAdmin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Nav atļauts dzēst produktus']);
    exit;
}
$data = json_decode(file_get_contents('php:
$productId = $data['id'] ?? null;
if (!$productId) {
    http_response_code(400);
    echo json_encode(['error' => 'Produkta ID nav norādīts']);
    exit;
}
try {
    $stmt = $pdo->prepare("SELECT product_name FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    if (!$product) {
        http_response_code(404);
        echo json_encode(['error' => 'Produkts nav atrasts']);
        exit;
    }
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    echo json_encode([
        'success' => true,
        'message' => 'Produkts "' . $product['product_name'] . '" veiksmīgi dzēsts'
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Kļūda dzēšot produktu: ' . $e->getMessage()]);
}
?>
