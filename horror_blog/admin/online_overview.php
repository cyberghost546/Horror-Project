<?php
session_start();

if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Live Activity Overview</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: radial-gradient(circle at top, #1e3a8a, #020617 55%);
            color: #e5e7eb;
            font-family: system-ui, sans-serif;
        }

        .wrapper {
            max-width: 900px;
            margin: 60px auto;
        }

        .card-dark {
            background: rgba(2, 6, 23, 0.75);
            backdrop-filter: blur(16px);
            border-radius: 20px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.45);
            padding: 24px;
            text-align: center;
        }

        .stat {
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(135deg, #60a5fa, #a855f7);
            -webkit-background-clip: text;
            color: transparent;
        }

        .label {
            font-size: 0.9rem;
            color: #9ca3af;
        }

        .dash-card {
            height: 100%;
            background: #020617;
            border: 1px solid #1f2937;
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.55);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .dash-main {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .dash-value {
            font-size: 3rem;
            font-weight: 700;
            color: #e5e7eb;
            line-height: 1;
        }

        .dash-value.accent {
            color: #a855f7;
        }

        .dash-label {
            margin-top: 6px;
            font-size: 0.8rem;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .dash-trend {
            font-size: 0.85rem;
            font-weight: 600;
            color: #22c55e;
        }

        .dash-meta {
            margin-top: 22px;
            padding-top: 18px;
            border-top: 1px solid #1f2937;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .meta-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: #e5e7eb;
        }

        .meta-label {
            font-size: 0.75rem;
            color: #9ca3af;
        }
    </style>
</head>

<body>

    <div class="wrapper">

        <h2 class="mb-4 text-center">Live Security Activity</h2>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card-dark">
                    <div class="stat" id="onlineCount">0</div>
                    <div class="label">Users Online</div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="dash-card">
                        <div class="dash-main">
                            <div class="dash-value" id="onlineCount">0</div>
                            <div class="dash-label">Users online</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="dash-card">
                        <div class="dash-main">
                            <div>
                                <div class="dash-value accent" id="chattingCount">0</div>
                                <div class="dash-label">Users chatting</div>
                            </div>
                            <div class="dash-trend" id="chatTrend">+0%</div>
                        </div>

                        <div class="dash-meta">
                            <div>
                                <div class="meta-value" id="adminOnline">0</div>
                                <div class="meta-label">Admins online</div>
                            </div>
                            <div>
                                <div class="meta-value" id="lastActivity">--</div>
                                <div class="meta-label">Last message</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-md-12 mt-4">
                <div class="card-dark">
                    <div class="stat" id="peakCount">0</div>
                    <div class="label">Peak Users Today</div>
                </div>
            </div>

            <div class="card-dark mt-4">
                <h6>Chat Activity (Last 60 Minutes)</h6>
                <canvas id="activityChart" height="120"></canvas>
            </div>


        </div>

    </div>

    <script>
        const ws = new WebSocket('ws://localhost:8080')

        const onlineEl = document.getElementById('onlineCount')
        const chattingEl = document.getElementById('chattingCount')

        ws.onopen = () => {
            ws.send(JSON.stringify({
                type: 'join',
                username: '<?= $_SESSION['username'] ?>',
                role: 'admin'
            }))
        }

        ws.onmessage = e => {
            const data = JSON.parse(e.data)

            if (data.type === 'online') {
                onlineEl.textContent = data.users.length

                const chatting = data.users.filter(u => u.chatting)
                chattingEl.textContent = chatting.length
            }
        }
        if (data.type === 'online') {
            onlineEl.textContent = data.users.length
            chattingEl.textContent = data.users.filter(u => u.chatting).length
            document.getElementById('peakCount').textContent = data.peak
        }
    </script>

</body>

</html>