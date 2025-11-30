<?php
session_start();
require 'include/db.php';

if (empty($_SESSION['user_id']) || empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$search = trim($_GET['q'] ?? '');
$sort   = $_GET['sort'] ?? 'newest';

$params = [];
$where  = '1=1';

if ($search !== '') {
    $where .= ' AND (s.title LIKE :q OR s.category LIKE :q)';
    $params[':q'] = '%' . $search . '%';
}

$orderBy = 's.created_at DESC';

if ($sort === 'views') {
    $orderBy = 's.views DESC, s.created_at DESC';
} elseif ($sort === 'likes') {
    $orderBy = 's.likes DESC, s.created_at DESC';
} elseif ($sort === 'oldest') {
    $orderBy = 's.created_at ASC';
}

$sql = "
    SELECT
        s.id,
        s.title,
        s.category,
        s.is_published,
        s.is_featured,
        s.views,
        s.likes,
        s.image_path,
        s.created_at,
        u.username,
        u.display_name
    FROM stories s
    JOIN users u ON u.id = s.user_id
    WHERE $where
    ORDER BY $orderBy
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$stories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Stories list | silent_evidence</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css">
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body>
<?php include 'include/header.php'; ?>

<div class="layout-wrapper">

    <aside class="sidebar">
        <div class="sidebar-title">Company name</div>
        <a href="dashboard.php" class="side-link">
            <span class="icon">🏠</span>
            <span>Dashboard</span>
        </a>
        <a href="stories_list.php" class="side-link active">
            <span class="icon">📖</span>
            <span>Stories</span>
        </a>
        <a href="users_list.php" class="side-link">
            <span class="icon">👥</span>
            <span>Users</span>
        </a>
        <a href="contact_requests.php" class="side-link">
            <span class="icon">📨</span>
            <span>Contact requests</span>
        </a>

        <div class="nav-section-label">Account</div>
        <a href="profile.php" class="side-link">
            <span class="icon">⚙️</span>
            <span>Settings</span>
        </a>
        <a href="logout.php" class="side-link">
            <span class="icon">⏻</span>
            <span>Sign out</span>
        </a>
    </aside>

    <div class="main-area">
        <div class="main-header d-flex flex-wrap justify-content-between gap-2 align-items-center">
            <h1 class="page-title mb-0">Stories</h1>

            <form class="d-flex gap-2" method="get" action="stories_list.php">
                <input
                    type="text"
                    name="q"
                    value="<?php echo htmlspecialchars($search); ?>"
                    class="form-control form-control-sm bg-dark border-secondary text-light"
                    placeholder="Search title or category"
                >
                <select
                    name="sort"
                    class="form-select form-select-sm bg-dark border-secondary text-light"
                >
                    <option value="newest"  <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest</option>
                    <option value="oldest"  <?php echo $sort === 'oldest' ? 'selected' : ''; ?>>Oldest</option>
                    <option value="views"   <?php echo $sort === 'views'  ? 'selected' : ''; ?>>Most views</option>
                    <option value="likes"   <?php echo $sort === 'likes'  ? 'selected' : ''; ?>>Most likes</option>
                </select>
                <button class="btn btn-sm btn-outline-light" type="submit">Filter</button>
            </form>
        </div>

        <div class="main-content p-3">
            <div class="card-dark">
                <div class="card-dark-header d-flex justify-content-between align-items-center">
                    <span>All stories</span>
                    <span class="text-secondary small">
                        Total: <?php echo count($stories); ?>
                    </span>
                </div>
                <div class="card-dark-body">
                    <?php if (!$stories): ?>
                        <p class="text-secondary mb-0">No stories found.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-dark table-hover table-sm align-middle table-dark-custom">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Thumbnail</th>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Author</th>
                                        <th>Published</th>
                                        <th>Featured</th>
                                        <th>Views</th>
                                        <th>Likes</th>
                                        <th>Created</th>
                                        <th>Open</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($stories as $story): ?>
                                        <?php
                                        $thumb = !empty($story['image_path'])
                                            ? $story['image_path']
                                            : 'assets/img/default_story.jpg';
                                        ?>
                                        <tr>
                                            <td><?php echo (int)$story['id']; ?></td>
                                            <td>
                                                <img
                                                    src="<?php echo htmlspecialchars($thumb); ?>"
                                                    alt="Story image"
                                                    style="width:60px;height:40px;object-fit:cover;border-radius:0.5rem;border:1px solid #374151;"
                                                >
                                            </td>
                                            <td class="text-truncate" style="max-width:220px;">
                                                <?php echo htmlspecialchars($story['title']); ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <?php echo htmlspecialchars($story['category']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($story['display_name'] ?: $story['username']); ?>
                                            </td>
                                            <td>
                                                <?php if ($story['is_published']): ?>
                                                    <span class="badge bg-success">Yes</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark">No</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($story['is_featured'])): ?>
                                                    <span class="badge bg-info text-dark">Featured</span>
                                                <?php else: ?>
                                                    <span class="badge bg-dark border border-secondary">Normal</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo (int)$story['views']; ?></td>
                                            <td><?php echo (int)$story['likes']; ?></td>
                                            <td><?php echo date('d M Y H:i', strtotime($story['created_at'])); ?></td>
                                            <td>
                                                <a
                                                    href="story.php?id=<?php echo (int)$story['id']; ?>"
                                                    class="btn btn-sm btn-outline-light"
                                                    target="_blank"
                                                >
                                                    View
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
