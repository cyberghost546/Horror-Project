<?php
session_start();
require '../include/db.php';

if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$success = "";

/* handle save */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $show_latest   = isset($_POST['show_latest']) ? 1 : 0;
    $show_popular  = isset($_POST['show_popular']) ? 1 : 0;
    $show_featured = isset($_POST['show_featured']) ? 1 : 0;

    $stmt = $pdo->prepare("
        UPDATE homepage_settings
        SET show_latest = :sl,
            show_popular = :sp,
            show_featured = :sf
        WHERE id = 1
    ");
    $stmt->execute([
        ':sl' => $show_latest,
        ':sp' => $show_popular,
        ':sf' => $show_featured
    ]);

    $success = "Homepage settings saved";
}

/* fetch settings */
$stmt = $pdo->query("SELECT * FROM homepage_settings WHERE id = 1 LIMIT 1");
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$settings) {
    $settings = [
        'show_latest' => 1,
        'show_popular' => 1,
        'show_featured' => 1
    ];
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Homepage Sections</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
  background: radial-gradient(circle at top, #0b1220, #020617 60%);
  color: #e5e7eb;
  font-family: system-ui, sans-serif;
}

.page-wrap {
  max-width: 900px;
  margin: 0 auto;
  padding: 40px 24px;
}

.page-header {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 28px;
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
  border-radius: 20px;
  padding: 28px;
}

.card-dark h5 {
  font-size: 1.1rem;
  margin-bottom: 16px;
}

.helper {
  font-size: 0.85rem;
  color: #9ca3af;
}
</style>
</head>

<body>

<div class="page-wrap">

  <div class="page-header">
    <button class="back-btn" onclick="history.back()">← Back</button>
    <h1 class="m-0">Homepage sections</h1>
  </div>

  <?php if ($success): ?>
    <div class="alert alert-success py-2 small">
      <?php echo htmlspecialchars($success); ?>
    </div>
  <?php endif; ?>

  <div class="card-dark">

    <h5>Visible sections</h5>
    <p class="helper mb-4">
      Enable or disable sections on the homepage. Changes apply immediately.
    </p>

    <form method="post">

      <div class="form-check form-switch mb-3">
        <input class="form-check-input"
               type="checkbox"
               id="show_latest"
               name="show_latest"
               <?php if ($settings['show_latest']) echo 'checked'; ?>>
        <label class="form-check-label" for="show_latest">
          Show latest stories
        </label>
      </div>

      <div class="form-check form-switch mb-3">
        <input class="form-check-input"
               type="checkbox"
               id="show_popular"
               name="show_popular"
               <?php if ($settings['show_popular']) echo 'checked'; ?>>
        <label class="form-check-label" for="show_popular">
          Show popular stories
        </label>
      </div>

      <div class="form-check form-switch mb-4">
        <input class="form-check-input"
               type="checkbox"
               id="show_featured"
               name="show_featured"
               <?php if ($settings['show_featured']) echo 'checked'; ?>>
        <label class="form-check-label" for="show_featured">
          Show featured stories
        </label>
      </div>

      <button type="submit" class="btn btn-outline-light btn-sm">
        Save settings
      </button>

    </form>

    <p class="helper mt-4 mb-0">
      Use the is_featured flag on a story to control featured content.
    </p>

  </div>

</div>

</body>
</html>
