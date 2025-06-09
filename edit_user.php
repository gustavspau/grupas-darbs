<?php
require_once 'auth.php';
require_once 'config.php';

// Only allow admin users to edit users
if (!isAdmin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Nav atļauts rediģēt lietotājus']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$required_fields = ['id', 'first_name', 'last_name', 'email', 'role'];
foreach ($required_fields as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(['error' => 'Visiem laukiem jābūt aizpildītiem']);
        exit;
    }
}

try {
    // Check if email already exists for other users
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$data['email'], $data['id']]);
    if ($stmt->rowCount() > 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Šāds e-pasts jau eksistē']);
        exit;
    }

    // Update user
    $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, role = ?";
    $params = [$data['first_name'], $data['last_name'], $data['email'], $data['role']];

    // If password is provided, update it too
    if (!empty($data['password'])) {
        $sql .= ", password = ?";
        $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
    }

    $sql .= " WHERE id = ?";
    $params[] = $data['id'];

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Lietotājs veiksmīgi atjaunināts'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Kļūda atjauninot lietotāju: ' . $e->getMessage()]);
}
?> 