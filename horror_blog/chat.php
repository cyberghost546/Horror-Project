<?php
session_start();
require '../include/db.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Chat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #020617;
            color: #e5e7eb;
            font-family: system-ui;
        }

        .chat-box {
            max-width: 800px;
            margin: 40px auto;
            background: rgba(2, 6, 23, 0.8);
            border-radius: 18px;
            padding: 20px;
        }

        .messages {
            height: 400px;
            overflow-y: auto;
            margin-bottom: 16px;
        }

        .msg {
            margin-bottom: 10px;
        }

        .msg.you {
            text-align: right;
            color: #a5b4fc;
        }

        input {
            background: #020617;
            border: 1px solid #111827;
            color: #e5e7eb;
        }
    </style>
</head>

<body>

    <div class="chat-box">
        <h4>Chat</h4>

        <div class="messages" id="messages"></div>

        <form id="chatForm">
            <div class="input-group">
                <input type="text" id="message" class="form-control" placeholder="Type message..." autocomplete="off">
                <button class="btn btn-primary">Send</button>
            </div>
        </form>
    </div>

    <script>
        const ws = new WebSocket('ws://localhost:8080')
        const chatBox = document.getElementById('chatMessages')
        const form = document.getElementById('chatForm')
        const input = document.getElementById('chatMessage')

        ws.onmessage = e => {
            const data = JSON.parse(e.data)
            const div = document.createElement('div')
            div.className = 'chat-msg'
            div.innerHTML = `<strong>${data.user}:</strong> ${data.message}`
            chatBox.appendChild(div)
            chatBox.scrollTop = chatBox.scrollHeight
        }

        form.addEventListener('submit', e => {
            e.preventDefault()
            if (!input.value.trim()) return

            fetch('chat_send.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'message=' + encodeURIComponent(input.value)
                })
                .then(res => res.json())
                .then(data => {
                    ws.send(JSON.stringify(data))
                    input.value = ''
                })
        })

        const ctx = document.getElementById('activityChart').getContext('2d')

        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Topics',
                    data: [],
                    backgroundColor: '#7c3aed'
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
                        ticks: {
                            color: '#9ca3af'
                        }
                    },
                    y: {
                        ticks: {
                            color: '#9ca3af'
                        },
                        beginAtZero: true
                    }
                }
            }
        })

        ws.onmessage = e => {
            const data = JSON.parse(e.data)

            if (!data.keywords) return

            chart.data.labels = Object.keys(data.keywords)
            chart.data.datasets[0].data = Object.values(data.keywords)
            chart.update()
        }

        const ws = new WebSocket('ws://localhost:8080');
        const onlineBox = document.getElementById('onlineUsers');

        ws.onopen = () => {
            ws.send(JSON.stringify({
                type: 'join',
                user: '<?= $_SESSION['username'] ?>'
            }))

            ws.send(JSON.stringify({
                type: 'chat'
            }))

        }

        ws.onmessage = e => {
            const data = JSON.parse(e.data)

            if (data.type === 'online') {
                onlineBox.innerHTML = ''
                data.users.forEach(u => {
                    const div = document.createElement('div')
                    div.className = 'online-user'
                    div.textContent = u
                    onlineBox.appendChild(div)
                })
                return
            }

            const div = document.createElement('div')
            div.className = 'chat-msg'
            div.innerHTML = `<strong>${data.user}:</strong> ${data.message}`
            chatBox.appendChild(div)
            chatBox.scrollTop = chatBox.scrollHeight
        }
    </script>
</body>

</html>