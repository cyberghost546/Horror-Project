<?php
// --------------------
// SESSION SECURITY
// --------------------
ini_set('session.use_strict_mode', 1);
ini_set('session.use_only_cookies', 1);

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => isset($_SERVER['HTTPS']),
    'httponly' => true,
    'samesite' => 'Strict'
]);

session_start();
require 'include/db.php';

// --------------------
// INIT
// --------------------
$errors = [];

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Redirect if already logged in
if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Helper: get IP
function getUserIp()
{
    return $_SERVER['HTTP_X_FORWARDED_FOR']
        ?? $_SERVER['REMOTE_ADDR']
        ?? 'unknown';
}

// --------------------
// HANDLE LOGIN
// --------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF check
    if (
        empty($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        $errors[] = 'Invalid request. Refresh and try again.';
    }

    $login    = strtolower(trim($_POST['login'] ?? ''));
    $password = $_POST['password'] ?? '';
    $ip       = getUserIp();

    if ($login === '' || $password === '') {
        $errors[] = 'Login and password are required';
    }

    // Rate limit (5 failures / 10 min)
    if (!$errors) {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM login_attempts
            WHERE ip_address = :ip
              AND success = 0
              AND created_at >= DATE_SUB(NOW(), INTERVAL 10 MINUTE)
        ");
        $stmt->execute([':ip' => $ip]);

        if ($stmt->fetchColumn() >= 5) {
            $errors[] = 'Too many login attempts. Try again later.';
        }
    }

    // Check credentials
    if (!$errors) {
        $stmt = $pdo->prepare("
            SELECT id, username, email, password_hash, display_name, avatar, role, locked_until
            FROM users
            WHERE username = :login OR email = :login
            LIMIT 1
        ");
        $stmt->execute([':login' => $login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Account locked
        if ($user && $user['is_blocked']) {
            $errors[] = 'Your account has been blocked by an administrator.';
        }


        // Wrong credentials
        if (!$user || !password_verify($password, $user['password_hash'])) {

            $errors[] = 'Login or password is wrong';

            // Log failure
            $stmt = $pdo->prepare("
                INSERT INTO login_attempts (user_id, login_input, ip_address, success)
                VALUES (NULL, :login, :ip, 0)
            ");
            $stmt->execute([
                ':login' => $login,
                ':ip' => $ip
            ]);
        }
    }
    $ip = $_SERVER['REMOTE_ADDR'];

    $blocked = $pdo->prepare("
    SELECT 1 FROM blocked_ips WHERE ip_address = :ip
                                                ");
    $blocked->execute([':ip' => $ip]);

    if ($blocked->fetch()) {
        die('Access denied.');
    }

    $ip = $_SERVER['REMOTE_ADDR'];
    $login = trim($_POST['login'] ?? '');

    function geoLocate($ip)
    {
        $json = @file_get_contents("https://ipinfo.io/{$ip}/json");
        if (!$json) return [null, null];
        $data = json_decode($json, true);
        return isset($data['loc']) ? explode(',', $data['loc']) : [null, null];
    }

    [$lat, $lng] = geoLocate($ip);

    $stmt = $pdo->prepare("
    INSERT INTO login_attempts
    (login_input, ip_address, success, latitude, longitude)
    VALUES (:l, :ip, 0, :lat, :lng)
");
    $stmt->execute([
        ':l' => $login,
        ':ip' => $ip,
        ':lat' => $lat,
        ':lng' => $lng
    ]);


    // SUCCESS
    if (!$errors) {

        // Log success
        $stmt = $pdo->prepare("
            INSERT INTO login_attempts (user_id, login_input, ip_address, success)
            VALUES (:uid, :login, :ip, 1)
        ");
        $stmt->execute([
            ':uid' => $user['id'],
            ':login' => $login,
            ':ip' => $ip
        ]);

        // Regenerate session
        session_regenerate_id(true);

        $_SESSION['user_id']     = $user['id'];
        $_SESSION['user_name']   = $user['display_name'] ?: $user['username'];
        $_SESSION['user_avatar'] = $user['avatar'];
        $_SESSION['user_role']   = $user['role'];
        $_SESSION['ip']          = $_SERVER['REMOTE_ADDR'];
        $_SESSION['ua']          = $_SERVER['HTTP_USER_AGENT'];
        $_SESSION['last_activity'] = time();

        unset($_SESSION['csrf_token']);

        header('Location: dashboard.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Login | silent_evidence</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #020617;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .auth-card {
            width: 100%;
            max-width: 430px;
            background-color: #0f172a;
            border-radius: 20px;
            border: 1px solid #1e293b;
            padding: 28px;
            box-shadow: 0 0 100px rgba(246, 0, 0, 0.25);
        }

        .auth-title {
            color: #f60000;
            font-size: 1.6rem;
            font-weight: 700;
            text-align: center;
        }

        .auth-subtitle {
            font-size: 0.9rem;
            color: #94a3b8;
            text-align: center;
            margin-bottom: 18px;
        }

        .label-text {
            font-size: 0.85rem;
            color: #94a3b8;
        }

        .form-control {
            background-color: #1e293b;
            border-color: #334155;
            color: #e2e8f0 !important;
            border-radius: 10px;
            font-size: 0.9rem;
        }

        .form-control:focus {
            background-color: #1e293b;
            border-color: #f60000;
            box-shadow: 0 0 0 5px rgba(246, 0, 0, 0.3);
        }

        .form-control::placeholder {
            color: #64748b;
        }

        .btn-login {
            background-color: #f60000;
            color: #0f172a;
            width: 100%;
            padding: 10px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .btn-login:hover {
            background-color: #ca0000;
        }

        .divider {
            text-align: center;
            color: #64748b;
            margin: 18px 0;
        }

        .divider span {
            padding: 0 12px;
            background-color: #0f172a;
        }

        .divider::before,
        .divider::after {
            content: "";
            display: inline-block;
            width: 30%;
            border-bottom: 1px solid #1f2937;
            vertical-align: middle;
        }

        .google-btn {
            width: 100%;
            background-color: #1e293b;
            border: 1px solid #334155;
            color: #e2e8f0;
            padding: 10px;
            border-radius: 999px;
            font-size: 0.9rem;
        }

        .google-btn:hover {
            background-color: #293548;
        }

        .small-text {
            text-align: center;
            color: #94a3b8;
            margin-top: 12px;
            font-size: 0.85rem;
        }

        .small-text a {
            color: #f60000;
            text-decoration: none;
        }

        .small-text a:hover {
            text-decoration: underline;
        }

        .alert-small {
            font-size: 0.8rem;
            padding: 0.45rem 0.6rem;
            border-radius: 8px;
        }
    </style>
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

                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">


                <button type="submit" class="btn btn-login mt-3">
                    Log in
                </button>

            </form>
            <p class="small-text">
                <a href="forgot_password.php">Forgot your password?</a>
            </p>

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