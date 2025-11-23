<?php
session_start();
require 'include/db.php';
$errors = [];
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if ($email === '') {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address';
    }
    if (!$errors) {
        // find user by email 
        $stmt = $pdo->prepare('SELECT id, email FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();
        // Do not reveal if email exists or not 
        if ($user) {
            $userId = (int) $user['id'];
            // delete old tokens for this user 
            $pdo->prepare('DELETE FROM password_resets WHERE user_id = :uid')->execute([':uid' => $userId]);
            $token = bin2hex(random_bytes(32));
            $expiresAt = (new DateTime('+1 hour'))->format('Y-m-d H:i:s');
            $stmt = $pdo->prepare('INSERT INTO password_resets (user_id, token, expires_at) VALUES (:uid, :token, :expires)');
            $stmt->execute([':uid' => $userId, ':token' => $token, ':expires' => $expiresAt,]);
            // Change this to your real base URL 
            $baseUrl = 'http://localhost/horror_blog';
            $resetLink = $baseUrl . '/reset_password.php?token=' . urlencode($token);
            // send email 
            $subject = 'Password reset for Silent Evidence';
            $message = "Hi,\n\nSomeone requested a password reset for your account.\n\n";
            $message .= "Click this link to reset your password:\n";
            $message .= $resetLink . "\n\n";
            $message .= "If you did not request this, you can ignore this email.\n";
            $headers = "From: no-reply@silentevidence.local\r\n";
            // This uses PHP mail. Later you can switch to PHPMailer 
            @mail($user['email'], $subject, $message, $headers);
        }
        // Always show success text 
        $success = 'If this email exists, a reset link has been sent';
    }
} ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Forgot password | Silent Evidence</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body> <?php include 'include/header.php'; ?> <div class="auth-wrapper">
        <div class="auth-card">
            <h1 class="auth-title">Forgot password</h1>
            <p class="auth-subtitle"> Enter your email to receive a reset link </p>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger alert-small">
                    <?php foreach ($errors as $err) echo '<div>' . htmlspecialchars($err) . '</div>'; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success alert-small">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form method="post" novalidate>
                <div class="mb-3">
                    <label class="label-text">Email address</label>
                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        placeholder="you@example.com"
                        value="<?php echo htmlspecialchars($_POST['email'] ?? '') ?>"
                        required>
                </div>

                <button type="submit" class="btn btn-login mt-3">
                    Send reset link
                </button>
            </form>

            <p class="small-text">
                Remembered your password
                <a href="login.php">Back to login</a>
            </p>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>