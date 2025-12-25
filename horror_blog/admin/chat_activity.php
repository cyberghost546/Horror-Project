<?php
session_start();
require '../include/db.php';

if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    exit;
}

$stmt = $pdo->query("
    SELECT message
    FROM chat_messages
    WHERE created_at >= NOW() - INTERVAL 30 MINUTE
");

$keywords = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $msg = strtolower($row['message']);
    $msg = preg_replace('/[^a-z0-9\s]/', '', $msg);
    $parts = explode(' ', $msg);

    foreach ($parts as $word) {
        if (strlen($word) < 4) continue;
        $keywords[$word] = ($keywords[$word] ?? 0) + 1;
    }
}

arsort($keywords);

echo json_encode([
    'keywords' => array_slice($keywords, 0, 10, true)
]);
