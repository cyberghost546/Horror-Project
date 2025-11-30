<?php
session_start();
require 'include/db.php';

$errors  = [];
$success = '';

// Pre fill name and email if user is logged in
$currentUserId = !empty($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
$prefillName   = $_SESSION['user_name']  ?? '';
$prefillEmail  = $_SESSION['user_email'] ?? '';

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Basic validation
    if ($name === '') {
        $errors[] = 'Name is required.';
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email is required.';
    }

    if ($subject === '') {
        $errors[] = 'Subject is required.';
    }

    if ($message === '') {
        $errors[] = 'Message is required.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare(
            'INSERT INTO contact_requests (user_id, name, email, subject, message)
             VALUES (:uid, :n, :e, :s, :m)'
        );
        $stmt->execute([
            ':uid' => $currentUserId ?: null,
            ':n'   => $name,
            ':e'   => $email,
            ':s'   => $subject,
            ':m'   => $message,
        ]);

        $success = 'Your message has been sent. We will get back to you soon.';

        // Clear form after success
        $name    = '';
        $email   = '';
        $subject = '';
        $message = '';
    }
} else {
    // Initial values
    $name    = $prefillName;
    $email   = $prefillEmail;
    $subject = '';
    $message = '';
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Contact | silent_evidence</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php include 'include/header.php'; ?>

    <div class="page-wrapper">
        <div class="container py-4">

            <div class="mb-4">
                <h1 class="page-title">Contact</h1>
                <p class="text-muted mb-1">
                    Having a problem with the site or your account
                </p>
                <p class="text-muted">
                    Fill in this form and the admin will see your request in the dashboard.
                </p>
            </div>

            <?php if ($errors): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $err): ?>
                            <li><?php echo htmlspecialchars($err); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <div class="card-dark">
                <div class="card-dark-header">
                    Contact form
                </div>
                <div class="card-dark-body">
                    <form method="post" action="contact.php" novalidate>
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                value="<?php echo htmlspecialchars($name); ?>"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                value="<?php echo htmlspecialchars($email); ?>"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <input
                                type="text"
                                name="subject"
                                class="form-control"
                                value="<?php echo htmlspecialchars($subject); ?>"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea
                                name="message"
                                rows="5"
                                class="form-control"
                                required><?php echo htmlspecialchars($message); ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-warning">
                            Send message
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
