<?php
session_start();
require '../include/db.php';

header('Content-Type: application/json');

$limit = isset($_GET['limit']) && ctype_digit($_GET['limit']) ? (int)$_GET['limit'] : 20;

$sql = "
    SELECT 
        s.id,
        s.title,
        s.slug,
        s.category,
        SUBSTRING(s.content, 1, 200) AS excerpt,
        s.created_at,
        s.likes,
        u.display_name AS author
    FROM stories s
    JOIN users u ON u.id = s.user_id
    WHERE s.is_published = 1
    ORDER BY s.created_at DESC
    LIMIT :limit
";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();

$stories = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'data' => $stories
]);
