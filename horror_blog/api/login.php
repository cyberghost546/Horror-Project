<?php
session_start();
require '../include/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed'
    ]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if ($email === '' || $password === '') {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Email and password are required'
    ]);
    exit;
}

$stmt = $pdo->prepare('SELECT id, password_hash, display_name, role FROM users WHERE email = :email LIMIT 1');
$stmt->execute([':email' => $email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password_hash'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Invalid login'
    ]);
    exit;
}

$_SESSION['user_id'] = (int)$user['id'];
$_SESSION['user_role'] = $user['role'];
$_SESSION['display_name'] = $user['display_name'];

echo json_encode([
    'success' => true,
    'data' => [
        'id' => (int)$user['id'],
        'display_name' => $user['display_name'],
        'role' => $user['role']
    ]
]);
