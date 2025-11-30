<?php
session_start();
require 'include/db.php';

// Only admin
if (empty($_SESSION['user_id']) || empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Handle status change (open <-> closed)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'], $_POST['new_status'])) {
    $id = (int) $_POST['request_id'];
    $newStatus = $_POST['new_status'] === 'closed' ? 'closed' : 'open';

    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE contact_requests SET status = :st WHERE id = :id');
        $stmt->execute([
            ':st' => $newStatus,
            ':id' => $id
        ]);
    }

    header('Location: contact_requests.php?updated=1');
    exit;
}

// Fetch all requests
$stmt = $pdo->query(
    'SELECT cr.*, u.username
     FROM contact_requests cr
     LEFT JOIN users u ON cr.user_id = u.id
     ORDER BY cr.created_at DESC'
);
$requests = $stmt->fetchAll();
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Contact requests | silent_evidence</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'include/header.php'; ?>

    <div class="layout-wrapper">

        <!-- sidebar -->
        <aside class="sidebar">
            <div class="sidebar-title">Company name</div>
            <a href="dashboard.php" class="side-link">
                <span class="icon">🏠</span>
                <span>Dashboard</span>
            </a>
            <a href="stories_list.php" class="side-link">
                <span class="icon">📖</span>
                <span>Stories</span>
            </a>
            <a href="users_list.php" class="side-link">
                <span class="icon">👥</span>
                <span>Users</span>
            </a>
            <a href="contact_requests.php" class="side-link active">
                <span class="icon">📨</span>
                <span>Contact requests</span>
            </a>

            <div class="nav-section-label">Account</div>
            <a href="profile.php" class="side-link">
                <span class="icon">⚙️</span>
                <span>Settings</span>
            </a>
            <a href="logout.php" class="side-link">
                <span class="icon">⏻</span>
                <span>Sign out</span>
            </a>
        </aside>

        <!-- main -->
        <div class="main-area">
            <div class="main-header d-flex flex-wrap justify-content-between gap-2 align-items-center">
                <h1 class="page-title mb-0">Contact requests</h1>
            </div>

            <div class="main-content p-3">

                <?php if (isset($_GET['updated'])): ?>
                    <div class="alert alert-success py-2 small mb-3">
                        Request updated
                    </div>
                <?php endif; ?>

                <div class="card-dark">
                    <div class="card-dark-header d-flex justify-content-between align-items-center">
                        <span>All requests</span>
                    </div>
                    <div class="card-dark-body">
                        <?php if (!$requests): ?>
                            <p class="text-muted small mb-0">No contact requests yet.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-dark table-hover table-sm align-middle table-dark-custom">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Created</th>
                                            <th>Subject</th>
                                            <th>From</th>
                                            <th>Status</th>
                                            <th>Message</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($requests as $row): ?>
                                            <tr>
                                                <td><?php echo (int) $row['id']; ?></td>
                                                <td><?php echo date('d M Y H:i', strtotime($row['created_at'])); ?></td>
                                                <td><?php echo htmlspecialchars($row['subject']); ?></td>
                                                <td>
                                                    <?php if (!empty($row['username'])): ?>
                                                        <div><?php echo htmlspecialchars($row['username']); ?></div>
                                                    <?php endif; ?>
                                                    <div class="text-muted small">
                                                        <?php echo htmlspecialchars($row['name']); ?>
                                                        (<?php echo htmlspecialchars($row['email']); ?>)
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php if ($row['status'] === 'open'): ?>
                                                        <span class="badge bg-warning text-dark">Open</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Closed</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td style="max-width: 260px;">
                                                    <div class="small" style="white-space: pre-wrap;">
                                                        <?php echo nl2br(htmlspecialchars($row['message'])); ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <form method="post" class="d-inline">
                                                        <input type="hidden" name="request_id" value="<?php echo (int) $row['id']; ?>">
                                                        <?php if ($row['status'] === 'open'): ?>
                                                            <input type="hidden" name="new_status" value="closed">
                                                            <button
                                                                type="submit"
                                                                class="btn btn-sm btn-outline-success">
                                                                Mark as closed
                                                            </button>
                                                        <?php else: ?>
                                                            <input type="hidden" name="new_status" value="open">
                                                            <button
                                                                type="submit"
                                                                class="btn btn-sm btn-outline-warning">
                                                                Reopen
                                                            </button>
                                                        <?php endif; ?>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
