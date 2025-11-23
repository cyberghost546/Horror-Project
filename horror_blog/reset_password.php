<?php
session_start();
require 'include/db.php';
$errors = [];
$success = '';
$token = $_GET['token'] ?? '';
if ($token === '') {
    http_response_code(400);
    $errors[] = 'Invalid or missing token';
} else {
    // Look up token 
    $stmt = $pdo->prepare('SELECT pr.id, pr.user_id, pr.expires_at, u.id AS uid FROM password_resets pr JOIN users u ON pr.user_id = u.id WHERE pr.token = :token LIMIT 1');
    $stmt->execute([':token' => $token]);
    $row = $stmt->fetch();
    if (!$row) {
        $errors[] = 'This reset link is invalid';
    } else {
        $now = new DateTime();
        $expiresAt = new DateTime($row['expires_at']);
        if ($expiresAt < $now) {
            $errors[] = 'This reset link has expired';
        } else {
            // valid token, handle POST 
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $password = $_POST['password'] ?? '';
                $password2 = $_POST['password_confirm'] ?? '';
                if ($password === '' || $password2 === '') {
                    $errors[] = 'Password and confirm password are required';
                } elseif ($password !== $password2) {
                    $errors[] = 'Passwords do not match';
                } elseif (strlen($password) < 8) {
                    $errors[] = 'Password must be at least 8 characters';
                }
                if (!$errors) {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $pdo->beginTransaction();
                    try {
                        // update user password
                        $stmt = $pdo->prepare('UPDATE users SET password_hash = :ph WHERE id = :uid');
                        $stmt->execute([':ph' => $hash, ':uid' => $row['user_id'],]);
                        // delete used token 
                        $stmt = $pdo->prepare('DELETE FROM password_resets WHERE id = :id');
                        $stmt->execute([':id' => $row['id']]);
                        $pdo->commit();
                        $success = 'Your password has been reset. You can log in now.';
                    } catch (Exception $e) {
                        $pdo->rollBack();
                        $errors[] = 'Something went wrong. Please try again';
                    }
                }
            }
        }
    }
} ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Reset password | Silent Evidence</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger alert-small">
        <?php foreach ($errors as $err) echo '<div>' . htmlspecialchars($err) . '</div>'; ?>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success alert-small">
        <?php echo htmlspecialchars($success); ?>
    </div>

    <p class="small-text">
        <a href="login.php">Go to login</a>
    </p>
<?php endif; ?>

<?php if (!$success && empty($errors) === false && $token === ''): ?>
    <!-- invalid token, nothing to show -->
<?php endif; ?>

<?php if ($token && !$success && (empty($errors) || !in_array('This reset link has expired', $errors))): ?>
    <p class="auth-subtitle">
        Choose a new password for your account
    </p>

    <form method="post" novalidate>
        <div class="mb-3">
            <label class="label-text">New password</label>
            <input
                type="password"
                name="password"
                class="form-control"
                placeholder="New password"
                required>
        </div>

        <div class="mb-3">
            <label class="label-text">Confirm password</label>
            <input
                type="password"
                name="password_confirm"
                class="form-control"
                placeholder="Confirm password"
                required>
        </div>

        <button type="submit" class="btn btn-login mt-3">
            Reset password
        </button>
    </form>
<?php endif; ?>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>