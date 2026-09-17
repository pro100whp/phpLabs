<?php
require_once 'db.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare('DELETE FROM events WHERE id = :id');
    $stmt->execute([':id' => $id]);
}

header('Location: index.php');
exit;
?>