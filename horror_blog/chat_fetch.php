<?php
session_start();
require __DIR__ . '/include/db.php';

$isAdmin = ($_SESSION['user_role'] ?? '') === 'admin';

if ($isAdmin) {
    $stmt = $pdo->query("
        SELECT c.message, c.user_id, u.username
        FROM chat_messages c
        JOIN users u ON u.id = c.user_id
        ORDER BY c.id DESC
        LIMIT 100
    ");
} else {
    $stmt = $pdo->prepare("
        SELECT c.message, c.user_id, u.username
        FROM chat_messages c
        JOIN users u ON u.id = c.user_id
        WHERE c.user_id = :uid
        ORDER BY c.id DESC
        LIMIT 50
    ");
    $stmt->execute([':uid' => $_SESSION['user_id']]);
}

$messages = array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC));

foreach ($messages as $m) {
    $class = $m['user_id'] == $_SESSION['user_id'] ? 'you' : '';
    echo "<div class='chat-msg $class'>
            <strong>{$m['username']}:</strong> " .
        htmlspecialchars($m['message']) .
        "</div>";
}
