<?php
session_start();
require 'include/db.php';

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || $email === '' || $message === '') {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Enter a valid email address.';
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO contact_messages (name, email, message, created_at)
            VALUES (:name, :email, :message, NOW())
        ");
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':message' => $message
        ]);
        $success = true;
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Contact | silent_evidence</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #06080f;
            color: #e5e7eb;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
        }

        h1 {
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        p {
            line-height: 1.6;
        }

        form {
            background-color: #0b0f19;
            border-radius: 18px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
        }

        label {
            font-size: 0.9rem;
            color: #cbd5e1;
        }

        input,
        textarea {
            background-color: #020617;
            color: #e5e7eb;
            border: 1px solid #1f2937;
            border-radius: 10px;
            padding: 12px 14px;
        }

        input:focus,
        textarea:focus {
            outline: none;
            border-color: #dc2626;
            box-shadow: 0 0 0 2px rgba(220, 38, 38, 0.25);
            background-color: #020617;
            color: #e5e7eb;
        }

        textarea {
            resize: vertical;
        }

        .btn-silent-primary {
            background-color: #dc2626;
            border: none;
            border-radius: 999px;
            color: #fff;
            font-weight: 600;
            padding: 10px 24px;
            transition: background-color 0.2s ease, transform 0.1s ease;
        }

        .btn-silent-primary:hover {
            background-color: #b91c1c;
            transform: translateY(-1px);
        }

        .alert {
            border-radius: 12px;
            font-size: 0.9rem;
        }

        .alert-success {
            background-color: #052e16;
            color: #bbf7d0;
            border: 1px solid #166534;
        }

        .alert-danger {
            background-color: #3f0d0d;
            color: #fecaca;
            border: 1px solid #7f1d1d;
        }
    </style>

</head>

<body>
    <?php include 'include/header.php'; ?>

    <main class="container py-5" style="max-width:720px;">

        <h1 class="text-light mb-3">Contact Silent Evidence</h1>
        <p class="text-secondary mb-4">
            Got feedback, questions, or something important to report.
            Drop us a message. We read everything.
        </p>

        <?php if ($success): ?>
            <div class="alert alert-success">
                Your message was sent. We will get back to you.
            </div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="post" class="bg-dark p-4 rounded-4 border border-secondary">

            <div class="mb-3">
                <label class="form-label text-light">Your name</label>
                <input
                    type="text"
                    name="name"
                    class="form-control bg-black text-light border-secondary"
                    required>
            </div>

            <div class="mb-3">
                <label class="form-label text-light">Email address</label>
                <input
                    type="email"
                    name="email"
                    class="form-control bg-black text-light border-secondary"
                    required>
            </div>

            <div class="mb-4">
                <label class="form-label text-light">Message</label>
                <textarea
                    name="message"
                    rows="5"
                    class="form-control bg-black text-light border-secondary"
                    required></textarea>
            </div>

            <button type="submit" class="btn btn-silent-primary px-4">
                Send message
            </button>

        </form>

    </main>

    <?php include 'include/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>