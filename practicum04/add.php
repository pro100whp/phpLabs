<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $event_date = $_POST['event_date'];
    $venue = trim($_POST['venue']);
    $seats_left = (int)$_POST['seats_left'];

    $stmt = $pdo->prepare('INSERT INTO events (title, event_date, venue, seats_left) VALUES (:title, :event_date, :venue, :seats_left)');
    $stmt->execute([
        ':title' => $title,
        ':event_date' => $event_date,
        ':venue' => $venue,
        ':seats_left' => $seats_left
    ]);

    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Додати подію</title>
</head>
<body style="font-family: Arial; padding: 20px; max-width: 400px; margin: 0 auto;">
    <h2>Нова подія</h2>
    <form method="post">
        <p><label>Назва:</label><br><input type="text" name="title" required style="width: 100%;"></p>
        <p><label>Дата:</label><br><input type="date" name="event_date" required style="width: 100%;"></p>
        <p><label>Локація:</label><br><input type="text" name="venue" required style="width: 100%;"></p>
        <p><label>Кількість місць:</label><br><input type="number" name="seats_left" min="0" required style="width: 100%;"></p>
        <button type="submit" style="padding: 10px; background: #28a745; color: #fff; border: none; width: 100%;">Зберегти</button>
    </form>
</body>
</html>