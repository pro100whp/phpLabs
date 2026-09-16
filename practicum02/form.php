<?php
$errors = [];
$successMessage = '';
$eventTitle = '';
$seatsCount = '';
$buyerEmail = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventTitle = trim($_POST['eventTitle'] ?? '');
    $seatsCount = $_POST['seatsCount'] ?? '';
    $buyerEmail = trim($_POST['buyerEmail'] ?? '');

    if ($eventTitle === '') {
        $errors['eventTitle'] = 'Вкажіть назву події.';
    }

    if (!filter_var($buyerEmail, FILTER_VALIDATE_EMAIL)) {
        $errors['buyerEmail'] = 'Некоректний формат e-mail.';
    }

    if (!filter_var($seatsCount, FILTER_VALIDATE_INT) || $seatsCount < 1 || $seatsCount > 10) {
        $errors['seatsCount'] = 'Кількість квитків має бути цілим числом від 1 до 10.';
    }

    if (empty($errors)) {
        $successMessage = "Успіх! Ви забронювали $seatsCount квитків на «" . htmlspecialchars($eventTitle) . "». Email: " . htmlspecialchars($buyerEmail);
        $eventTitle = $seatsCount = $buyerEmail = '';
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Бронювання квитків</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 500px; margin: 0 auto; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input { width: 100%; padding: 8px; box-sizing: border-box; }
        .error-message { color: red; font-size: 0.9em; margin-top: 5px; display: block; }
        .success-box { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        button { padding: 10px 15px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h2>Форма бронювання квитків</h2>

    <?php if ($successMessage): ?>
        <div class="success-box"><?= $successMessage ?></div>
        <script>
            localStorage.removeItem('ticket_draft');
        </script>
    <?php endif; ?>

    <form id="bookingForm" method="post" action="form.php" novalidate>
        
        <div class="form-group">
            <label for="eventTitle">Назва події:</label>
            <input type="text" id="eventTitle" name="eventTitle" required 
                   value="<?= htmlspecialchars($eventTitle) ?>">
            <?php if (isset($errors['eventTitle'])): ?>
                <span class="error-message"><?= $errors['eventTitle'] ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="seatsCount">Кількість квитків (1-10):</label>
            <input type="number" id="seatsCount" name="seatsCount" min="1" max="10" required 
                   value="<?= htmlspecialchars($seatsCount) ?>">
            <?php if (isset($errors['seatsCount'])): ?>
                <span class="error-message"><?= $errors['seatsCount'] ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="buyerEmail">Ваш E-mail:</label>
            <input type="email" id="buyerEmail" name="buyerEmail" required 
                   value="<?= htmlspecialchars($buyerEmail) ?>">
            <?php if (isset($errors['buyerEmail'])): ?>
                <span class="error-message"><?= $errors['buyerEmail'] ?></span>
            <?php endif; ?>
        </div>

        <button type="submit">Забронювати</button>
    </form>

    <script>
        const form = document.getElementById('bookingForm');
        const inputs = {
            title: document.getElementById('eventTitle'),
            seats: document.getElementById('seatsCount'),
            email: document.getElementById('buyerEmail')
        };

        window.addEventListener('DOMContentLoaded', () => {
            const savedDraft = localStorage.getItem('ticket_draft');
            if (savedDraft && !inputs.title.value && !inputs.seats.value && !inputs.email.value) {
                const data = JSON.parse(savedDraft);
                inputs.title.value = data.title || '';
                inputs.seats.value = data.seats || '';
                inputs.email.value = data.email || '';
            }
        });

        form.addEventListener('input', () => {
            const draft = {
                title: inputs.title.value,
                seats: inputs.seats.value,
                email: inputs.email.value
            };
            localStorage.setItem('ticket_draft', JSON.stringify(draft));
        });

        form.addEventListener('submit', (event) => {
            let isValid = true;
            
            const seats = parseInt(inputs.seats.value, 10);
            if (isNaN(seats) || seats < 1 || seats > 10) {
                alert('Перевірте кількість квитків (має бути від 1 до 10).');
                isValid = false;
            }

            if (!inputs.email.value.includes('@')) {
                alert('Введіть коректний email.');
                isValid = false;
            }

            if (!isValid) {
                event.preventDefault(); 
            }
        });
    </script>
</body>
</html>