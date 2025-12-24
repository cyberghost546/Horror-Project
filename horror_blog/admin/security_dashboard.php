<?php
session_start();
require '../include/db.php';

if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

/* ---------------- ACTION HANDLERS ---------------- */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Unblock user
    if (isset($_POST['unblock_user'])) {
        $stmt = $pdo->prepare("UPDATE users SET locked_until = NULL WHERE username = :u");
        $stmt->execute([':u' => $_POST['unblock_user']]);
    }

    // Block IP
    if (isset($_POST['block_ip'])) {
        $stmt = $pdo->prepare("
            INSERT IGNORE INTO blocked_ips (ip_address, reason)
            VALUES (:ip, 'Manual block from security dashboard')
        ");
        $stmt->execute([':ip' => $_POST['block_ip']]);
    }

    // Toggle email alerts
    if (isset($_POST['toggle_alerts'])) {
        $_SESSION['security_alerts'] = !($_SESSION['security_alerts'] ?? false);
    }
}

/* ---------------- DATA QUERIES ---------------- */

/* ---------------- DATA QUERIES ---------------- */

$failedLogins = $pdo->query("
    SELECT login_input, ip_address, COUNT(*) AS attempts, MAX(created_at) AS last_try
    FROM login_attempts
    WHERE success = 0
      AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
    GROUP BY login_input, ip_address
    ORDER BY attempts DESC
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

$successLogins = $pdo->query("
    SELECT u.username, l.ip_address, l.created_at
    FROM login_attempts l
    JOIN users u ON u.id = l.user_id
    WHERE l.success = 1
    ORDER BY l.created_at DESC
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

$lockedUsers = $pdo->query("
    SELECT username, locked_until
    FROM users
    WHERE locked_until IS NOT NULL
      AND locked_until > NOW()
")->fetchAll(PDO::FETCH_ASSOC);

$suspiciousIps = $pdo->query("
    SELECT ip_address, COUNT(*) AS attempts
    FROM login_attempts
    WHERE success = 0
      AND created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
    GROUP BY ip_address
    HAVING attempts >= 3
    ORDER BY attempts DESC
")->fetchAll(PDO::FETCH_ASSOC);

/* ✅ ADD THIS */
$users = $pdo->query("
    SELECT id, username, is_blocked
    FROM users
    ORDER BY username ASC
")->fetchAll(PDO::FETCH_ASSOC);

$geoPoints = $pdo->query("
    SELECT ip_address, latitude, longitude
    FROM login_attempts
    WHERE latitude IS NOT NULL
      AND longitude IS NOT NULL
      AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
")->fetchAll(PDO::FETCH_ASSOC);

/* ---------------- SECURITY SCORE ---------------- */

$score = 100;
$score -= count($suspiciousIps) * 10;
$score -= count($lockedUsers) * 5;
$score -= count($failedLogins) > 5 ? 10 : 0;
$score = max(0, $score);

$scoreColor =
    $score >= 80 ? 'text-success' : ($score >= 50 ? 'text-warning' : 'text-danger');
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Security Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #0b1220;
            color: #e5e7eb;
            font-family: system-ui, sans-serif;
        }

        .page-wrap {
            max-width: 1400px;
            margin: 0 auto;
            padding: 32px 24px;
        }

        .card-dark {
            background-color: #020617;
            border: 1px solid #111827;
            border-radius: 16px;
        }

        .card-header-dark {
            padding: 14px 16px;
            border-bottom: 1px solid #111827;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-body-dark {
            padding: 16px;
        }

        .btn-mini {
            font-size: 0.75rem;
            padding: 4px 8px;
        }
    </style>
</head>

<body>

    <div class="page-wrap">



        <h1 class="mb-4">🛡️ Security Dashboard</h1>

        <!-- SECURITY SCORE -->
        <div class="card-dark mb-4">
            <div class="card-header-dark">
                <strong>Security score</strong>
                <form method="post">
                    <button name="toggle_alerts" class="btn btn-outline-light btn-mini">
                        Alerts: <?php echo ($_SESSION['security_alerts'] ?? false) ? 'ON' : 'OFF'; ?>
                    </button>
                </form>
            </div>
            <div class="card-body-dark">
                <div class="fs-2 fw-bold <?php echo $scoreColor; ?>">
                    <?php echo $score; ?> / 100
                </div>
                <small class="text-muted">
                    Based on failed logins, locked users, and suspicious IPs
                </small>
            </div>
        </div>
        <!-- SECURITY GEO MAP -->
        <div class="col-12">
            <div class="card-dark">
                <div class="card-header-dark">
                    <strong>Security map (last 7 days)</strong>
                </div>
                <div class="card-body-dark p-0">
                    <div id="securityMap" style="height:420px;border-radius:0 0 16px 16px;"></div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row g-4">

            <!-- FAILED LOGINS -->
            <div class="col-lg-6">
                <div class="card-dark h-100">
                    <div class="card-header-dark"><strong>Failed logins</strong></div>
                    <div class="card-body-dark">
                        <table class="table table-dark table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Login</th>
                                    <th>IP</th>
                                    <th>Attempts</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($failedLogins as $r): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($r['login_input']) ?></td>
                                        <td><?= htmlspecialchars($r['ip_address']) ?></td>
                                        <td class="text-danger"><?= $r['attempts'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- SUSPICIOUS IPS -->
            <div class="col-lg-6">
                <div class="card-dark h-100">
                    <div class="card-header-dark"><strong>Suspicious IPs</strong></div>
                    <div class="card-body-dark">
                        <?php foreach ($suspiciousIps as $r): ?>
                            <form method="post" class="d-flex justify-content-between mb-2">
                                <span><?= htmlspecialchars($r['ip_address']) ?> (<?= $r['attempts'] ?>)</span>
                                <button name="block_ip" value="<?= $r['ip_address'] ?>" class="btn btn-danger btn-mini">
                                    Block
                                </button>
                            </form>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- LOCKED USERS -->
            <div class="col-lg-6">
                <div class="card-dark h-100">
                    <div class="card-header-dark"><strong>Locked users</strong></div>
                    <div class="card-body-dark">
                        <?php foreach ($lockedUsers as $u): ?>
                            <form method="post" class="d-flex justify-content-between mb-2">
                                <span><?= htmlspecialchars($u['username']) ?></span>
                                <button name="unblock_user" value="<?= $u['username'] ?>" class="btn btn-success btn-mini">
                                    Unblock
                                </button>
                            </form>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- USER BLOCK/UNBLOCK -->
            <div class="col-lg-6">
                <div class="card-dark h-100">
                    <div class="card-header-dark"><strong>Users</strong></div>
                    <div class="card-body-dark">
                        <?php foreach ($users as $u): ?>
                            <form method="post" class="d-flex justify-content-between mb-2">
                                <span><?= $u['username'] ?></span>
                                <?php if (!$u['is_blocked']): ?>
                                    <button name="block_user" value="<?= $u['id'] ?>" class="btn btn-danger btn-sm">Block</button>
                                <?php else: ?>
                                    <button name="unblock_user" value="<?= $u['id'] ?>" class="btn btn-success btn-sm">Unblock</button>
                                <?php endif; ?>
                            </form>
                        <?php endforeach; ?>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $u): ?>
                                ...
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="small text-muted">No users found</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>




            <!-- SUSPICIOUS IPS WITH BLOCK BUTTONS -->
            <div class="col-lg-6 card-dark">
                <div class="card-body-dark p-3">
                    <h6>Suspicious IPs</h6>

                    <?php if (!empty($suspiciousIps)): ?>
                        <?php foreach ($suspiciousIps as $s): ?>
                            <form method="post" class="d-flex justify-content-between mb-2">
                                <span>
                                    <?= htmlspecialchars($s['ip_address']) ?>
                                    (<?= (int)$s['attempts'] ?>)
                                </span>
                                <button
                                    name="block_ip"
                                    value="<?= htmlspecialchars($s['ip_address']) ?>"
                                    class="btn btn-warning btn-sm">
                                    Block IP
                                </button>
                            </form>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="small text-muted mb-0">No suspicious IPs</p>
                    <?php endif; ?>
                </div>
            </div>



            <!-- SUCCESS LOGINS -->
            <div class="col-lg-6">
                <div class="card-dark h-100">
                    <div class="card-header-dark"><strong>Recent logins</strong></div>
                    <div class="card-body-dark">
                        <table class="table table-dark table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>IP</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($successLogins as $r): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($r['username']) ?></td>
                                        <td><?= htmlspecialchars($r['ip_address']) ?></td>
                                        <td><?= date('d M H:i', strtotime($r['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
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
                    fillOpacity: 0.75
                })
                .addTo(map)
                .bindPopup('IP: ' + p.ip_address);
        });
    </script>

</body>

</html>