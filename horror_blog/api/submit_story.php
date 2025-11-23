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

$title = trim($data['title'] ?? '');
$category = trim($data['category'] ?? '');
$content = trim($data['content'] ?? '');
$image = trim($data['image'] ?? '');

if ($title === '' || $content === '') {
    echo json_encode([
        'success' => false,
        'error' => 'Title and content are required'
    ]);
    exit;
}

$slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $title));
$slug = trim($slug, '-');

$stmt = $pdo->prepare("
    INSERT INTO stories (user_id, title, slug, category, content, image, is_published, created_at)
    VALUES (:uid, :title, :slug, :category, :content, :image, 1, NOW())
");

$ok = $stmt->execute([
    ':uid' => $_SESSION['user_id'],
    ':title' => $title,
    ':slug' => $slug,
    ':category' => $category,
    ':content' => $content,
    ':image' => $image
]);

echo json_encode([
    'success' => $ok
]);
