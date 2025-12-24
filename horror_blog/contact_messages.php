<?php
session_start();
require 'include/db.php';

if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['delete_id'])) {
        $id = (int)$_POST['delete_id'];
        $pdo->prepare("DELETE FROM contact_messages WHERE id = ?")->execute([$id]);
        header("Location: contact_messages.php");
        exit;
    }

    if (isset($_POST['read_id'])) {
        $id = (int)$_POST['read_id'];
        $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?")->execute([$id]);
        header("Location: contact_messages.php");
        exit;
    }
}


$stmt = $pdo->query("
    SELECT *
    FROM contact_messages
    ORDER BY created_at DESC
");
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

$search = trim($_GET['q'] ?? '');

$sql = "SELECT * FROM contact_messages";
$params = [];

if ($search !== '') {
    $sql .= " WHERE name LIKE :q OR email LIKE :q OR subject LIKE :q OR message LIKE :q";
    $params[':q'] = "%$search%";
}

$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Contact messages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #020617;
            color: #e5e7eb;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        h1 {
            font-size: 1.6rem;
            font-weight: 600;
            color: #ffffff;
        }

        .sidebar {
            width: 250px;
            background-color: #020617;
            border-right: 1px solid #111827;
            padding: 16px 12px;
        }

        .sidebar-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .side-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 10px;
            font-size: 0.9rem;
            color: #9ca3af;
            border-radius: 8px;
            text-decoration: none;
        }

        .side-link.active,
        .side-link:hover {
            background-color: #111827;
            color: #fff;
        }

        .container {
            max-width: 1200px;
        }

        .table {
            background-color: #020617;
            border: 1px solid #111827;
            border-radius: 14px;
            overflow: hidden;
        }

        .table thead {
            background-color: #020617;
        }

        .table thead th {
            color: #9ca3af;
            font-size: 0.8rem;
            text-transform: uppercase;
            border-bottom: 1px solid #111827;
        }

        .table tbody td {
            color: #e5e7eb;
            font-size: 0.85rem;
            border-top: 1px solid #111827;
            vertical-align: top;
        }

        .table-hover tbody tr:hover {
            background-color: #111827;
        }

        td {
            white-space: normal;
            word-break: break-word;
        }

        p.text-secondary {
            color: #9ca3af;
            font-size: 0.9rem;
        }

        .table-responsive {
            border-radius: 14px;
        }

        @media (max-width: 768px) {
            td:nth-child(4) {
                max-width: 200px;
            }
        }
    </style>

</head>

<body>

    <?php include 'include/header.php'; ?>

    <div style="display:flex;min-height:100vh;">

        <!-- sidebar -->
        <aside class="sidebar">
            <div class="sidebar-title">Silent Evidence</div>

            <a href="dashboard.php" class="side-link"><span>🏠</span>Dashboard</a>
            <a href="stories_list.php" class="side-link"><span>📖</span>Stories</a>
            <a href="users_list.php" class="side-link"><span>👥</span>Users</a>
            <a href="contact_messages.php" class="side-link active"><span>✉️</span>Contact Messages</a>

            <div style="margin-top:auto">
                <a href="logout.php" class="side-link"><span>⏻</span>Sign out</a>
            </div>
        </aside>

        <!-- main content -->
        <main style="flex:1;padding:24px;">

            <form method="get" class="mb-3" style="max-width:400px;">
                <input
                    type="text"
                    name="q"
                    value="<?php echo htmlspecialchars($search); ?>"
                    class="form-control form-control-sm"
                    placeholder="Search name, email, subject or message">
            </form>


            <h1 class="mb-3">Contact messages</h1>

            <?php if (!$messages): ?>
                <p class="text-secondary">No messages yet.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>

                        <tbody>
                            <?php foreach ($messages as $msg): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($msg['name']); ?></td>
                                    <td><?php echo htmlspecialchars($msg['email']); ?></td>
                                    <td><?php echo htmlspecialchars($msg['subject']); ?></td>
                                    <td style="max-width:320px;">
                                        <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                                    </td>
                                    <td>
                                        <?php echo $msg['is_read'] ? 'Read' : 'New'; ?>
                                    </td>
                                    <td>
                                        <?php echo date('d M Y H:i', strtotime($msg['created_at'])); ?>
                                    </td>
                                    <td>
                                        <form method="post" style="display:flex;gap:6px;">
                                            <?php if (!$msg['is_read']): ?>
                                                <button name="read_id" value="<?php echo $msg['id']; ?>" class="btn btn-sm btn-outline-success">
                                                    Mark read
                                                </button>
                                            <?php endif; ?>

                                            <button name="delete_id" value="<?php echo $msg['id']; ?>" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Delete this message?')">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        </main>

    </div>

</body>


</html>