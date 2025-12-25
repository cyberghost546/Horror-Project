<?php
session_start();
require 'include/db.php';

if (empty($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

$userId = (int) $_SESSION['user_id'];
$errors = [];
$success = '';

// get user info
$stmt = $pdo->prepare(
  'SELECT id, username, display_name, email, avatar, bio, created_at, last_login
       FROM users
      WHERE id = :id
      LIMIT 1'
);
$stmt->execute([':id' => $userId]);
$user = $stmt->fetch();

if (!$user) {
  die('User not found');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $displayName = trim($_POST['display_name'] ?? '');
  $bio         = trim($_POST['bio'] ?? '');

  if ($displayName === '') {
    $errors[] = 'Display name is required';
  }

  // current avatar
  $avatarPath = $user['avatar'];

  // handle avatar upload
  if (!empty($_FILES['avatar']['name'])) {
    $fileTmp  = $_FILES['avatar']['tmp_name'];
    $fileName = $_FILES['avatar']['name'];
    $fileSize = $_FILES['avatar']['size'];

    $ext     = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($ext, $allowed)) {
      $errors[] = 'Only JPG, PNG, or WebP allowed';
    }

    if ($fileSize > 4 * 1024 * 1024) {
      $errors[] = 'Image must be smaller than 4MB';
    }

    if (!$errors) {
      $newName    = 'avatar_' . $userId . '_' . time() . '.' . $ext;
      $uploadPath = 'uploads/avatars/' . $newName;

      if (!is_dir('uploads/avatars')) {
        mkdir('uploads/avatars', 0777, true);
      }

      move_uploaded_file($fileTmp, $uploadPath);
      $avatarPath = $uploadPath;
    }
  }

  if (!$errors) {
    $stmt = $pdo->prepare(
      'UPDATE users
                SET display_name = :dn,
                    bio = :bio,
                    avatar = :avatar
              WHERE id = :id'
    );

    $stmt->execute([
      ':dn'     => $displayName,
      ':bio'    => $bio,
      ':avatar' => $avatarPath,
      ':id'     => $userId,
    ]);

    // update session + local user array
    $_SESSION['user_name']   = $displayName;
    $_SESSION['user_avatar'] = $avatarPath;

    $user['display_name'] = $displayName;
    $user['bio']          = $bio;
    $user['avatar']       = $avatarPath;

    $success = 'Profile updated';
  }
}

// total stories
$totalStoriesQuery = $pdo->prepare('SELECT COUNT(*) FROM stories WHERE user_id = :id');
$totalStoriesQuery->execute([':id' => $userId]);
$totalStories = (int) $totalStoriesQuery->fetchColumn();

// fetch bookmarks
$bookmarksStmt = $pdo->prepare("
    SELECT s.id, s.title, s.category, s.created_at, s.views
    FROM story_bookmarks b
    JOIN stories s ON s.id = b.story_id
    WHERE b.user_id = :uid
    ORDER BY b.created_at DESC
");
$bookmarksStmt->execute([':uid' => $userId]);
$bookmarks = $bookmarksStmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>My profile | silent_evidence</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet">

  <style>
    body {
      background-color: #020617;
      color: #e5e7eb;
      font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }

    .page-wrapper {
      padding: 24px;
      max-width: 1100px;
      margin: 0 auto;
    }

    .profile-header {
      margin-bottom: 18px;
    }

    .profile-header h1 {
      font-size: 1.9rem;
      font-weight: 700;
      margin: 0;
    }

    .card-dark {
      background-color: #020617;
      border-radius: 16px;
      border: 1px solid #111827;
    }

    .card-dark .card-header {
      border-bottom-color: #111827;
      font-size: 1rem;
      color: #e5e7eb;
    }

    .avatar-lg {
      width: 120px;
      height: 120px;
      border-radius: 999px;
      object-fit: cover;
      border: 2px solid #111827;
    }

    .username-tag {
      font-size: 0.9rem;
      color: #9ca3af;
    }

    .meta-label {
      font-size: 0.85rem;
      color: #9ca3af;
    }

    .meta-value {
      font-size: 0.9rem;
    }

    .stat-number {
      font-size: 1.2rem;
      font-weight: 600;
    }

    .text-muted-small {
      font-size: 0.85rem;
      color: #6b7280;
    }

    .btn-outline-silent {
      border-color: #4b5563;
      color: #e5e7eb;
      font-size: 0.9rem;
      border-radius: 999px;
      padding: 6px 16px;
    }

    .btn-outline-silent:hover {
      background-color: #111827;
      border-color: #6b7280;
      color: #ffffff;
    }

    /* modal form styling */
    .form-control {
      background-color: #111827;
      border-color: #1f2937;
      color: #e5e7eb;
    }

    .form-control:focus {
      border-color: #6366f1;
      box-shadow: none;
    }

    .avatar-preview {
      width: 90px;
      height: 90px;
      border-radius: 999px;
      object-fit: cover;
      border: 2px solid #1f2937;
    }

    .btn-save {
      background-color: #1f2937;
      color: #ffffff;
      border-radius: 999px;
    }

    .btn-save:hover {
      background-color: #111827;
    }

    /* CHAT PANEL */
    .chat-panel {
      background: rgba(2, 6, 23, 0.75);
      backdrop-filter: blur(16px);
      border-radius: 20px;
      box-shadow:
        0 30px 60px rgba(0, 0, 0, 0.45),
        inset 0 1px 0 rgba(255, 255, 255, 0.04);
      padding: 16px;
      display: flex;
      flex-direction: column;
      height: 420px;
    }

    /* HEADER */
    .chat-panel h6,
    .chat-header {
      font-size: 0.9rem;
      font-weight: 600;
      color: #c7d2fe;
      margin-bottom: 12px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    /* STATUS DOT */
    .chat-status {
      font-size: 0.75rem;
      color: #34d399;
    }

    /* MESSAGES CONTAINER */
    .chat-messages {
      flex: 1;
      overflow-y: auto;
      padding-right: 4px;
      font-size: 0.85rem;
    }

    /* SCROLLBAR */
    .chat-messages::-webkit-scrollbar {
      width: 6px;
    }

    .chat-messages::-webkit-scrollbar-thumb {
      background: rgba(99, 102, 241, 0.4);
      border-radius: 10px;
    }

    /* MESSAGE BUBBLES */
    .chat-msg {
      max-width: 85%;
      margin-bottom: 8px;
      padding: 8px 12px;
      border-radius: 14px;
      background: rgba(255, 255, 255, 0.04);
      line-height: 1.4;
      word-wrap: break-word;
    }

    /* USER MESSAGE */
    .chat-msg.you {
      margin-left: auto;
      background: linear-gradient(135deg, #6366f1, #9333ea);
      color: #fff;
    }

    /* USERNAME */
    .chat-msg strong {
      display: block;
      font-size: 0.7rem;
      opacity: 0.8;
      margin-bottom: 2px;
    }

    /* INPUT AREA */
    .chat-input {
      display: flex;
      gap: 8px;
      margin-top: 12px;
    }

    .chat-input input {
      flex: 1;
      background: #020617;
      border: 1px solid rgba(255, 255, 255, 0.08);
      color: #e5e7eb;
      padding: 10px 14px;
      border-radius: 999px;
      font-size: 0.85rem;
    }

    .chat-input input::placeholder {
      color: #64748b;
    }

    /* SEND BUTTON */
    .chat-input button {
      background: linear-gradient(135deg, #6366f1, #9333ea);
      border: none;
      color: #fff;
      padding: 10px 18px;
      border-radius: 999px;
      font-size: 0.85rem;
      cursor: pointer;
      transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .chat-input button:hover {
      transform: translateY(-1px);
      box-shadow: 0 8px 24px rgba(99, 102, 241, 0.45);
    }

    .online-panel {
      margin-top: 12px;
    }

    .online-user {
      font-size: 0.8rem;
      padding: 4px 0;
      color: #34d399;
    }
  </style>
</head>

<body>

  <?php include 'include/header.php'; ?>

  <div class="page-wrapper">

    <div class="profile-header d-flex justify-content-between align-items-center">
      <h1>My profile</h1>
      <button
        class="btn btn-outline-silent"
        type="button"
        data-bs-toggle="modal"
        data-bs-target="#editProfileModal">
        Edit profile
      </button>
    </div>

    <?php if ($success): ?>
      <div class="alert alert-success py-2 mb-3">
        <?php echo htmlspecialchars($success); ?>
      </div>
    <?php endif; ?>

    <?php if ($errors): ?>
      <div class="alert alert-danger py-2 mb-3">
        <?php foreach ($errors as $err) echo '<div>' . htmlspecialchars($err) . '</div>'; ?>
      </div>
    <?php endif; ?>

    <div class="row g-3">
      <!-- left column -->
      <div class="col-md-4">
        <div class="card card-dark h-100">
          <div class="card-body text-center py-4 px-4">
            <img
              src="<?php echo htmlspecialchars($user['avatar']); ?>"
              alt="Avatar"
              class="avatar-lg mb-3">

            <h2 class="h5 mb-1 text-white">
              <?php echo htmlspecialchars($user['display_name'] ?: $user['username']); ?>
            </h2>

            <div class="username-tag mb-3 text-danger">
              @<?php echo htmlspecialchars($user['username']); ?>
            </div>

            <div class="mb-2">
              <div class="meta-label text-white">Email</div>
              <div class="meta-value text-danger">
                <?php echo htmlspecialchars($user['email']); ?>
              </div>
            </div>

            <div class="mb-2">
              <div class="meta-label text-white">Member since</div>
              <div class="meta-value text-danger">
                <?php echo date('d M Y', strtotime($user['created_at'])); ?>
              </div>
            </div>

            <div class="mb-3">
              <div class="meta-label text-white">Last login</div>
              <div class="meta-value text-danger">
                <?php echo $user['last_login']
                  ? date('d M Y H:i', strtotime($user['last_login']))
                  : 'First time online'; ?>
              </div>
            </div>

            <div class="mt-2">
              <div class="meta-label text-white">Total stories</div>
              <div class="stat-number text-danger">
                <?php echo $totalStories; ?>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- right column -->
      <div class="col-md-8">
        <!-- About you -->
        <div class="card card-dark mb-3">
          <div class="card-header bg-transparent">
            About you
          </div>
          <div class="card-body">
            <?php if ($user['bio']): ?>
              <p class="mb-0 text-danger">
                <?php echo nl2br(htmlspecialchars($user['bio'])); ?>
              </p>
            <?php else: ?>
              <p class="text-muted-small mb-0">
                You have not written a bio yet.
              </p>
            <?php endif; ?>
          </div>
        </div>



        <!-- Latest stories -->
        <div class="card card-dark">
          <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
            <span>Your latest stories</span>
            <a href="submit_story.php" class="btn btn-outline-silent btn-sm">
              New story
            </a>
          </div>
          <div class="card-body">
            <?php if ($totalStories === 0): ?>
              <p class="text-muted-small mb-0">
                You have not posted any stories yet.
              </p>
            <?php else: ?>
              <p class="text-muted-small mb-0">
                You have posted <?php echo $totalStories; ?> stories.
                (We can list them here later.)
              </p>
            <?php endif; ?>
          </div>
        </div>

        <div class="card-dark chat-panel">

          <h6>Your Messages</h6>

          <div class="chat-messages" id="profileChat"></div>

          <form id="profileChatForm" class="chat-input">
            <input type="text" id="profileMessage" placeholder="Send message to admin">
            <button type="submit">Send</button>
          </form>
        </div>

        <!-- Bookmarked stories -->
        <div class="card card-dark mt-3">
          <div class="card-header bg-transparent">
            Your bookmarks
          </div>
          <div class="card-body">

            <?php if (!$bookmarks): ?>
              <p class="text-muted-small mb-0">You have no bookmarks yet.</p>

            <?php else: ?>
              <div class="list-group">

                <?php foreach ($bookmarks as $bm): ?>
                  <a href="story.php?id=<?php echo $bm['id']; ?>"
                    class="list-group-item list-group-item-action"
                    style="background:#0f172a;border:1px solid #1e293b;color:#e5e7eb;">

                    <div class="d-flex justify-content-between">
                      <div>
                        <strong><?php echo htmlspecialchars($bm['title']); ?></strong><br>
                        <span class="text-muted-small">
                          <?php echo htmlspecialchars($bm['category']); ?> •
                          <?php echo date('d M Y', strtotime($bm['created_at'])); ?>
                        </span>
                      </div>

                      <span class="text-muted-small">
                        👁 <?php echo (int)$bm['views']; ?>
                      </span>
                    </div>

                  </a>
                <?php endforeach; ?>

              </div>
            <?php endif; ?>

          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Edit profile modal -->
  <div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content card-dark">
        <div class="modal-header border-0">
          <h5 class="modal-title">Edit profile</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form method="post" enctype="multipart/form-data">
            <div class="mb-3 text-center">
              <img
                src="<?php echo htmlspecialchars($user['avatar']); ?>"
                class="avatar-preview mb-2"
                alt="Avatar preview">
              <input type="file" name="avatar" class="form-control mt-2">
            </div>

            <div class="mb-3">
              <label class="form-label">Display name</label>
              <input
                type="text"
                name="display_name"
                class="form-control"
                value="<?php echo htmlspecialchars($user['display_name']); ?>"
                required>
            </div>

            <div class="mb-3">
              <label class="form-label">Bio</label>
              <textarea
                name="bio"
                rows="4"
                class="form-control"><?php echo htmlspecialchars($user['bio']); ?></textarea>
            </div>

            <button type="submit" class="btn btn-save w-100">
              Save changes
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const profileBox = document.getElementById('profileChat')
    const profileForm = document.getElementById('profileChatForm')
    const profileInput = document.getElementById('profileMessage')

    function loadProfileChat() {
      fetch('chat_fetch.php')
        .then(res => res.text())
        .then(html => {
          profileBox.innerHTML = html
          profileBox.scrollTop = profileBox.scrollHeight
        })
    }

    profileForm.addEventListener('submit', e => {
      e.preventDefault()
      if (!profileInput.value.trim()) return

      fetch('chat_send.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'message=' + encodeURIComponent(profileInput.value)
      }).then(() => {
        profileInput.value = ''
        loadProfileChat()
      })
    })

    setInterval(loadProfileChat, 2000)
    loadProfileChat()
  </script>

</body>

</html>