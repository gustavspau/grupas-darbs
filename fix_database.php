<?php
require_once 'config.php';

try {
    // Add category column to products table
    $sql = "ALTER TABLE products ADD COLUMN category VARCHAR(100) DEFAULT 'General' AFTER product_name";
    $pdo->exec($sql);
    echo "✅ Category column added successfully!<br>";
    
    // Update existing products with default category
    $sql2 = "UPDATE products SET category = 'General' WHERE category IS NULL OR category = ''";
    $stmt = $pdo->prepare($sql2);
    $stmt->execute();
    echo "✅ Existing products updated with default category!<br>";
    
    echo "<br>Database fixed! You can now use the edit functionality.";
    
} catch(PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "ℹ️ Category column already exists - no changes needed!";
    } else {
        echo "❌ Error: " . $e->getMessage();
    }
}
?> 