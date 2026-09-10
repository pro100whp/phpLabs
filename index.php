<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);


$books = [
    ['title' => '1984', 'author' => 'Джордж Оруелл', 'year' => 1949, 'pages' => 328, 'isAvailable' => true],
    ['title' => 'Майстер і Маргарита', 'author' => 'Михайло Булгаков', 'year' => 1967, 'pages' => 480, 'isAvailable' => false],
    ['title' => 'Кобзар', 'author' => 'Тарас Шевченко', 'year' => 1840, 'pages' => 240, 'isAvailable' => true],
    ['title' => 'Дюна', 'author' => 'Френк Герберт', 'year' => 1965, 'pages' => 704, 'isAvailable' => false],
    ['title' => 'Тіні забутих предків', 'author' => 'Михайло Коцюбинський', 'year' => 1911, 'pages' => 120, 'isAvailable' => true]
];


function formatBook(array $book): string {
    return "<b>{$book['title']}</b> (Автор: {$book['author']}, Рік: {$book['year']})";
}


$totalPages = 0;
$availableCount = 0;

foreach ($books as $book) {
    $totalPages += $book['pages'];
    if ($book['isAvailable']) {
        $availableCount++;
    }
}
$averagePages = count($books) > 0 ? round($totalPages / count($books)) : 0;
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Бібліотека - Практична 1</title>
    <!-- Подключаем наш файл стилей -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Каталог книг (Варіант 1)</h1>

    <div class="books-container">
        <?php
        
        foreach ($books as $book) {
            
            
            if ($book['isAvailable']) {
                $status = '<span class="status-available">Доступна</span>';
            } else {
                $status = '<span class="status-issued">Видана</span>';
            }

            echo '<div class="book-card">';
            echo '<p>Назва: ' . formatBook($book) . '</p>';
            echo '<p>Кількість сторінок: ' . $book['pages'] . '</p>';
            echo '<p>Статус: ' . $status . '</p>';
            echo '</div>';
        }
        ?>
    </div>

    <!-- Блок з агрегатними показниками -->
    <div class="summary">
        <h2>Статистика бібліотеки:</h2>
        <p>Середня кількість сторінок: <b><?= $averagePages ?></b> стор.</p>
        <p>Кількість доступних книг: <b><?= $availableCount ?></b> шт.</p>
    </div>
</body>
</html>