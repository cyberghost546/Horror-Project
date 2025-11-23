<?php
session_start();
require '../include/db.php';

header('Content-Type: application/json');

$story_id = $_GET['story_id'] ?? null;

if (!$story_id || !ctype_digit("$story_id")) {
    echo json_encode([
        'success' => false
    ]);
    exit;
}

// Count total likes
$stmt = $pdo->prepare("
    SELECT COUNT(*) AS total
    FROM story_likes
    WHERE story_id = :sid
");
$stmt->execute([':sid' => $story_id]);
$total = (int)$stmt->fetchColumn();

// Check if current user liked
$liked = false;
if (!empty($_SESSION['user_id'])) {
    $stmt2 = $pdo->prepare("
        SELECT id FROM story_likes
        WHERE story_id = :sid AND user_id = :uid
        LIMIT 1
    ");
    $stmt2->execute([
        ':sid' => $story_id,
        ':uid' => $_SESSION['user_id']
    ]);
    $liked = $stmt2->fetch() ? true : false;
}

echo json_encode([
    'success' => true,
    'likes' => $total,
    'liked' => $liked
]);
