<?php
require_once 'auth.php';
require_once 'config.php';

// Only allow admin users to add new users
if (!isAdmin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Nav atļauts pievienot lietotājus']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$required_fields = ['first_name', 'last_name', 'email', 'password', 'role'];
foreach ($required_fields as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(['error' => 'Visiem laukiem jābūt aizpildītiem']);
        exit;
    }
}

try {
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$data['email']]);
    if ($stmt->rowCount() > 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Šāds e-pasts jau eksistē']);
        exit;
    }

    // Hash the password
    $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $pdo->prepare("
        INSERT INTO users (first_name, last_name, email, password, role, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");

    $stmt->execute([
        $data['first_name'],
        $data['last_name'],
        $data['email'],
        $hashed_password,
        $data['role']
    ]);

    // Get the new user's ID
    $user_id = $pdo->lastInsertId();

    // Return success response with the new user's data
    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $user_id,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'role' => $data['role']
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Kļūda pievienojot lietotāju: ' . $e->getMessage()]);
}
?> 