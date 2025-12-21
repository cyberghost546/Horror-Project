<?php
session_start();
require 'include/db.php';

if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Determine range
$range = $_GET['range'] ?? 'month';
$startDate = $endDate = date('Y-m-d');

if ($range === 'week') {
    $startDate = date('Y-m-d', strtotime('monday this week'));
    $endDate   = date('Y-m-d', strtotime('sunday this week'));
}

// Stats
$totalUsers = $pdo->prepare("SELECT COUNT(*) FROM users WHERE created_at BETWEEN ? AND ?");
$totalUsers->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
$totalUsers = $totalUsers->fetchColumn();

$totalStories = $pdo->prepare("SELECT COUNT(*) FROM stories WHERE created_at BETWEEN ? AND ?");
$totalStories->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
$totalStories = $totalStories->fetchColumn();

$totalFeatured = $pdo->prepare("SELECT COUNT(*) FROM stories WHERE is_featured = 1 AND created_at BETWEEN ? AND ?");
$totalFeatured->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
$totalFeatured = $totalFeatured->fetchColumn();

// Top popular stories this week
$stmt = $pdo->prepare("SELECT title, views, likes, created_at FROM stories WHERE created_at BETWEEN ? AND ? ORDER BY views DESC LIMIT 5");
$stmt->execute([$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
$popularStories = $stmt->fetchAll();

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Admin Dashboard - Last week</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="include/styles.css" rel="stylesheet">
    <style>
        /* General layout */
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #121212;
            color: #f1f1f1;
        }

        .layout-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
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

        .side-link:hover,
        .side-link.active {
            background-color: #111827;
            color: #ffffff;
        }

        .side-link span.icon {
            width: 18px;
            text-align: center;
        }


        /* Main content area */
        .main-area {
            flex: 1;
            padding: 20px;
        }

        .main-header {
            margin-bottom: 20px;
        }

        .page-title {
            font-size: 24px;
            font-weight: bold;
        }

        /* Cards */
        .card-dark {
            background-color: #1e1e1e;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.4);
        }

        .card-dark-header {
            background-color: #2c2c2c;
            padding: 10px 15px;
            font-weight: bold;
            font-size: 16px;
        }

        .card-dark-body {
            padding: 15px;
        }

        .stat-number {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        /* Grid layout for stats row */
        .row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .col-md-4 {
            flex: 1;
            min-width: 180px;
        }

        /* Table */
        .table-dark-custom {
            width: 100%;
            border-collapse: collapse;
        }

        .table-dark-custom th,
        .table-dark-custom td {
            padding: 8px 12px;
            text-align: left;
        }

        .table-dark-custom thead {
            background-color: #2c2c2c;
        }

        .table-dark-custom tbody tr:nth-child(even) {
            background-color: #1a1a1a;
        }

        .table-dark-custom tbody tr:hover {
            background-color: #333;
        }

        /* Small text */
        .small {
            font-size: 12px;
            color: #aaa;
        }

        /* Text colors */
        .text-danger {
            color: #ff5b5b;
        }

        .text-center {
            text-align: center;
        }

        .fw-bold {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <?php include 'include/header.php'; ?>
    <div class="layout-wrapper">
        <aside class="sidebar">
            <div class="sidebar-title">Company name</div>
            <a href="dashboard.php" class="side-link active">
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
            <a href="contact_messages.php" class="side-link">
                <span class="icon">✉️</span>
                <span>Contact messages</span>
            </a>

            <div class="nav-section-label">Saved views</div>
            <a href="dashboard.php?range=month" class="side-link">
                <span class="icon">🗓️</span>
                <span>Current month</span>
            </a>
            <a href="dashboard_week.php" class="side-link">
                <span class="icon">📈</span>
                <span>Last week</span>
            </a>

            <div style="margin-top:auto">
                <div class="nav-section-label">Account</div>
                <a href="profile.php" class="side-link">
                    <span class="icon">⚙️</span>
                    <span>Settings</span>
                </a>
                <a href="logout.php" class="side-link">
                    <span class="icon">⏻</span>
                    <span>Sign out</span>
                </a>
            </div>
        </aside>
        <div class="main-area">
            <div class="main-header">
                <h1 class="page-title">Dashboard (Last week)</h1>
            </div>
            <div class="main-content">

                <!-- Stats row -->
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="card-dark">
                            <div class="card-dark-header text-center fw-bold">Users</div>
                            <div class="card-dark-body text-center">
                                <div class="stat-number text-danger"><?php echo (int)$totalUsers; ?></div>
                                <div class="small">Registered this week</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card-dark">
                            <div class="card-dark-header text-center fw-bold">Stories</div>
                            <div class="card-dark-body text-center">
                                <div class="stat-number text-danger"><?php echo (int)$totalStories; ?></div>
                                <div class="small">Published this week</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card-dark">
                            <div class="card-dark-header text-center fw-bold">Featured</div>
                            <div class="card-dark-body text-center">
                                <div class="stat-number text-danger"><?php echo (int)$totalFeatured; ?></div>
                                <div class="small">Featured this week</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Popular stories -->
                <div class="card-dark">
                    <div class="card-dark-header">Top popular stories (Last week)</div>
                    <div class="card-dark-body">
                        <?php if (!$popularStories): ?>
                            <p class="small text-muted">No stories this week</p>
                        <?php else: ?>
                            <table class="table table-dark table-hover table-sm align-middle table-dark-custom">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Views</th>
                                        <th>Likes</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($popularStories as $s): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($s['title']); ?></td>
                                            <td><?php echo (int)$s['views']; ?></td>
                                            <td><?php echo (int)$s['likes']; ?></td>
                                            <td><?php echo date('d M Y', strtotime($s['created_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>

</html>