<?php
require_once 'include/db.php';

// change these keys to match your own login script 
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $_SESSION['user_name'] ?? 'Anonymous';
$userAvatar = $_SESSION['user_avatar'] ?? 'https://i.pravatar.cc/40';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <header class="top-navbar">
        <nav class="navbar navbar-expand-lg navbar-dark bg-transparent py-3">
            <div class="container-fluid">
                <!-- Brand -->
                <a class="navbar-brand d-flex align-items-center" href="index.php">
                    <span class="brand-icon me-2">Silent Evidence</span>
                    <span class="brand-text">Silent Evidence</span>
                </a>
                <!-- Mobile toggler -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Nav content -->
                <div class="collapse navbar-collapse" id="mainNavbar">
                    <!-- Left links -->
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'stories.php' ? 'active' : '' ?>" href="stories.php">Stories</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'top.php' ? 'active' : '' ?>" href="top.php">Top rated</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'about.php' ? 'active' : '' ?>" href="about.php">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'contact.php' ? 'active' : '' ?>" href="contact.php">Contact</a>
                        </li>
                    </ul>


                    <!-- Search -->
                    <form class="d-flex me-3 mt-2 mt-lg-0" action="search.php" method="get">
                        <input
                            class="form-control search-input" type="search" name="q" placeholder="Search..." aria-label="Search">
                    </form>

                    <!-- Right side: auth or profile -->
                    <?php if (!$isLoggedIn): ?>

                        <!-- Not logged in: show Login / Sign up -->
                        <div class="d-flex gap-2 mt-2 mt-lg-0">
                            <a href="login.php" class="btn btn-outline-light btn-sm rounded-pill">Login</a>
                            <a href="signup.php" class="btn btn-warning btn-sm rounded-pill fw-semibold">Sign up</a>
                        </div>

                    <?php else: ?>

                        <!-- Logged in: show profile picture with dropdown -->
                        <div class="dropdown mt-2 mt-lg-0">
                            <button
                                class="btn btn-profile dropdown-toggle d-flex align-items-center"
                                type="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <img
                                    src="<?php echo htmlspecialchars($userAvatar); ?>"
                                    alt="Profile"
                                    class="profile-img me-1">
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end user-dropdown-menu">
                                <li class="dropdown-header small text-muted px-3">
                                    <div class="fw-semibold"> <?php echo htmlspecialchars($userName); ?> </div>
                                    <div class="text-muted small"> Signed in </div>
                                </li>
                                <li>
                                    <hr class="dropdown-divider my-1">
                                </li>

                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2" href="profile.php">
                                        <span class="user-dropdown-icon">
                                            ☰
                                        </span>
                                        <span>Account</span>
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2" href="my_stories.php">
                                        <span class="user-dropdown-icon">
                                            ⚙
                                        </span>
                                        <span>Settings</span>
                                    </a>
                                </li>

                                <?php if (!empty($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2" href="dashboard.php">
                                            <span class="user-dropdown-icon">
                                                ★
                                            </span>
                                            <span>Admin dashboard</span>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <li>
                                    <hr class="dropdown-divider my-1">
                                </li>

                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 text-danger" href="logout.php">
                                        <span class="user-dropdown-icon user-dropdown-icon-danger">
                                            ⏻
                                        </span>
                                        <span>Log out</span>
                                    </a>
                                </li>
                            </ul>
                        </div>

                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>
</body>

</html>
<style>
    .top-navbar {
        background-color: #111827;
        border-bottom: 1px solid #374151;
    }

    /* brand */
    .brand-icon {
        width: 36px;
        height: 36px;
        border-radius: 999px;
        background-color: #f9fafb;
        color: #111827;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 20px;
    }

    .brand-text {
        font-size: 1rem;
        color: #e5e7eb;
    }

    /* nav links */
    .navbar .nav-link {
        color: #9ca3af;
        font-size: 0.95rem;
    }

    .navbar .nav-link.active,
    .navbar .nav-link:hover {
        color: #ffffff;
    }

    /* search */
    .search-input {
        min-width: 220px;
        border-radius: 999px;
        background-color: #020617;
        border: 1px solid #4b5563;
        color: #e5e7eb;
    }

    .search-input::placeholder {
        color: #6b7280;
    }

    .search-input:focus {
        box-shadow: none;
        border-color: #818cf8;
    }

    /* auth buttons */
    .btn-warning {
        background-color: #fb2424ff;
        border-color: #fb2424ff;
        color: #111827;
    }

    .btn-warning:hover {
        background-color: #f50b0bc4;
        border-color: #f50b0bc4;
        color: #111827;
    }

    /* profile */
    .btn-profile {
        padding: 2px 4px;
        background-color: transparent;
        border: none;
    }

    .btn-profile:focus {
        box-shadow: none;
    }

    .profile-img {
        width: 34px;
        height: 34px;
        border-radius: 999px;
        object-fit: cover;
    }

    /* mobile tweaks */
    @media (max-width: 992px) {
        .search-input {
            min-width: 140px;
            margin-top: 8px;
        }

        .top-navbar .dropdown {
            margin-top: 8px;
        }
    }
</style>