<?php
require_once 'db.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seats_left = (int)$_POST['seats_left'];
    
    $stmt = $pdo->prepare('UPDATE events SET seats_left = :seats_left WHERE id = :id');
    $stmt->execute([
        ':seats_left' => $seats_left,
        ':id' => $id
    ]);

    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM events WHERE id = :id');
$stmt->execute([':id' => $id]);
$event = $stmt->fetch();

if (!$event) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Редагувати місця</title>
</head>
<body style="font-family: Arial; padding: 20px; max-width: 400px; margin: 0 auto;">
    <h2>Редагування: <?= htmlspecialchars($event['title']) ?></h2>
    <form method="post">
        <p><label>Залишилось місць:</label><br>
        <input type="number" name="seats_left" min="0" value="<?= $event['seats_left'] ?>" required style="width: 100%;"></p>
        <button type="submit" style="padding: 10px; background: #007bff; color: #fff; border: none; width: 100%;">Оновити</button>
    </form>
</body>
</html>