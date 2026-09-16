<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$events = [
    ['title' => 'Концерт гурту "ДахаБраха"', 'date' => '2026-10-15', 'price' => 1200, 'seatsLeft' => 45],
    ['title' => 'Вистава "Конотопська відьма"', 'date' => '2026-09-20', 'price' => 800, 'seatsLeft' => 0],
    ['title' => 'Стендап-вечір', 'date' => '2026-09-25', 'price' => 400, 'seatsLeft' => 12],
    ['title' => 'Кіно "Дюна 2"', 'date' => '2026-09-12', 'price' => 250, 'seatsLeft' => 0],
    ['title' => 'IT-конференція', 'date' => '2026-11-05', 'price' => 3000, 'seatsLeft' => 150]
];

function formatEvent(array $event): string {
    return "<b>{$event['title']}</b> (Дата: {$event['date']}, Вартість: {$event['price']} ₴)";
}

$totalExpectedIncome = 0;

foreach ($events as $event) {
    $totalExpectedIncome += ($event['price'] * $event['seatsLeft']);
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Система бронювання</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Афіша подій </h1>

    <div class="events-container">
        <?php
        foreach ($events as $event) {
            
            if ($event['seatsLeft'] == 0) {
                $status = '<span class="status-soldout">Квитків немає</span>';
            } else {
                $status = '<span class="status-available">Доступно квитків: ' . $event['seatsLeft'] . ' шт.</span>';
            }

            echo '<div class="event-card">';
            echo '<p>Подія: ' . formatEvent($event) . '</p>';
            echo '<p>Статус: ' . $status . '</p>';
            echo '</div>';
        }
        ?>
    </div>

    <div class="summary">
        <h2>Фінансова статистика:</h2>
        <p>Сумарний потенційний дохід від усіх подій: <b><?= $totalExpectedIncome ?> ₴</b></p>
    </div>
</body>
</html>