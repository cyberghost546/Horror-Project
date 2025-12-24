<?php
session_start();
require '../include/db.php';

if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$totalUsers = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$totalStories = $pdo->query('SELECT COUNT(*) FROM stories')->fetchColumn();
$totalFeatured = $pdo->query('SELECT COUNT(*) FROM stories WHERE is_featured = 1')->fetchColumn();
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Platform Stats</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: radial-gradient(circle at top, #0b1220, #020617 60%);
            color: #e5e7eb;
            font-family: system-ui, sans-serif;
        }

        .page-wrap {
            max-width: 1200px;
            margin: 0 auto;
            padding: 48px 24px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .page-header h1 {
            font-size: 2.2rem;
            font-weight: 600;
        }

        .page-header span {
            font-size: 0.9rem;
            color: #9ca3af;
        }

        .stat-card {
            background: linear-gradient(180deg, #020617, #020617);
            border: 1px solid #111827;
            border-radius: 22px;
            padding: 34px 28px;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at top, rgba(59, 130, 246, 0.15), transparent 60%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #111827;
            margin-bottom: 18px;
            font-size: 1.3rem;
        }

        .stat-value {
            font-size: 3.2rem;
            font-weight: 700;
            color: #f87171;
            line-height: 1;
        }

        .stat-label {
            margin-top: 10px;
            font-size: 1rem;
            font-weight: 500;
        }

        .stat-sub {
            margin-top: 4px;
            font-size: 0.85rem;
            color: #9ca3af;
        }

        .footer-note {
            margin-top: 40px;
            font-size: 0.8rem;
            color: #6b7280;
        }

        .back-btn {
            background-color: #020617;
            border: 1px solid #111827;
            color: #e5e7eb;
            padding: 8px 14px;
            border-radius: 10px;
            font-size: 0.85rem;
            cursor: pointer;
        }

        .back-btn:hover {
            background-color: #111827;
            color: #ffffff;
        }
    </style>
</head>

<body>

    <div class="page-wrap">

        <div class="page-header">
            <button class="back-btn" onclick="history.back()">← Back</button>
            <h1>Platform overview</h1>
        </div>

        <div class="row g-4">

            <div class="col-md-4">
                <a href="users_list.php" class="text-decoration-none text-white">
                    <div class="stat-card">
                        <div class="stat-icon">👥</div>
                        <div class="stat-value"><?php echo (int)$totalUsers; ?></div>
                        <div class="stat-label">Total users</div>
                        <div class="stat-sub">Registered accounts</div>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="users_list.php" class="text-decoration-none text-white">
                    <div class="stat-card">
                        <div class="stat-icon">📚</div>
                        <div class="stat-value"><?php echo (int)$totalStories; ?></div>
                        <div class="stat-label">Total stories</div>
                        <div class="stat-sub">All published content</div>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="users_list.php" class="text-decoration-none text-white">
                    <div class="stat-card">
                        <div class="stat-icon">⭐</div>
                        <div class="stat-value"><?php echo (int)$totalFeatured; ?></div>
                        <div class="stat-label">Featured stories</div>
                        <div class="stat-sub">Shown on homepage</div>
                    </div>
                </a>
            </div>

        </div>

        <div class="footer-note">
            Numbers update automatically as content changes.
        </div>

    </div>

</body>

</html>