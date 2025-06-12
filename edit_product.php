<?php


error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

require_once 'auth.php';
require_once 'config.php';

if (!isAdmin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Nav atļauts rediģēt produktus']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

function validateProductData($data, $pdo, $isEdit = false, $productId = null) {
    $errors = [];
    $required_fields = ['product_code', 'product_name', 'category', 'unit_price', 'min_stock_level'];
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || trim($data[$field]) === '') {
            $errors[] = "Lauks '{$field}' ir obligāts";
        }
    }
    if (!empty($errors)) {
        return $errors;
    }
    $product_code = trim($data['product_code']);
    if (strlen($product_code) < 3 || strlen($product_code) > 20) {
        $errors[] = 'Produkta kodam jābūt no 3 līdz 20 simboliem';
    }
    if (!preg_match('/^[A-Z0-9_-]+$/i', $product_code)) {
        $errors[] = 'Produkta kods drīkst saturēt tikai burtus, ciparus, zemsvītras un domuzīmes';
    }
    $product_name = trim($data['product_name']);
    if (strlen($product_name) < 2 || strlen($product_name) > 100) {
        $errors[] = 'Produkta nosaukumam jābūt no 2 līdz 100 simboliem';
    }
    if (!preg_match('/^[\p{L}\p{N}\s\-\.,&()]+$/u', $product_name)) {
        $errors[] = 'Produkta nosaukums satur neatļautus simbolus';
    }
    $category = trim($data['category']);
    if (strlen($category) < 2 || strlen($category) > 50) {
        $errors[] = 'Kategorijas nosaukumam jābūt no 2 līdz 50 simboliem';
    }
    $unit_price = $data['unit_price'];
    if (!is_numeric($unit_price) || $unit_price < 0) {
        $errors[] = 'Vienības cenai jābūt pozitīvam skaitlim';
    }
    if ($unit_price > 999999.99) {
        $errors[] = 'Vienības cena nedrīkst pārsniegt 999999.99 EUR';
    }
    $min_stock = $data['min_stock_level'];
    if (!is_numeric($min_stock) || $min_stock < 0 || $min_stock != floor($min_stock)) {
        $errors[] = 'Minimālajam krājuma līmenim jābūt pozitīvam veselam skaitlim';
    }
    if ($min_stock > 999999) {
        $errors[] = 'Minimālais krājuma līmenis nedrīkst pārsniegt 999999';
    }
    if (!empty($data['barcode'])) {
        $barcode = trim($data['barcode']);
        if (!preg_match('/^[0-9]{8,13}$/', $barcode)) {
            $errors[] = 'Svītrkods drīkst saturēt tikai ciparus un būt 8-13 simbolu garš';
        }
        if ($isEdit) {
            $stmt = $pdo->prepare("SELECT id FROM products WHERE barcode = ? AND id != ?");
            $stmt->execute([$barcode, $productId]);
        } else {
            $stmt = $pdo->prepare("SELECT id FROM products WHERE barcode = ?");
            $stmt->execute([$barcode]);
        }
        if ($stmt->rowCount() > 0) {
            $errors[] = 'Šāds svītrkods jau eksistē sistēmā';
        }
    }
    if (!empty($data['description'])) {
        $description = trim($data['description']);
        if (strlen($description) > 500) {
            $errors[] = 'Apraksts nedrīkst pārsniegt 500 simbolus';
        }
    }
    if ($isEdit) {
        $stmt = $pdo->prepare("SELECT id FROM products WHERE product_code = ? AND id != ?");
        $stmt->execute([$product_code, $productId]);
    } else {
        $stmt = $pdo->prepare("SELECT id FROM products WHERE product_code = ?");
        $stmt->execute([$product_code]);
    }
    if ($stmt->rowCount() > 0) {
        $errors[] = 'Šāds produkta kods jau eksistē sistēmā';
    }
    return $errors;
}

if (!isset($data['id']) || !is_numeric($data['id']) || $data['id'] <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Nederīgs produkta ID']);
    exit;
}

$product_id = intval($data['id']);

try {
    $stmt = $pdo->prepare("SELECT id FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Produkts nav atrasts']);
        exit;
    }
} catch (PDOException $e) {
    error_log("Database error checking product existence: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Sistēmas kļūda']);
    exit;
}

$validation_errors = validateProductData($data, $pdo, true, $product_id);
if (!empty($validation_errors)) {
    http_response_code(400);
    echo json_encode([
        'error' => 'Validācijas kļūdas',
        'validation_errors' => $validation_errors
    ]);
    exit;
}

try {
    $product_code = strtoupper(trim($data['product_code']));
    $product_name = trim($data['product_name']);
    $category = trim($data['category']);
    $barcode = !empty($data['barcode']) ? trim($data['barcode']) : null;
    $description = !empty($data['description']) ? trim($data['description']) : null;
    $unit_price = round(floatval($data['unit_price']), 2);
    $min_stock_level = intval($data['min_stock_level']);
    $stmt = $pdo->prepare("
        UPDATE products 
        SET product_code = ?, product_name = ?, category = ?, barcode = ?, 
            unit_price = ?, min_stock_level = ?, description = ?, updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([
        $product_code,
        $product_name,
        $category,
        $barcode,
        $unit_price,
        $min_stock_level,
        $description,
        $product_id
    ]);
    echo json_encode([
        'success' => true,
        'message' => 'Produkts veiksmīgi atjaunināts',
        'product' => [
            'id' => $product_id,
            'product_code' => $product_code,
            'product_name' => $product_name,
            'category' => $category,
            'unit_price' => $unit_price,
            'min_stock_level' => $min_stock_level
        ]
    ]);
} catch (PDOException $e) {
    error_log("Database error in edit_product.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Sistēmas kļūda. Lūdzu mēģiniet vēlāk.']);
}
?>
