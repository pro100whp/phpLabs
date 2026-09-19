<?php
session_start();
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
    error_log("DB Connection error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Внутрішня помилка сервера']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$resource = $_GET['resource'] ?? null;
$action = $_GET['action'] ?? null;
$venueFilter = $_GET['venue'] ?? '';

if ($resource !== 'events') {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Ресурс не знайдено']);
    exit;
}

if ($method === 'GET') {
    try {
        if ($venueFilter !== '') {
            $stmt = $pdo->prepare('SELECT * FROM events WHERE venue LIKE :venue ORDER BY event_date ASC');
            $stmt->execute([':venue' => "%$venueFilter%"]);
        } else {
            $stmt = $pdo->query('SELECT * FROM events ORDER BY event_date ASC');
        }
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    } catch (PDOException $e) {
        error_log("SQL Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Помилка отримання даних']);
    }
    
} elseif ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    // Перевірка CSRF токена: спочатку перевіряємо чи він взагалі є в сесії, щоб уникнути hash_equals('', '')
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $input['csrf_token'] ?? '')) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Недійсний або відсутній CSRF-токен']);
        exit;
    }

    if ($action === 'buy') {
        if (!isset($input['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Відсутній ID події']);
            exit;
        }
        try {
            $stmt = $pdo->prepare('UPDATE events SET seats_left = seats_left - 1 WHERE id = :id AND seats_left > 0');
            $stmt->execute([':id' => $input['id']]);
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'data' => 'Квиток успішно придбано']);
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Місць більше немає або невірний ID']);
            }
        } catch (PDOException $e) {
            error_log("SQL Error (buy): " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Помилка при покупці']);
        }
    } elseif ($action === null) {
        if (empty($input['title']) || empty($input['event_date']) || empty($input['venue']) || !isset($input['seats_left'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Відсутні обов\'язкові поля']);
            exit;
        }
        try {
            $stmt = $pdo->prepare('INSERT INTO events (title, event_date, venue, seats_left) VALUES (:title, :event_date, :venue, :seats_left)');
            $stmt->execute([
                ':title' => $input['title'],
                ':event_date' => $input['event_date'],
                ':venue' => $input['venue'],
                ':seats_left' => $input['seats_left']
            ]);
            http_response_code(201);
            echo json_encode(['success' => true, 'data' => ['id' => $pdo->lastInsertId()]]);
        } catch (PDOException $e) {
            error_log("SQL Error (insert): " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Помилка збереження даних']);
        }
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Метод не підтримується']);
}