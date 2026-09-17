<?php
require_once 'db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if ($data && isset($data['id'])) {
    $stmt = $pdo->prepare('UPDATE events SET seats_left = seats_left - 1 WHERE id = :id AND seats_left > 0');
    $stmt->execute([':id' => $data['id']]);
    
    echo json_encode(['status' => 'success']);
}