<?php
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['user_id'])) {
    echo json_encode([
        'authenticated' => false
    ]);
    exit;
}

echo json_encode([
    'authenticated' => true,
    'data' => [
        'id' => (int)$_SESSION['user_id'],
        'display_name' => $_SESSION['display_name'] ?? '',
        'role' => $_SESSION['user_role'] ?? 'user'
    ]
]);
