<?php
require_once 'db.php';

// Вибірка всіх подій
$stmt = $pdo->query("SELECT * FROM events ORDER BY event_date ASC");
$events = $stmt->fetchAll();

// Вибірка Sold Out (немає місць)
$stmtSoldOut = $pdo->query("SELECT * FROM events WHERE seats_left = 0");
$soldOutEvents = $stmtSoldOut->fetchAll();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Управління подіями</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 900px; margin: 0 auto; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        .btn { padding: 5px 10px; text-decoration: none; color: white; border-radius: 3px; }
        .btn-add { background-color: #28a745; display: inline-block; margin-bottom: 15px; padding: 10px 15px; }
        .btn-edit { background-color: #007bff; }
        .btn-delete { background-color: #dc3545; }
    </style>
</head>
<body>
    <h2>Афіша подій</h2>
    <a href="add.php" class="btn btn-add">+ Додати подію</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Назва</th>
                <th>Дата</th>
                <th>Локація</th>
                <th>Залишилось місць</th>
                <th>Дії</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($events as $event): ?>
            <tr>
                <td><?= $event['id'] ?></td>
                <td><?= htmlspecialchars($event['title']) ?></td>
                <td><?= htmlspecialchars($event['event_date']) ?></td>
                <td><?= htmlspecialchars($event['venue']) ?></td>
                <td><?= $event['seats_left'] ?></td>
                <td>
                    <a href="edit.php?id=<?= $event['id'] ?>" class="btn btn-edit">Редагувати</a>
                    <a href="delete.php?id=<?= $event['id'] ?>" class="btn btn-delete" onclick="return confirm('Ви впевнені?');">Видалити</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if (count($soldOutEvents) > 0): ?>
        <h2>Sold Out (Місць немає)</h2>
        <ul>
            <?php foreach ($soldOutEvents as $so): ?>
                <li><?= htmlspecialchars($so['title']) ?> (<?= htmlspecialchars($so['venue']) ?>)</li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>