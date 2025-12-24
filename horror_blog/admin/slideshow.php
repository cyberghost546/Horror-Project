<?php
session_start();
require '../include/db.php';

if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$success = "";

/* handle form submit */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['slides'])) {

    foreach ($_POST['slides'] as $id => $data) {
        $id = (int)$id;
        $title = trim($data['title'] ?? "");
        $caption = trim($data['caption'] ?? "");
        $order = (int)($data['sort_order'] ?? 0);
        $active = isset($data['is_active']) ? 1 : 0;
        $imagePath = trim($data['current_image'] ?? "");

        if (!empty($_FILES['slides_files']['name'][$id])) {
            $tmp = $_FILES['slides_files']['tmp_name'][$id];
            $name = basename($_FILES['slides_files']['name'][$id]);
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','webp'];

            if (in_array($ext, $allowed)) {
                $dir = 'uploads/slides/';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                $target = $dir . time() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '_', $name);
                if (move_uploaded_file($tmp, $target)) {
                    $imagePath = $target;
                }
            }
        }

        if ($id && $title && $imagePath) {
            $stmt = $pdo->prepare("
                UPDATE carousel_slides
                SET title = :t,
                    caption = :c,
                    image_url = :i,
                    sort_order = :o,
                    is_active = :a
                WHERE id = :id
            ");
            $stmt->execute([
                ':t' => $title,
                ':c' => $caption,
                ':i' => $imagePath,
                ':o' => $order,
                ':a' => $active,
                ':id' => $id
            ]);
        }
    }

    $success = "Slideshow updated successfully";
}

/* fetch slides */
$stmt = $pdo->query("
    SELECT id, title, caption, image_url, sort_order, is_active
    FROM carousel_slides
    ORDER BY sort_order, id
");
$slidesAdmin = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Homepage Slideshow</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
  background: radial-gradient(circle at top, #0b1220, #020617 60%);
  color: #e5e7eb;
  font-family: system-ui, sans-serif;
}

.page-wrap {
  max-width: 1100px;
  margin: 0 auto;
  padding: 40px 24px;
}

.page-header {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 30px;
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
  padding: 24px;
}

.slide-box {
  border: 1px solid #1f2937;
  border-radius: 16px;
  padding: 20px;
  margin-bottom: 20px;
}

.slide-box small {
  color: #9ca3af;
}
</style>
</head>

<body>

<div class="page-wrap">

  <div class="page-header">
    <button class="back-btn" onclick="history.back()">← Back</button>
    <h1 class="m-0">Homepage slideshow</h1>
  </div>

  <p class="text-white mb-4">
    Manage images, text and order of the homepage hero slideshow.
  </p>

  <?php if ($success): ?>
    <div class="alert alert-success py-2 small">
      <?php echo htmlspecialchars($success); ?>
    </div>
  <?php endif; ?>

  <div class="card-dark">

    <form method="post" enctype="multipart/form-data">

      <?php foreach ($slidesAdmin as $slide): ?>
        <div class="slide-box">

          <div class="row g-3">

            <div class="col-md-6">
              <label class="form-label small">Title</label>
              <input type="text"
                     class="form-control form-control-sm"
                     name="slides[<?php echo $slide['id']; ?>][title]"
                     value="<?php echo htmlspecialchars($slide['title']); ?>">
            </div>

            <div class="col-md-6">
              <label class="form-label small">Caption</label>
              <input type="text"
                     class="form-control form-control-sm"
                     name="slides[<?php echo $slide['id']; ?>][caption]"
                     value="<?php echo htmlspecialchars($slide['caption']); ?>">
            </div>

            <div class="col-md-6">
              <label class="form-label small">Replace image</label>
              <input type="file"
                     class="form-control form-control-sm"
                     name="slides_files[<?php echo $slide['id']; ?>]"
                     accept="image/*">
              <input type="hidden"
                     name="slides[<?php echo $slide['id']; ?>][current_image]"
                     value="<?php echo htmlspecialchars($slide['image_url']); ?>">
              <?php if ($slide['image_url']): ?>
                <small>Current: <?php echo htmlspecialchars($slide['image_url']); ?></small>
              <?php endif; ?>
            </div>

            <div class="col-md-3">
              <label class="form-label small">Order</label>
              <input type="number"
                     class="form-control form-control-sm"
                     name="slides[<?php echo $slide['id']; ?>][sort_order]"
                     value="<?php echo (int)$slide['sort_order']; ?>">
            </div>

            <div class="col-md-3 d-flex align-items-end">
              <div class="form-check">
                <input class="form-check-input"
                       type="checkbox"
                       name="slides[<?php echo $slide['id']; ?>][is_active]"
                       <?php if ($slide['is_active']) echo 'checked'; ?>>
                <label class="form-check-label small">Active</label>
              </div>
            </div>

          </div>

        </div>
      <?php endforeach; ?>

      <button type="submit" class="btn btn-outline-light btn-sm">
        Save slideshow
      </button>

    </form>

  </div>

</div>

</body>
</html>
