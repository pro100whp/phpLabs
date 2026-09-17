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
    $start = microtime(true);
    
    if ($action === 'soldout') {
        $cacheFile = sys_get_temp_dir() . '/cache_soldout.json';
        $ttl = 60;
        
        if (is_file($cacheFile) && (time() - filemtime($cacheFile)) < $ttl) {
            $data = json_decode(file_get_contents($cacheFile), true);
        } else {
            $stmt = $pdo->query('SELECT * FROM events WHERE seats_left = 0');
            $data = $stmt->fetchAll();
            file_put_contents($cacheFile, json_encode($data));
        }
        echo json_encode(['success' => true, 'data' => $data]);
    } elseif ($id === null) {
        $sql = '
            SELECT e.*, vc.venue_count 
            FROM events e 
            LEFT JOIN (
                SELECT venue, COUNT(*) as venue_count 
                FROM events 
                GROUP BY venue
            ) vc ON e.venue = vc.venue 
            ORDER BY e.event_date ASC
        ';
        $stmt = $pdo->query($sql);
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
    
    $elapsed = (microtime(true) - $start) * 1000;
    $peakMemory = memory_get_peak_usage(true) / 1024 / 1024;
    error_log(sprintf('Time: %.2f ms, Memory: %.2f MB', $elapsed, $peakMemory));
    
} elseif ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    if ($action === 'buy') {
        if (!isset($input['id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing event id']);
            exit;
        }
        $stmt = $pdo->prepare('UPDATE events SET seats_left = seats_left - 1 WHERE id = :id AND seats_left > 0');
        $stmt->execute([':id' => $input['id']]);
        if ($stmt->rowCount() > 0) {
            @unlink(sys_get_temp_dir() . '/cache_soldout.json');
            echo json_encode(['success' => true, 'data' => 'Ticket purchased']);
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'No seats left']);
        }
    } elseif ($action === null) {
        if (empty($input['title']) || empty($input['event_date']) || empty($input['venue']) || !isset($input['seats_left'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing required fields']);
            exit;
        }
        $stmt = $pdo->prepare('INSERT INTO events (title, event_date, venue, seats_left) VALUES (:title, :event_date, :venue, :seats_left)');
        $stmt->execute([
            ':title' => $input['title'],
            ':event_date' => $input['event_date'],
            ':venue' => $input['venue'],
            ':seats_left' => $input['seats_left']
        ]);
        @unlink(sys_get_temp_dir() . '/cache_soldout.json');
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