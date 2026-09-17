<?php
require_once 'db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    $stmt = $pdo->prepare('INSERT INTO events (title, event_date, venue, seats_left) VALUES (:title, :event_date, :venue, :seats_left)');
    $stmt->execute([
        ':title' => $data['title'],
        ':event_date' => $data['event_date'],
        ':venue' => $data['venue'],
        ':seats_left' => $data['seats_left']
    ]);
    
    http_response_code(201);
    echo json_encode(['status' => 'success']);
}