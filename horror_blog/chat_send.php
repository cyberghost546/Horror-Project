<?php
session_start();
require __DIR__ . '/include/db.php';

if (empty($_SESSION['user_id']) || empty($_POST['message'])) {
    exit;
}

$message = trim($_POST['message']);

$stmt = $pdo->prepare("
    INSERT INTO chat_messages (user_id, message)
    VALUES (:u, :m)
");

$stmt->execute([
    ':u' => $_SESSION['user_id'],
    ':m' => $message
]);

echo json_encode([
    'user' => $_SESSION['username'],
    'user_id' => $_SESSION['user_id'],
    'message' => htmlspecialchars($message)
]);
