<?php
require_once 'auth.php';
require_once 'config.php';
if (!isAdmin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Nav atļauts skatīt lietotāju sarakstu']);
    exit;
}
try {
    $stmt = $pdo->query("SELECT id, first_name, last_name, email, role, created_at FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($users);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Kļūda ielādējot lietotājus: ' . $e->getMessage()]);
}
?>
