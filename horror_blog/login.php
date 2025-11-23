<?php
session_start();
require 'include/db.php';

// always define this so PHP does not complain
$errors = [];

// If already logged in redirect
if (!empty($_SESSION['user_id'])) {
    if (!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
        header('Location: dashboard.php');
    } else {
        header('Location: index.php');
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $login    = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($login === '' || $password === '') {
        $errors[] = 'Login and password are required';
    }

    if (!$errors) {
        $stmt = $pdo->prepare(
            'SELECT id, username, email, password_hash, display_name, avatar, role
               FROM users
              WHERE username = :login OR email = :login
              LIMIT 1'
        );
        $stmt->execute([':login' => $login]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $errors[] = 'Login or password is wrong';
        } else {
            $stmt = $pdo->prepare('UPDATE users SET last_login = NOW() WHERE id = :id');
            $stmt->execute([':id' => $user['id']]);

            $_SESSION['user_id']     = $user['id'];
            $_SESSION['user_name']   = $user['display_name'] ?: $user['username'];
            $_SESSION['user_avatar'] = $user['avatar'];
            $_SESSION['user_role']   = $user['role'];

            if ($user['role'] === 'admin') {
                header('Location: dashboard.php');
            } else {
                header('Location: index.php');
            }
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Login | silent_evidence</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <?php include 'include/header.php'; ?>

    <div class="auth-wrapper">
        <div class="auth-card">

            <h1 class="auth-title">Log in</h1>
            <p class="auth-subtitle">
                Welcome back to Silent Evidence
            </p>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger alert-small">
                    <?php foreach ($errors as $err) echo '<div>' . htmlspecialchars($err) . '</div>'; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($_GET['registered'])): ?>
                <div class="alert alert-success alert-small">
                    Account created, you can log in now.
                </div>
            <?php endif; ?>

            <form method="post" novalidate>
                <div class="mb-3">
                    <label class="label-text">Username or email</label>
                    <input
                        type="text"
                        name="login"
                        class="form-control"
                        placeholder="yourname or you@example.com"
                        value="<?php echo htmlspecialchars($_POST['login'] ?? '') ?>"
                        required>
                </div>

                <div class="mb-2">
                    <label class="label-text">Password</label>
                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        placeholder="Your password"
                        required>
                </div>

                <p class="small-text">
                    <a href="forgot_password.php">Forgot your password?</a>
                </p>

                <button type="submit" class="btn btn-login mt-3">
                    Log in
                </button>

            </form>

            <p class="small-text">
                No account yet
                <a href="signup.php">Sign up</a>
            </p>

            <div class="divider">
                <span>or</span>
            </div>

            <button type="button" class="google-btn">
                <img
                    src="https://www.svgrepo.com/show/475656/google-color.svg"
                    width="18"
                    class="me-2"
                    alt="G">
                Continue with Google
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>