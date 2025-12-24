<?php
session_start();
require 'include/db.php';

if (empty($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$success = '';

// handle forms
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // slideshow form (has slides[])
    if (isset($_POST['slides'])) {

        foreach ($_POST['slides'] as $id => $data) {
            $id      = (int)$id;
            $title   = trim($data['title'] ?? '');
            $caption = trim($data['caption'] ?? '');
            $order   = (int)($data['sort_order'] ?? 0);
            $active  = isset($data['is_active']) ? 1 : 0;

            // default to existing image from hidden field
            $imagePath = trim($data['current_image'] ?? '');

            // check if a new file was uploaded for this slide
            if (!empty($_FILES['slides_files']['name'][$id])) {
                $fileTmp  = $_FILES['slides_files']['tmp_name'][$id];
                $fileName = basename($_FILES['slides_files']['name'][$id]);

                // basic extension check
                $ext     = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'webp'];

                if (in_array($ext, $allowed)) {
                    $uploadDir = 'uploads/slides/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $target = $uploadDir . time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $fileName);

                    if (move_uploaded_file($fileTmp, $target)) {
                        $imagePath = $target;
                    }
                }
            }

            if ($id > 0 && $title !== '' && $imagePath !== '') {
                $stmt = $pdo->prepare("
                    UPDATE carousel_slides
                       SET title      = :t,
                           caption    = :c,
                           image_url  = :i,
                           sort_order = :o,
                           is_active  = :a
                     WHERE id = :id
                ");
                $stmt->execute([
                    ':t'  => $title,
                    ':c'  => $caption,
                    ':i'  => $imagePath,
                    ':o'  => $order,
                    ':a'  => $active,
                    ':id' => $id,
                ]);
            }
        }

        $success = 'Carousel updated';
    } else {
        // homepage settings form
        $show_latest   = isset($_POST['show_latest'])   ? 1 : 0;
        $show_popular  = isset($_POST['show_popular'])  ? 1 : 0;
        $show_featured = isset($_POST['show_featured']) ? 1 : 0;

        $stmt = $pdo->prepare(
            'UPDATE homepage_settings
                SET show_latest   = :sl,
                    show_popular  = :sp,
                    show_featured = :sf
              WHERE id = 1'
        );
        $stmt->execute([
            ':sl' => $show_latest,
            ':sp' => $show_popular,
            ':sf' => $show_featured,
        ]);

        $success = 'Homepage settings updated';
    }
}

$range = $_GET['range'] ?? 'week';

switch ($range) {
    case 'today':
        $dateWhere = "created_at >= CURDATE()";
        break;

    case 'month':
        $dateWhere = "created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        break;

    default:
        $dateWhere = "created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)";
        $range = 'week';
}

switch ($range) {
    case 'today':
        $currentWhere = "created_at >= CURDATE()";
        $previousWhere = "created_at >= DATE_SUB(CURDATE(), INTERVAL 1 DAY)
                          AND created_at < CURDATE()";
        break;

    case 'month':
        $currentWhere = "created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        $previousWhere = "created_at >= DATE_SUB(CURDATE(), INTERVAL 60 DAY)
                          AND created_at < DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        break;

    default:
        $currentWhere = "created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)";
        $previousWhere = "created_at >= DATE_SUB(CURDATE(), INTERVAL 13 DAY)
                          AND created_at < DATE_SUB(CURDATE(), INTERVAL 6 DAY)";
}

$currentStories = $pdo->query("
    SELECT COUNT(*) FROM stories WHERE $currentWhere
")->fetchColumn();

$previousStories = $pdo->query("
    SELECT COUNT(*) FROM stories WHERE $previousWhere
")->fetchColumn();

$storyChange = $currentStories - $previousStories;


$activityStmt = $pdo->query("
    SELECT 
        'user' AS type,
        username AS title,
        created_at
    FROM users

    UNION ALL

    SELECT 
        'story' AS type,
        title,
        created_at
    FROM stories

    UNION ALL

    SELECT 
        'featured' AS type,
        title,
        updated_at AS created_at
    FROM stories
    WHERE is_featured = 1

    ORDER BY created_at DESC
    LIMIT 10
");

$activities = $activityStmt->fetchAll(PDO::FETCH_ASSOC);


// fetch homepage settings
$stmt = $pdo->query('SELECT * FROM homepage_settings WHERE id = 1 LIMIT 1');
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

// fallback if table is empty
if (!$settings) {
    $settings = [
        'show_latest'   => 1,
        'show_popular'  => 1,
        'show_featured' => 1,
    ];
}

// fetch slides for dashboard form
$slidesAdminStmt = $pdo->query("
    SELECT id, title, caption, image_url, sort_order, is_active
    FROM carousel_slides
    ORDER BY sort_order, id
");
$slidesAdmin = $slidesAdminStmt->fetchAll();

// quick stats
$totalUsers    = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$totalStories  = $pdo->query('SELECT COUNT(*) FROM stories')->fetchColumn();
$totalFeatured = $pdo->query('SELECT COUNT(*) FROM stories WHERE is_featured = 1')->fetchColumn();

// top 5 popular stories by views
$stmt = $pdo->query('SELECT title, views, likes, created_at FROM stories ORDER BY views DESC LIMIT 5');
$popularStories = $stmt->fetchAll();

$chartStmt = $pdo->query("
    SELECT 
        DATE(created_at) AS day,
        COUNT(*) AS total
    FROM stories
    WHERE $dateWhere
    GROUP BY DATE(created_at)
    ORDER BY DATE(created_at)
");

$chartData = [
    'labels' => [],
    'values' => []
];

while ($row = $chartStmt->fetch(PDO::FETCH_ASSOC)) {
    $chartData['labels'][] = date('d M', strtotime($row['day']));
    $chartData['values'][] = (int)$row['total'];
}



$stmt = $pdo->query("
    SELECT title, views, likes, created_at
    FROM stories
    WHERE $dateWhere
    ORDER BY views DESC
    LIMIT 5
");
$popularStories = $stmt->fetchAll();

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Dashboard Silent Evidence</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            margin: 0;
            background-color: #0b1220;
            color: #e5e7eb;
            font-family: system-ui, sans-serif;
        }

        .wrapper {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 260px;
            background-color: #020617;
            border-right: 1px solid #111827;
            padding: 16px;
        }

        .sidebar h6 {
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        .nav-link {
            color: #9ca3af;
            padding: 10px 12px;
            border-radius: 8px;
            display: flex;
            gap: 10px;
        }

        .nav-link:hover,
        .nav-link.active {
            background-color: #111827;
            color: #fff;
        }

        .table-dark-custom tbody tr:hover {
            background-color: #111827;
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

        .side-link:hover,
        .side-link.active {
            background-color: #111827;
            color: #ffffff;
        }

        .side-link span.icon {
            width: 18px;
            text-align: center;
        }

        .main {
            flex: 1;
        }

        .text-success {
            color: #22c55e !important;
        }

        .text-danger {
            color: #ef4444 !important;
        }

        .header {
            padding: 16px 24px;
            border-bottom: 1px solid #111827;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 1.6rem;
            margin: 0;
        }

        .content {
            padding: 24px;
        }

        .card-dark {
            background-color: #020617;
            border: 1px solid #111827;
            border-radius: 14px;
            padding: 16px;
        }

        .table-dark-custom th,
        .table-dark-custom td {
            border-color: #111827;
            font-size: 0.85rem;
        }

        .btn-outline {
            border: 1px solid #374151;
            color: #e5e7eb;
        }

        .btn-outline:hover {
            background-color: #111827;
            color: #fff;
        }

        .card-dark ul li:last-child {
            border-bottom: none;
        }

        .btn-outline.active {
            background-color: #111827;
            color: #ffffff;
        }
    </style>
</head>

<body>
    <?php include 'include/header.php'; ?>
    <div class="wrapper">


        <aside class="sidebar">
            <h6>Silent Evidence</h6>

            <a class="nav-link active" href="#">
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

            <hr class="text-secondary">

            <a class="nav-link" href="admin/stats.php">Stats</a>
            <a class="nav-link" href="admin/slideshow.php">Slideshow For Homepage</a>
            <a class="nav-link" href="admin/popular_stories.php">Popular Stories</a>
            <a class="nav-link" href="admin/homepage_sections.php">Homepage Sections</a>
            <a class="nav-link" href="admin/security_dashboard.php">Security Dashboard</a>
            <hr class="text-secondary">

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

        <main class="main">

            <div class="header">
                <h1>Dashboard</h1>
                <div class="d-flex gap-2">
                    <a href="?range=today" class="btn btn-outline btn-sm <?php if ($range === 'today') echo 'active'; ?>">
                        Today
                    </a>
                    <a href="?range=week" class="btn btn-outline btn-sm <?php if ($range === 'week') echo 'active'; ?>">
                        This week
                    </a>
                    <a href="?range=month" class="btn btn-outline btn-sm <?php if ($range === 'month') echo 'active'; ?>">
                        This month
                    </a>
                </div>

            </div>

            <div class="content">
                <div class="card-dark mb-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">Stories created</h5>
                        <div class="small text-muted">
                            Compared to previous period
                        </div>
                    </div>

                    <div class="fs-4 fw-bold
        <?php echo $storyChange >= 0 ? 'text-success' : 'text-danger'; ?>">
                        <?php
                        echo $storyChange >= 0
                            ? '+' . $storyChange
                            : $storyChange;
                        ?>
                    </div>
                </div>

                <!-- chart stays -->
                <div class="card-dark mb-4">
                    <canvas id="salesChart" height="90"></canvas>
                </div>

                <!-- real popular stories table -->
                <div class="card-dark">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Top popular stories</h5>
                        <a href="stories_list.php" class="btn btn-outline-silent btn-sm text-white fw-bold">
                            Manage stories
                        </a>
                    </div>

                    <?php if (!$popularStories): ?>
                        <p class="small text-muted mb-0">No stories yet</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-dark table-hover table-sm align-middle table-dark-custom mb-0">
                                <thead>
                                    <tr style="cursor:pointer"
                                        onclick="window.location='stories_list.php?search=<?php echo urlencode($story['title']); ?>'">

                                        <th>Title</th>
                                        <th>Views</th>
                                        <th>Likes</th>
                                        <th>Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($popularStories as $story): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($story['title']); ?></td>
                                            <td><?php echo (int)$story['views']; ?></td>
                                            <td><?php echo (int)$story['likes']; ?></td>
                                            <td><?php echo date('d M Y', strtotime($story['created_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                </div>

                <div class="card-dark mt-4">
                    <h5 class="mb-3">Recent activity</h5>

                    <?php if (!$activities): ?>
                        <p class="small text-muted mb-0">No recent activity</p>
                    <?php else: ?>
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($activities as $item): ?>
                                <li class="d-flex justify-content-between align-items-center py-2 border-bottom"
                                    style="border-color:#111827">

                                    <div>
                                        <?php if ($item['type'] === 'user'): ?>
                                            👤 New user registered
                                        <?php elseif ($item['type'] === 'story'): ?>
                                            📖 Story published
                                        <?php else: ?>
                                            ⭐ Story featured
                                        <?php endif; ?>

                                        <div class="small text-white-50">
                                            <?php echo htmlspecialchars($item['title']); ?>
                                        </div>
                                    </div>

                                    <div class="small text-white-50">
                                        <?php echo date('d M H:i', strtotime($item['created_at'])); ?>
                                    </div>

                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>


            </div>



        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const ctx = document.getElementById('salesChart');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($chartData['labels']); ?>,
                datasets: [{
                    label: 'Stories created',
                    data: <?php echo json_encode($chartData['values']); ?>,
                    borderColor: '#3b82f6',
                    backgroundColor: 'transparent',
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: '#3b82f6'
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: '#111827'
                        },
                        ticks: {
                            color: '#9ca3af'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#111827'
                        },
                        ticks: {
                            color: '#9ca3af'
                        }
                    }
                }
            }
        });
    </script>


</body>

</html>