<?php

// Turn off direct display of errors and enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

require_once 'auth.php';
require_once 'config.php';
if (!isAdmin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Nav atļauts rediģēt lietotājus']);
    exit;
}
$data = json_decode(file_get_contents('php://input'), true);
$required_fields = ['id', 'first_name', 'last_name', 'email', 'role'];
foreach ($required_fields as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(['error' => 'Visiem laukiem jābūt aizpildītiem']);
        exit;
    }
}
try {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$data['email'], $data['id']]);
    if ($stmt->rowCount() > 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Šāds e-pasts jau eksistē']);
        exit;
    }
    $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, role = ?";
    $params = [$data['first_name'], $data['last_name'], $data['email'], $data['role']];
    if (!empty($data['password'])) {
        $sql .= ", password = ?";
        $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
    }
    $sql .= " WHERE id = ?";
    $params[] = $data['id'];
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    echo json_encode([
        'success' => true,
        'message' => 'Lietotājs veiksmīgi atjaunināts'
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Kļūda atjauninot lietotāju: ' . $e->getMessage()]);
}
?>
