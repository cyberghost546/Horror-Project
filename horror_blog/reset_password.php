<?php
session_start();
require 'include/db.php';

$token = $_GET['token'] ?? '';
$error = '';
$success = '';

$stmt = $pdo->prepare("
    SELECT id FROM users
    WHERE reset_token = :t
      AND reset_expires > NOW()
    LIMIT 1
");
$stmt->execute([':t' => $token]);
$user = $stmt->fetch();

if (!$user) {
    die('Invalid or expired reset link');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if ($password === '' || $password !== $confirm) {
        $error = 'Passwords do not match';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            UPDATE users
            SET password_hash = :p,
                reset_token = NULL,
                reset_expires = NULL
            WHERE id = :id
        ");
        $stmt->execute([
            ':p' => $hash,
            ':id' => $user['id']
        ]);

        $success = 'Password updated. You can now log in.';
    }
}
?>
