<?php
session_start();
require '../include/db.php';

header('Content-Type: application/json');

$id = $_GET['id'] ?? null;

if (!$id || !ctype_digit($id)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Invalid story id'
    ]);
    exit;
}

$id = (int)$id;

$sql = "
    SELECT 
        s.id,
        s.title,
        s.slug,
        s.content,
        s.category,
        s.created_at,
        s.likes,
        u.display_name AS author
    FROM stories s
    JOIN users u ON u.id = s.user_id
    WHERE s.id = :id AND s.is_published = 1
    LIMIT 1
";

$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$story = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$story) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'error' => 'Story not found'
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'data' => $story
]);
