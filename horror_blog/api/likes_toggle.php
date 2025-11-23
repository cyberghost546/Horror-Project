<?php
session_start();
require '../include/db.php';

header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Not logged in'
    ]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$story_id = $data['story_id'] ?? null;

if (!$story_id || !ctype_digit("$story_id")) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid story id'
    ]);
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// Check if user already liked
$stmt = $pdo->prepare("
    SELECT id FROM story_likes
    WHERE story_id = :sid AND user_id = :uid
    LIMIT 1
");
$stmt->execute([
    ':sid' => $story_id,
    ':uid' => $user_id
]);
$liked = $stmt->fetch();

if ($liked) {
    // If liked, remove like
    $deleteStmt = $pdo->prepare("
        DELETE FROM story_likes 
        WHERE story_id = :sid AND user_id = :uid
    ");
    $deleteStmt->execute([
        ':sid' => $story_id,
        ':uid' => $user_id
    ]);
    $status = 'unliked';
} else {
    // Add like
    $insertStmt = $pdo->prepare("
        INSERT INTO story_likes (story_id, user_id)
        VALUES (:sid, :uid)
    ");
    $insertStmt->execute([
        ':sid' => $story_id,
        ':uid' => $user_id
    ]);
    $status = 'liked';
}

echo json_encode([
    'success' => true,
    'status' => $status
]);
