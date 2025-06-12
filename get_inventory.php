<?php
require_once 'config.php';
header('Content-Type: application/json');
try {
    $stmt = $pdo->prepare("SELECT i.quantity, p.product_name, p.product_code, p.category, p.unit_price
                            FROM inventory i
                            JOIN products p ON i.product_id = p.id
                            ORDER BY p.product_name ASC");
    $stmt->execute();
    $inventoryItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'inventory' => $inventoryItems]);
} catch (PDOException $e) {
    error_log('Database error in get_inventory.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    error_log('General error in get_inventory.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred: ' . $e->getMessage()]);
}
?>
