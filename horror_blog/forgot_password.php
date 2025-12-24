<?php
session_start();
require 'include/db.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');

    if ($login === '') {
        $error = 'Enter your email or username';
    } else {
        $stmt = $pdo->prepare("
            SELECT id, email FROM users
            WHERE email = :login OR username = :login
            LIMIT 1
        ");
        $stmt->execute([':login' => $login]);
        $user = $stmt->fetch();

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', time() + 3600);

            $stmt = $pdo->prepare("
                UPDATE users
                SET reset_token = :t,
                    reset_expires = :e
                WHERE id = :id
            ");
            $stmt->execute([
                ':t' => $token,
                ':e' => $expires,
                ':id' => $user['id']
            ]);

            // send email later, for now show link
            $success = 'Reset link generated';
            $resetLink = "http://yourdomain.com/reset_password.php?token=$token";
        } else {
            $success = 'If the account exists, a reset link was sent';
        }
    }
}
?>
