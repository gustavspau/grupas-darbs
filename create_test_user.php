<?php
require_once 'config.php';
require_once 'auth.php';
$testUsers = [
    [
        'username' => 'admin',
        'password' => 'password',
        'first_name' => 'Administrators',
        'last_name' => 'Lietotājs',
        'email' => 'admin@warehouse.lv',
        'role' => 'admin'
    ],
    [
        'username' => 'janis.berzins',
        'password' => 'password',
        'first_name' => 'Jānis',
        'last_name' => 'Bērziņš',
        'email' => 'janis@warehouse.lv',
        'role' => 'warehouse'
    ],
    [
        'username' => 'anna.ozolina',
        'password' => 'password',
        'first_name' => 'Anna',
        'last_name' => 'Ozoliņa',
        'email' => 'anna@warehouse.lv',
        'role' => 'shelf'
    ]
];
try {
    foreach ($testUsers as $userData) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$userData['username']]);
        if ($stmt->fetch()) {
            echo "User '{$userData['username']}' already exists.<br>";
            continue;
        }
        $hashedPassword = hashPassword($userData['password']);
        $stmt = $pdo->prepare("
            INSERT INTO users (username, password, first_name, last_name, email, role) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $userData['username'],
            $hashedPassword,
            $userData['first_name'],
            $userData['last_name'],
            $userData['email'],
            $userData['role']
        ]);
        echo "Created user: {$userData['username']} ({$userData['role']})<br>";
    }
    echo "<br><strong>Test users created successfully!</strong><br>";
    echo "<br>You can now login with:<br>";
    echo "- Username: admin, Password: password (Administrator)<br>";
    echo "- Username: janis.berzins, Password: password (Warehouse Worker)<br>";
    echo "- Username: anna.ozolina, Password: password (Shelf Organizer)<br>";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
