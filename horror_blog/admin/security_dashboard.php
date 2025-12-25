<?php
session_start();
require '../include/db.php';

if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

/* ACTIONS */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['block_ip'])) {
        $stmt = $pdo->prepare("
            INSERT IGNORE INTO blocked_ips (ip_address, reason)
            VALUES (:ip, 'Manual block')
        ");
        $stmt->execute([':ip' => $_POST['block_ip']]);
    }
}

/* DATA */
$failedLogins = $pdo->query("
    SELECT login_input, ip_address, COUNT(*) AS attempts
    FROM login_attempts
    WHERE success = 0
    GROUP BY login_input, ip_address
    ORDER BY attempts DESC
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

$suspiciousIps = $pdo->query("
    SELECT ip_address, COUNT(*) AS attempts
    FROM login_attempts
    WHERE success = 0
    GROUP BY ip_address
    HAVING attempts >= 3
")->fetchAll(PDO::FETCH_ASSOC);

$geoPoints = $pdo->query("
    SELECT ip_address, latitude, longitude
    FROM login_attempts
    WHERE latitude IS NOT NULL
      AND longitude IS NOT NULL
      AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
")->fetchAll(PDO::FETCH_ASSOC);

$recentActivity = $pdo->query("
    SELECT ip_address, success, created_at
    FROM login_attempts
    ORDER BY created_at DESC
    LIMIT 8
")->fetchAll(PDO::FETCH_ASSOC);


/* SAFETY GUARD */
$suspiciousIps = $suspiciousIps ?? [];

/* THREAT LEVEL */
$suspiciousCount = count($suspiciousIps);

$threatLevel = 'Low';
$threatColor = 'success';

if ($suspiciousCount >= 5) {
    $threatLevel = 'Medium';
    $threatColor = 'warning';
}
if ($suspiciousCount >= 10) {
    $threatLevel = 'High';
    $threatColor = 'danger';
}

$totalFailed = $pdo->query("
    SELECT COUNT(*) FROM login_attempts WHERE success = 0
")->fetchColumn();

$totalSuccess = $pdo->query("
    SELECT COUNT(*) FROM login_attempts WHERE success = 1
")->fetchColumn();


?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Security Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <style>
        body {
            margin: 0;
            background: radial-gradient(circle at top, #1e3a8a, #020617 55%);
            color: #e5e7eb;
            font-family: system-ui, sans-serif;
        }

        .app-shell {
            display: flex;
            min-height: 100vh;
        }

        .leaflet-container {
            background: #020617;
            filter: brightness(0.9) contrast(1.15) saturate(0.85);
        }


        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, #020617, #020617 60%, #030712);
            border-right: 1px solid rgba(255, 255, 255, 0.05);
            padding: 24px 16px;
            transition: width 0.25s ease;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }

        .logo {
            font-weight: 700;
            font-size: 1.1rem;
        }

        .toggle-btn {
            background: none;
            border: none;
            color: #c7d2fe;
            font-size: 1.3rem;
        }

        .menu a {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 14px;
            border-radius: 12px;
            color: #c7d2fe;
            text-decoration: none;
            margin-bottom: 6px;
            position: relative;
        }

        .menu a i {
            font-size: 1.2rem;
            min-width: 24px;
            text-align: center;
        }

        .menu a.active {
            background: linear-gradient(135deg, #6366f1, #9333ea);
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.4);
            color: #fff;
        }

        .menu a:hover {
            background: rgba(99, 102, 241, 0.12);
        }

        .sidebar.collapsed .menu a span,
        .sidebar.collapsed .logo {
            display: none;
        }

        .sidebar.collapsed .menu a {
            justify-content: center;
        }

        .sidebar.collapsed .menu a span {
            position: absolute;
            left: 90px;
            background: #020617;
            padding: 6px 10px;
            border-radius: 8px;
            font-size: 0.75rem;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
        }

        .sidebar.collapsed .menu a:hover span {
            opacity: 1;
        }

        .content {
            flex: 1;
            padding: 32px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
        }

        .card-dark {
            background: rgba(2, 6, 23, 0.75);
            backdrop-filter: blur(16px);
            border-radius: 20px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.45),
                inset 0 1px 0 rgba(255, 255, 255, 0.04);
            padding: 20px;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .map-wrap {
            overflow: hidden;
            border-radius: 18px;
        }

        .leaflet-container {
            background: #020617;
            filter: saturate(0.85) contrast(1.05);
        }

        button {
            border-radius: 999px;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        button:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.35);
        }

        .back-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(2, 6, 23, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #c7d2fe;
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: background 0.15s ease, transform 0.15s ease;
        }

        .back-btn:hover {
            background: rgba(99, 102, 241, 0.2);
            transform: translateY(-1px);
        }

        .back-btn i {
            font-size: 1rem;
        }

        .timeline {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .timeline-item {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
        }

        .timeline-item.fail {
            color: #f87171;
        }

        .timeline-item.success {
            color: #34d399;
        }

        .online-panel {
            margin-top: 12px;
        }

        .online-user {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8rem;
            padding: 6px 0;
        }

        .online-user .name {
            flex: 1;
        }

        .badge {
            font-size: 0.65rem;
            padding: 2px 6px;
            border-radius: 999px;
        }

        .badge.admin {
            background: linear-gradient(135deg, #ef4444, #b91c1c);
            color: #fff;
        }

        .badge.user {
            background: rgba(99, 102, 241, 0.25);
            color: #c7d2fe;
        }

        .online-user .seen {
            font-size: 0.65rem;
            color: #9ca3af;
        }
    </style>
</head>

<body>

    <div class="app-shell">

        <aside class="sidebar" id="sidebar">
            <div class="sidebar-top">
                <span class="logo">SMARTNET</span>
                <button class="toggle-btn" onclick="toggleSidebar()">
                    <i class="bi bi-list"></i>
                </button>
            </div>

            <nav class="menu">
                <a class="active"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>
                <a><i class="bi bi-shield-lock"></i><span>Security</span></a>
                <a href="online_overview.php"><i class="bi bi-people"></i><span>Online Overview</span></a>
                <a><i class="bi bi-activity"></i><span>Logs</span></a>
            </nav>
        </aside>

        <main class="content">

            <div class="page-header">
                <a href="../dashboard.php" class="back-btn">
                    <i class="bi bi-arrow-left">
                        Back
                    </i>
                </a>
                <h1>Security Dashboard</h1>
            </div>


            <div class="stat-grid">
                <div class="card-dark">
                    <small>Suspicious IPs</small>
                    <div class="stat-number" data-value="<?= count($suspiciousIps) ?>">0</div>
                </div>
                <div class="card-dark">
                    <small>Failed Logins</small>
                    <div class="stat-number"><?= count($failedLogins) ?></div>
                </div>
                <div class="card-dark">
                    <small>Tracked Locations</small>
                    <div class="stat-number"><?= count($geoPoints) ?></div>
                </div>
            </div>

            <div class="card-dark">
                <small>Threat Level</small>
                <div class="stat-number text-<?= $threatColor ?>">
                    <?= $threatLevel ?>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card-dark online-panel">
                    <h6>Online Users</h6>
                    <div id="onlineUsers"></div>
                </div>
            </div>


            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card-dark">
                        <h6>Security Map</h6>
                        <div class="map-wrap">
                            <div id="securityMap" style="height:420px;"></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card-dark">
                        <h6>Suspicious IPs</h6>
                        <?php foreach ($suspiciousIps as $ip): ?>
                            <form method="post" class="d-flex justify-content-between mb-2">
                                <span><?= htmlspecialchars($ip['ip_address']) ?></span>
                                <button name="block_ip" value="<?= $ip['ip_address'] ?>" class="btn btn-danger btn-sm">
                                    Block
                                </button>
                            </form>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="card-dark mt-4">
                <h6>Recent Activity</h6>

                <div class="timeline">
                    <?php foreach ($recentActivity as $a): ?>
                        <div class="timeline-item <?= $a['success'] ? 'success' : 'fail' ?>">
                            <span><?= htmlspecialchars($a['ip_address']) ?></span>
                            <span><?= date('H:i', strtotime($a['created_at'])) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="card-dark mt-4">
                <h6>Top Attack Sources</h6>

                <?php if (!empty($topAttackers)): ?>
                    <?php foreach ($topAttackers as $a): ?>
                        <div class="d-flex justify-content-between small mb-1">
                            <span><?= htmlspecialchars($a['ip_address']) ?></span>
                            <span class="text-danger"><?= (int)$a['attempts'] ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="small text-muted mb-0">No attack data</p>
                <?php endif; ?>
            </div>




        </main>
    </div>


    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const map = L.map('securityMap').setView([20, 0], 2);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        const points = <?= json_encode($geoPoints) ?>;

        points.forEach(p => {
            if (!p.latitude || !p.longitude) return;
            L.circleMarker([p.latitude, p.longitude], {
                radius: 6,
                color: '#ef4444',
                fillColor: '#ef4444',
                fillOpacity: 0.8
            }).addTo(map).bindPopup('IP: ' + p.ip_address);
        });

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('collapsed');
        }


        document.querySelectorAll('.stat-number').forEach(el => {
            const target = parseInt(el.dataset.value, 10)
            let current = 0
            const step = Math.max(1, Math.floor(target / 40))

            const tick = () => {
                current += step
                if (current >= target) {
                    el.textContent = target
                } else {
                    el.textContent = current
                    requestAnimationFrame(tick)
                }
            }
            tick()
        })
    </script>

</body>

</html>