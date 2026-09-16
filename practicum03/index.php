<?php
require_once 'lib/functions.php';
require_once 'classes/Event.php';
require_once 'classes/TicketedEvent.php';
require_once 'classes/TicketOffice.php';

$office = new TicketOffice();

$office->addEvent(new Event('Виставка картин "Весна"', '2026-10-10', 'Галерея Арт'));
$office->addEvent(new TicketedEvent('Стендап-шоу', '2026-11-05', 'Клуб Сміх', 400, 15));
$office->addEvent(new TicketedEvent('Великий концерт', '2026-12-01', 'Арена', 1200, 0));
$office->addEvent(new TicketedEvent('Кінопоказ', '2026-10-20', 'ТРЦ Плаза', 250, 0));

?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Афіша подій</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 800px; margin: 0 auto; line-height: 1.6; color: #333; }
        .card { border: 1px solid #ccc; padding: 15px; margin-bottom: 10px; border-radius: 5px; background: #fff; }
        .sold-out { background-color: #ffe6e6; border-color: #ffcccc; }
        h2 { border-bottom: 2px solid #eee; padding-bottom: 5px; margin-top: 30px; }
    </style>
</head>
<body style="background: #f9f9f9;">
    <h2>Усі події</h2>
    <?php foreach ($office->getAllEvents() as $event): ?>
        <div class="card">
            <?= htmlspecialchars($event->getInfo()) ?>
        </div>
    <?php endforeach; ?>

    <h2>Sold Out (немає місць)</h2>
    <?php foreach ($office->soldOutList() as $soldOut): ?>
        <div class="card sold-out">
            <?= htmlspecialchars($soldOut->getInfo()) ?>
        </div>
    <?php endforeach; ?>

    <h2>Події у локації "Арена"</h2>
    <?php foreach ($office->findByVenue('Арена') as $venueEvent): ?>
        <div class="card">
            <?= htmlspecialchars($venueEvent->getInfo()) ?>
        </div>
    <?php endforeach; ?>
</body>
</html>