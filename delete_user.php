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
    echo json_encode(['error' => 'Nav atļauts dzēst lietotājus']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Lietotāja ID nav norādīts']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->execute([$data['id']]);
    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Lietotājs nav atrasts']);
        exit;
    }
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$data['id']]);
    echo json_encode([
        'success' => true,
        'message' => 'Lietotājs veiksmīgi dzēsts'
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Kļūda dzēšot lietotāju: ' . $e->getMessage()]);
}
?>
