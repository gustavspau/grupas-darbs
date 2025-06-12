<?php
require_once 'auth.php';
require_once 'config.php';
if (!isAdmin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Nav atļauts piekļūt atskaitēm']);
    exit;
}
$report_type = isset($_GET['type']) ? $_GET['type'] : 'overview';
try {
    $data = [];
    switch ($report_type) {
        case 'overview':
            $stmt = $pdo->query("SELECT COUNT(*) as total_products FROM products");
            $data['total_products'] = $stmt->fetchColumn();
            $stmt = $pdo->query("SELECT COUNT(*) as total_users FROM users WHERE status = 'active'");
            $data['total_users'] = $stmt->fetchColumn();
            $stmt = $pdo->query("SELECT SUM(unit_price * min_stock_level) as total_value FROM products");
            $data['total_value'] = $stmt->fetchColumn() ?: 0;
            $stmt = $pdo->query("SELECT COUNT(*) as low_stock FROM products WHERE min_stock_level < 10");
            $data['low_stock_items'] = $stmt->fetchColumn();
            break;
        case 'products_by_category':
            $stmt = $pdo->query("
                SELECT category, COUNT(*) as count 
                FROM products 
                GROUP BY category 
                ORDER BY count DESC
            ");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
        case 'low_stock':
            $stmt = $pdo->query("
                SELECT product_code, product_name, category, min_stock_level, unit_price
                FROM products 
                WHERE min_stock_level < 20 
                ORDER BY min_stock_level ASC
            ");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
        case 'expensive_products':
            $stmt = $pdo->query("
                SELECT product_code, product_name, category, unit_price
                FROM products 
                ORDER BY unit_price DESC 
                LIMIT 10
            ");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
        case 'users_by_role':
            $stmt = $pdo->query("
                SELECT role, COUNT(*) as count 
                FROM users 
                WHERE status = 'active'
                GROUP BY role
            ");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
        case 'monthly_trends':
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
            $data = [];
            foreach ($months as $month) {
                $data[] = [
                    'month' => $month,
                    'products_added' => rand(10, 50),
                    'value' => rand(1000, 5000)
                ];
            }
            break;
        default:
            $data = ['error' => 'Nezināms atskaites tips'];
    }
    echo json_encode($data);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Kļūda iegūstot datus: ' . $e->getMessage()]);
}
?>
