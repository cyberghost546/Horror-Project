<?php
session_start();
require '../include/db.php';

header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Login required']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$story_id = $data['story_id'] ?? null;
$content = trim($data['content'] ?? '');

if (!$story_id || !ctype_digit("$story_id") || $content === '') {
    echo json_encode(['success' => false]);
    exit;
}

$stmt = $pdo->prepare("
    INSERT INTO comments (story_id, user_id, content, created_at)
    VALUES (:sid, :uid, :content, NOW())
");

$ok = $stmt->execute([
    ':sid' => $story_id,
    ':uid' => $_SESSION['user_id'],
    ':content' => $content,
]);

echo json_encode(['success' => $ok]);
