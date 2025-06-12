<?php
require_once 'auth.php';
require_once 'config.php';
if (!hasRole('warehouse') && !hasRole('admin')) {
    http_response_code(403);
    echo json_encode(['error' => 'Nav atļauts piekļūt šai funkcijai']);
    exit;
}
$code = isset($_GET['code']) ? trim($_GET['code']) : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (empty($code) && empty($id)) {
    http_response_code(400);
    echo json_encode(['error' => 'Produkta kods vai ID nav norādīts']);
    exit;
}
try {
    if ($id > 0) {
        $stmt = $pdo->prepare("
            SELECT id, product_code, product_name, category, barcode, unit_price, min_stock_level, description
            FROM products 
            WHERE id = ?
        ");
        $stmt->execute([$id]);
    } else {
        $stmt = $pdo->prepare("
            SELECT id, product_code as code, product_name as name, category, barcode 
            FROM products 
            WHERE product_code = ? OR barcode = ?
        ");
        $stmt->execute([$code, $code]);
    }
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
