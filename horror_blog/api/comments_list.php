<?php
session_start();
require '../include/db.php';

header('Content-Type: application/json');

$story_id = $_GET['story_id'] ?? null;

if (!$story_id || !ctype_digit($story_id)) {
    echo json_encode(['success' => false]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT c.id, c.content, c.created_at, u.display_name
    FROM comments c
    JOIN users u ON u.id = c.user_id
    WHERE c.story_id = :id
    ORDER BY c.created_at DESC
");
$stmt->execute([':id' => $story_id]);

echo json_encode([
    'success' => true,
    'comments' => $stmt->fetchAll()
]);
