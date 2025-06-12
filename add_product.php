<?php
// Start output buffering to prevent any unwanted output
ob_start();

// Set content type to JSON
header('Content-Type: application/json');

// Prevent any PHP errors from showing in the response
error_reporting(0);

require_once 'auth.php';
require_once 'config.php';

// Clear any previous output
ob_clean();

// Only allow admin and warehouse workers to add products
if (!isAdmin() && !hasRole('warehouse')) {
    http_response_code(403);
    echo json_encode(['error' => 'Nav atļauts pievienot produktus']);
    exit;
}

// Get POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Debug logging
error_log("Raw input: " . $input);
error_log("Decoded data: " . print_r($data, true));

// Check if data was received
if (!$data || !is_array($data)) {
    http_response_code(400);
    echo json_encode(['error' => 'Nav saņemti dati vai dati nav derīgā JSON formātā']);
    exit;
}

// Comprehensive validation function that cannot be bypassed
function validateProductData($data, $pdo, $isEdit = false, $productId = null) {
    $errors = [];
    
    // Check if all required fields exist and are not empty
    $required_fields = ['product_code', 'product_name', 'category', 'unit_price', 'min_stock_level'];
    
    foreach ($required_fields as $field) {
        if (!isset($data[$field])) {
            $errors[] = "Trūkst obligātā lauka: {$field}";
            continue;
        }
        
        $value = is_string($data[$field]) ? trim($data[$field]) : $data[$field];
        
        if ($value === '' || $value === null) {
            switch($field) {
                case 'product_code': $errors[] = 'Produkta kods ir obligāts un nedrīkst būt tukšs'; break;
                case 'product_name': $errors[] = 'Produkta nosaukums ir obligāts un nedrīkst būt tukšs'; break;
                case 'category': $errors[] = 'Kategorija ir obligāta un nedrīkst būt tukša'; break;
                case 'unit_price': $errors[] = 'Vienības cena ir obligāta un nedrīkst būt tukša'; break;
                case 'min_stock_level': $errors[] = 'Minimālais krājums ir obligāts un nedrīkst būt tukšs'; break;
            }
        }
    }
    
    // If basic validation failed, return early
    if (!empty($errors)) {
        return $errors;
    }
    
    // Product code validation - must start with PRD + 3 digits
    $product_code = trim($data['product_code']);
    if (strlen($product_code) === 0) {
        $errors[] = 'Produkta kods nedrīkst būt tukšs';
    } elseif (strlen($product_code) !== 6) {
        $errors[] = 'Produkta kodam jābūt tieši 6 simboliem (PRD + 3 cipari)';
    } elseif (!preg_match('/^PRD[0-9]{3}$/', $product_code)) {
        $errors[] = 'Produkta kods drīkst sākties tikai ar PRD + 3 cipari (piemēram: PRD001)';
    }
    
    // Product name validation
    $product_name = trim($data['product_name']);
    if (strlen($product_name) === 0) {
        $errors[] = 'Produkta nosaukums nedrīkst būt tukšs';
    } elseif (strlen($product_name) < 2) {
        $errors[] = 'Produkta nosaukumam jābūt vismaz 2 simboliem';
    } elseif (strlen($product_name) > 50) {
        $errors[] = 'Produkta nosaukums nedrīkst pārsniegt 50 simbolus';
    } elseif (!preg_match('/^[\p{L}\p{N}\s\-\.,&()]+$/u', $product_name)) {
        $errors[] = 'Produkta nosaukums satur neatļautus simbolus';
    }
    
    // Category validation
    $category = trim($data['category']);
    $valid_categories = [
        'Bulkīšu izstrādājumi', 'Šķidrums', 'Piena produkti', 
        'Dārzeņi', 'Augļi', 'Sausie augļi un rieksti', 'Saldumi'
    ];
    
    if (strlen($category) === 0) {
        $errors[] = 'Kategorija nedrīkst būt tukša';
    } elseif (!in_array($category, $valid_categories)) {
        $errors[] = 'Izvēlētā kategorija nav derīga';
    }
    
    // Unit price validation
    $unit_price = $data['unit_price'];
    if ($unit_price === '' || $unit_price === null) {
        $errors[] = 'Vienības cena nedrīkst būt tukša';
    } elseif (!is_numeric($unit_price)) {
        $errors[] = 'Vienības cenai jābūt skaitlim';
    } elseif (floatval($unit_price) < 0) {
        $errors[] = 'Vienības cena nedrīkst būt negatīva';
    } elseif (floatval($unit_price) > 999999.99) {
        $errors[] = 'Vienības cena nedrīkst pārsniegt 999999.99 EUR';
    }
    
    // Min stock level validation
    $min_stock = $data['min_stock_level'];
    if ($min_stock === '' || $min_stock === null) {
        $errors[] = 'Minimālais krājums nedrīkst būt tukšs';
    } elseif (!is_numeric($min_stock)) {
        $errors[] = 'Minimālajam krājumam jābūt skaitlim';
    } elseif (intval($min_stock) < 0) {
        $errors[] = 'Minimālais krājums nedrīkst būt negatīvs';
    } elseif (intval($min_stock) != floatval($min_stock)) {
        $errors[] = 'Minimālajam krājumam jābūt veselam skaitlim';
    } elseif (intval($min_stock) > 999999) {
        $errors[] = 'Minimālais krājums nedrīkst pārsniegt 999999';
    }
    
    // Barcode validation (if provided) - exactly 13 digits for EAN-13
    if (isset($data['barcode']) && !empty(trim($data['barcode']))) {
        $barcode = trim($data['barcode']);
        if (!preg_match('/^[0-9]{13}$/', $barcode)) {
            $errors[] = 'Svītrkods drīkst saturēt tikai 13 ciparus (EAN-13 formāts)';
        } else {
            // Check barcode uniqueness
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
    }
    
    // Description validation (if provided)
    if (isset($data['description']) && !empty(trim($data['description']))) {
        $description = trim($data['description']);
        if (strlen($description) > 200) {
            $errors[] = 'Apraksts nedrīkst pārsniegt 200 simbolus';
        }
    }
    
    // Check product code uniqueness
    if (!empty($product_code)) {
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
    }
    
    return $errors;
}

// Validate the data
$validation_errors = validateProductData($data, $pdo);

if (!empty($validation_errors)) {
    // Better error mapping for client-side display
    $mapped_errors = [];
    foreach ($validation_errors as $error) {
        if (strpos($error, 'Produkta kods') !== false) {
            $mapped_errors[] = ['field' => 'productCode', 'message' => $error];
        } elseif (strpos($error, 'nosaukum') !== false) {
            $mapped_errors[] = ['field' => 'productName', 'message' => $error];
        } elseif (strpos($error, 'Kategorija') !== false) {
            $mapped_errors[] = ['field' => 'productCategory', 'message' => $error];
        } elseif (strpos($error, 'cena') !== false) {
            $mapped_errors[] = ['field' => 'unitPrice', 'message' => $error];
        } elseif (strpos($error, 'krājum') !== false) {
            $mapped_errors[] = ['field' => 'minStock', 'message' => $error];
        } elseif (strpos($error, 'Svītrkods') !== false) {
            $mapped_errors[] = ['field' => 'barcode', 'message' => $error];
        } elseif (strpos($error, 'Apraksts') !== false) {
            $mapped_errors[] = ['field' => 'description', 'message' => $error];
        } else {
            $mapped_errors[] = ['field' => '', 'message' => $error];
        }
    }
    
    http_response_code(422);
    echo json_encode([
        'error' => 'Datu validācijas kļūdas',
        'validation_errors' => $mapped_errors,
        'debug_data' => $data // Add debug info to see what's being sent
    ]);
    exit;
}

try {
    // Sanitize data
    $product_code = strtoupper(trim($data['product_code']));
    $product_name = trim($data['product_name']);
    $category = trim($data['category']);
    $barcode = !empty($data['barcode']) ? trim($data['barcode']) : null;
    $description = !empty($data['description']) ? trim($data['description']) : null;
    $unit_price = round(floatval($data['unit_price']), 2);
    $min_stock_level = intval($data['min_stock_level']);
    
    // Insert new product
    $stmt = $pdo->prepare("
        INSERT INTO products (product_code, product_name, category, barcode, description, unit_price, min_stock_level, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $stmt->execute([
        $product_code,
        $product_name,
        $category,
        $barcode,
        $description,
        $unit_price,
        $min_stock_level
    ]);

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Produkts veiksmīgi pievienots sistēmā',
        'id' => $pdo->lastInsertId(),
        'product' => [
            'product_code' => $product_code,
            'product_name' => $product_name,
            'category' => $category,
            'unit_price' => $unit_price,
            'min_stock_level' => $min_stock_level
        ]
    ]);

} catch (PDOException $e) {
    error_log("Database error in add_product.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Sistēmas kļūda. Lūdzu mēģiniet vēlāk.']);
}
?> 