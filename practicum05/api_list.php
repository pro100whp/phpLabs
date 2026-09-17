<?php
require_once 'db.php';
header('Content-Type: application/json');

$q = $_GET['q'] ?? '';

if ($q !== '') {
    $stmt = $pdo->prepare('SELECT * FROM events WHERE venue LIKE :q ORDER BY event_date ASC');
    $stmt->execute([':q' => "%$q%"]);
    echo json_encode($stmt->fetchAll());
} else {
    $stmt = $pdo->query('SELECT * FROM events ORDER BY event_date ASC');
    echo json_encode($stmt->fetchAll());
}