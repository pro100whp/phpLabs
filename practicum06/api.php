<?php
header('Content-Type: application/json');

$dsn = 'mysql:host=localhost;dbname=practicum4;charset=utf8mb4';
$user = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$resource = $_GET['resource'] ?? null;
$id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? null;

if ($resource !== 'events') {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Resource not found']);
    exit;
}

if ($method === 'GET') {
    if ($id === null) {
        $stmt = $pdo->query('SELECT * FROM events ORDER BY event_date ASC');
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    } else {
        $stmt = $pdo->prepare('SELECT * FROM events WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $event = $stmt->fetch();
        if ($event) {
            echo json_encode(['success' => true, 'data' => $event]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Event not found']);
        }
    }
} elseif ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    if ($action === 'buy') {
        if (!isset($input['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing event id for purchase']);
            exit;
        }
        $stmt = $pdo->prepare('UPDATE events SET seats_left = seats_left - 1 WHERE id = :id AND seats_left > 0');
        $stmt->execute([':id' => $input['id']]);
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'data' => 'Ticket purchased successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'No seats left or invalid event id']);
        }
    } elseif ($action === null) {
        if (empty($input['title']) || empty($input['event_date']) || empty($input['venue']) || !isset($input['seats_left'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing required fields (title, event_date, venue, seats_left)']);
            exit;
        }
        $stmt = $pdo->prepare('INSERT INTO events (title, event_date, venue, seats_left) VALUES (:title, :event_date, :venue, :seats_left)');
        $stmt->execute([
            ':title' => $input['title'],
            ':event_date' => $input['event_date'],
            ':venue' => $input['venue'],
            ':seats_left' => $input['seats_left']
        ]);
        http_response_code(201);
        echo json_encode(['success' => true, 'data' => ['id' => $pdo->lastInsertId()]]);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Unknown action']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method Not Allowed']);
}