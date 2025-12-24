<?php
require '../include/db.php';

$points = $pdo->query("
    SELECT ip_address, latitude, longitude
    FROM login_attempts
    WHERE latitude IS NOT NULL
")->fetchAll();
?>
<!doctype html>
<html>

<head>
    <title>Security Geo Map</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <style>
        body {
            margin: 0;
            background: #020617
        }

        #map {
            height: 100vh
        }
    </style>
</head>

<body>

    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const map = L.map('map').setView([20, 0], 2);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        const pts = <?= json_encode($points) ?>;
        pts.forEach(p => {
            if (p.latitude && p.longitude) {
                L.circleMarker([p.latitude, p.longitude], {
                    radius: 6,
                    color: '#ef4444'
                }).addTo(map).bindPopup(p.ip_address);
            }
        });
    </script>
</body>

</html>