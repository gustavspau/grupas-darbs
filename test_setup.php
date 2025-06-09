<?php
echo "<h2>Testing Warehouse Management System Setup</h2>";

// Test 1: Check PHP version
echo "<h3>1. PHP Version:</h3>";
echo "PHP Version: " . phpversion() . "<br>";

// Test 2: Check if MySQL extension is loaded
echo "<h3>2. Database Extensions:</h3>";
echo "PDO: " . (extension_loaded('pdo') ? '✅ Available' : '❌ Not available') . "<br>";
echo "PDO MySQL: " . (extension_loaded('pdo_mysql') ? '✅ Available' : '❌ Not available') . "<br>";

// Test 3: Try to connect to database
echo "<h3>3. Database Connection:</h3>";
try {
    $pdo = new PDO("mysql:host=localhost;charset=utf8mb4", "root", "");
    echo "✅ Can connect to MySQL server<br>";
    
    // Check if database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE 'warehouse_management'");
    if ($stmt->fetch()) {
        echo "✅ Database 'warehouse_management' exists<br>";
        
        // Connect to the specific database
        $pdo = new PDO("mysql:host=localhost;dbname=warehouse_management;charset=utf8mb4", "root", "");
        echo "✅ Can connect to warehouse_management database<br>";
        
        // Check if tables exist
        $tables = ['users', 'products', 'shelves', 'inventory', 'orders', 'tasks'];
        echo "<h4>Tables check:</h4>";
        foreach ($tables as $table) {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->fetch()) {
                echo "✅ Table '$table' exists<br>";
            } else {
                echo "❌ Table '$table' missing<br>";
            }
        }
        
        // Check if there are any users
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch();
        echo "<br>User count: " . $result['count'] . "<br>";
        
    } else {
        echo "❌ Database 'warehouse_management' does not exist<br>";
        echo "<strong>Please run the warehouse_management.sql script in phpMyAdmin first!</strong><br>";
    }
    
} catch(PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
    echo "<strong>Please make sure XAMPP is running and MySQL is started!</strong><br>";
}

// Test 4: Check file permissions
echo "<h3>4. File Structure:</h3>";
$files = ['index.php', 'login.php', 'auth.php', 'config.php', 'styles.css', 'script.js'];
foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file missing<br>";
    }
}

echo "<h3>5. Next Steps:</h3>";
echo "1. Make sure XAMPP is running<br>";
echo "2. Import warehouse_management.sql in phpMyAdmin<br>";
echo "3. Run create_test_user.php to create test users<br>";
echo "4. Access login.php to test the system<br>";
?> 