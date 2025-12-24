<?php
session_start();
require '../include/db.php';

if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->query("
    SELECT title, views, likes, created_at
    FROM stories
    ORDER BY views DESC
    LIMIT 20
");
$popularStories = $stmt->fetchAll(PDO::FETCH_ASSOC); 
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Popular Stories</title>
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
  padding: 40px 24px;
}

.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 28px;
}

.page-header h1 {
  font-size: 2rem;
  font-weight: 600;
  margin: 0;
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
  color: #fff;
}

.card-dark {
  background-color: #020617;
  border: 1px solid #111827;
  border-radius: 18px;
  padding: 20px;
}

.table-dark-custom th {
  color: #9ca3af;
  font-size: 0.8rem;
  text-transform: uppercase;
}

.table-dark-custom td {
  border-color: #111827;
  font-size: 0.85rem;
}

.table-dark-custom tbody tr:hover {
  background-color: #020617;
}

.meta {
  font-size: 0.8rem;
  color: #9ca3af;
}
</style>
</head>

<body>

<div class="page-wrap">

  <div class="page-header">
    <div class="d-flex align-items-center gap-3">
      <button class="back-btn" onclick="history.back()">← Back</button>
      <h1>Popular stories</h1>
    </div>

    <a href="stories_list.php" class="btn btn-outline-light btn-sm">
      Manage stories
    </a>
  </div>

  <p class="meta mb-4">
    Stories ranked by total views. Updated automatically.
  </p>

  <div class="card-dark">

    <?php if (!$popularStories): ?>
      <p class="small text-muted mb-0">No stories found.</p>
    <?php else: ?>

      <div class="table-responsive">
        <table class="table table-dark table-hover table-sm align-middle table-dark-custom mb-0">
          <thead>
            <tr>
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

  

</div>

</body>
</html>
