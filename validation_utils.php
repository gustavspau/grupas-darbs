<?php
/**
 * Product Validation Utilities
 * Shared validation functions for product operations
 */

class ProductValidator {
    
    /**
     * Comprehensive product data validation
     */
    public static function validateProductData($data, $pdo, $isEdit = false, $productId = null) {
        $errors = [];
        
        // Required fields validation
        $required_fields = ['product_code', 'product_name', 'category', 'unit_price', 'min_stock_level'];
        foreach ($required_fields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $errors[] = "Lauks '{$field}' ir obligāts";
            }
        }
        
        if (!empty($errors)) {
            return $errors;
        }
        
        // Product code validation
        $product_code = trim($data['product_code']);
        if (strlen($product_code) < 3 || strlen($product_code) > 20) {
            $errors[] = 'Produkta kodam jābūt no 3 līdz 20 simboliem';
        }
        if (!preg_match('/^[A-Z0-9_-]+$/i', $product_code)) {
            $errors[] = 'Produkta kods drīkst saturēt tikai burtus, ciparus, zemsvītras un domuzīmes';
        }
        
        // Product name validation
        $product_name = trim($data['product_name']);
        if (strlen($product_name) < 2 || strlen($product_name) > 100) {
            $errors[] = 'Produkta nosaukumam jābūt no 2 līdz 100 simboliem';
        }
        if (!preg_match('/^[\p{L}\p{N}\s\-\.,&()]+$/u', $product_name)) {
            $errors[] = 'Produkta nosaukums satur neatļautus simbolus';
        }
        
        // Category validation
        $category = trim($data['category']);
        if (strlen($category) < 2 || strlen($category) > 50) {
            $errors[] = 'Kategorijas nosaukumam jābūt no 2 līdz 50 simboliem';
        }
        
        $valid_categories = [
            'Bulkīšu izstrādājumi', 'Šķidrums', 'Piena produkti', 
            'Dārzeņi', 'Augļi', 'Sausie augļi un rieksti', 'Saldumi'
        ];
        if (!in_array($category, $valid_categories)) {
            $errors[] = 'Nederīga kategorija';
        }
        
        // Unit price validation
        $unit_price = $data['unit_price'];
        if (!is_numeric($unit_price) || $unit_price < 0) {
            $errors[] = 'Vienības cenai jābūt pozitīvam skaitlim';
        }
        if ($unit_price > 999999.99) {
            $errors[] = 'Vienības cena nedrīkst pārsniegt 999999.99 EUR';
        }
        
        // Min stock level validation
        $min_stock = $data['min_stock_level'];
        if (!is_numeric($min_stock) || $min_stock < 0 || $min_stock != floor($min_stock)) {
            $errors[] = 'Minimālajam krājuma līmenim jābūt pozitīvam veselam skaitlim';
        }
        if ($min_stock > 999999) {
            $errors[] = 'Minimālais krājuma līmenis nedrīkst pārsniegt 999999';
        }
        
        // Barcode validation (if provided)
        if (!empty($data['barcode'])) {
            $barcode = trim($data['barcode']);
            if (!preg_match('/^[0-9]{8,13}$/', $barcode)) {
                $errors[] = 'Svītrkods drīkst saturēt tikai ciparus un būt 8-13 simbolu garš';
            }
            
            // Check barcode uniqueness
            try {
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
            } catch (PDOException $e) {
                error_log("Database error checking barcode uniqueness: " . $e->getMessage());
                $errors[] = 'Kļūda pārbaudot svītrkoda unikalitāti';
            }
        }
        
        // Description validation (if provided)
        if (!empty($data['description'])) {
            $description = trim($data['description']);
            if (strlen($description) > 500) {
                $errors[] = 'Apraksts nedrīkst pārsniegt 500 simbolus';
            }
        }
        
        // Check product code uniqueness
        try {
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
        } catch (PDOException $e) {
            error_log("Database error checking product code uniqueness: " . $e->getMessage());
            $errors[] = 'Kļūda pārbaudot produkta koda unikalitāti';
        }
        
        return $errors;
    }
    
    /**
     * Sanitize product data
     */
    public static function sanitizeProductData($data) {
        return [
            'product_code' => strtoupper(trim($data['product_code'])),
            'product_name' => trim($data['product_name']),
            'category' => trim($data['category']),
            'barcode' => !empty($data['barcode']) ? trim($data['barcode']) : null,
            'description' => !empty($data['description']) ? trim($data['description']) : null,
            'unit_price' => round(floatval($data['unit_price']), 2),
            'min_stock_level' => intval($data['min_stock_level'])
        ];
    }
    
    /**
     * Validate product ID
     */
    public static function validateProductId($id) {
        if (!isset($id) || !is_numeric($id) || $id <= 0) {
            return 'Nederīgs produkta ID';
        }
        return null;
    }
    
    /**
     * Check if product exists
     */
    public static function checkProductExists($productId, $pdo) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Database error checking product existence: " . $e->getMessage());
            return false;
        }
    }
}
?> 