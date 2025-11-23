<?php
session_start();

header('Content-Type: application/json');

if (
    !isset($_SESSION['user_id']) || 
    !isset($_SESSION['user_role']) ||
    $_SESSION['user_role'] !== 'admin'
) {
    echo json_encode([
        'admin' => false
    ]);
    exit;
}

echo json_encode([
    'admin' => true,
    'id' => (int)$_SESSION['user_id'],
    'role' => 'admin'
]);
