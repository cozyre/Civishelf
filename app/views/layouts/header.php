<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' — Civishelf' : 'Civishelf' ?></title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php
// Determine active tab based on current URL path
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$currentPath = rtrim($currentPath, '/') ?: '/';

function navTabClass(string $path, string $match): string {
    if ($match === '/') {
        return $path === '/' ? 'secondary' : 'primary-light';
    }
    return str_starts_with($path, $match) ? 'secondary' : 'primary-light';
}
?>

<nav class="navbar primary pb-0">
    <div class="w-100 primary d-flex justify-content-between align-items-center fixed-top px-2 py-0 my-0">
        
        <!-- Logo -->
        <a href="/"><img src="assets/images/logos/logo.png" class="img-fluid" style="max-height: 2.5rem;" alt="Civishelf"></a>

        <!-- Greeting -->
        <div id="user-name">
            <?php if (isset($_SESSION['admin_id'])): ?>
                Hello, <?= htmlspecialchars($_SESSION['admin_name']) ?>
                <span class="badge bg-warning text-dark ms-1" style="font-size:.65rem;">Admin</span>
            <?php elseif (isset($_SESSION['user_id'])): ?>
                Hello, <?= htmlspecialchars($_SESSION['user_name']) ?>
            <?php else: ?>
                Hello, Guest
            <?php endif; ?>
        </div>

        <!-- Profile dropdown -->
        <div class="nav-item dropstart primary">
            <button class="btn primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle" style="font-size: 1.25rem"></i>
            </button>
            <ul class="dropdown-menu">

                <?php if (isset($_SESSION['admin_id'])): ?>
                    <li><a href="/administrator" class="dropdown-item"><i class="bi bi-shield-lock me-2"></i>Admin Panel</a></li>
                    <li><a href="/contact" class="dropdown-item"><i class="bi bi-envelope me-2"></i>Contact Us</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a href="/admin/logout" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>

                <?php elseif (isset($_SESSION['user_id'])): ?>
                    <li><a href="/user/profile" class="dropdown-item"><i class="bi bi-person me-2"></i>My Account</a></li>
                    <li><a href="/mybooks" class="dropdown-item"><i class="bi bi-clock-history me-2"></i>History</a></li>
                    <li><a href="/contact" class="dropdown-item"><i class="bi bi-envelope me-2"></i>Contact Us</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a href="/user/logout" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>

                <?php else: ?>
                    <li>
                        <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#loginModal">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Login / Signup
                        </a>
                    </li>
                    <li><a href="/contact" class="dropdown-item"><i class="bi bi-envelope me-2"></i>Contact Us</a></li>
                <?php endif; ?>

            </ul>
        </div>
    </div>

    <div class="container gap-2 pb-0 mt-5 align-items-end">
        <!-- add a little pull up animation later -->
        <a href="/"
           class="<?= navTabClass($currentPath, '') ?> col border border-bottom-0 rounded-top menu ps-3 text-decoration-none">
            Home
        </a>
        <a href="/books"
           class="<?= navTabClass($currentPath, '/books') ?> col border border-bottom-0 rounded-top menu ps-3 text-decoration-none">
            Explore
        </a>
        <a href="/news"
           class="<?= navTabClass($currentPath, '/news') ?> col border border-bottom-0 rounded-top menu ps-3 text-decoration-none">
            News
        </a>

        <?php if (isset($_SESSION['user_id']) || isset($_SESSION['admin_id'])): ?>
            <a href="/mybooks"
               class="<?= navTabClass($currentPath, '/mybooks') ?> col border border-bottom-0 rounded-top menu ps-3 text-decoration-none">
                My Books
            </a>
        <?php else: ?>
            <!-- Guest: prompt login instead of navigating -->
            <a href="#"
               class="primary-light col border border-bottom-0 rounded-top menu ps-3 text-decoration-none"
               data-bs-toggle="modal" data-bs-target="#loginModal">
                My Books
            </a>
        <?php endif; ?>
    </div>
</nav>

<!-- Flash messages -->
<?php if (isset($_SESSION['flash'])): ?>
    <div class="container mt-3">
        <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type']) ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['flash']['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<!-- ================================================================
     LOGIN MODAL — only rendered for guests
     Failed login sets $_SESSION['login_failed'] in UserController,
     which makes the JS below auto-reopen the modal on redirect back.
================================================================= -->
<?php if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])): ?>
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="loginModalLabel">Login to Civishelf</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <!-- Show inline error inside modal on failed login -->
                <?php if (isset($_SESSION['login_error'])): ?>
                    <div class="alert alert-danger py-2 small">
                        <?= htmlspecialchars($_SESSION['login_error']) ?>
                    </div>
                    <?php unset($_SESSION['login_error']); ?>
                <?php endif; ?>

                <form action="/user/login" method="POST">

                    <div class="mb-3">
                        <label for="loginEmail" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="loginEmail" name="email"
                               value="<?= htmlspecialchars($_SESSION['login_prefill'] ?? '') ?>"
                               autocomplete="email" required>
                        <?php unset($_SESSION['login_prefill']); ?>
                    </div>

                    <div class="mb-3">
                        <label for="loginPassword" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="loginPassword"
                                   name="password" autocomplete="current-password" required>
                            <button class="btn btn-outline-secondary toggle-pw" type="button" tabindex="-1">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary fw-semibold">Login</button>
                    </div>

                    <p class="text-center small mb-0">
                        Don't have an account? <a href="/user/register">Register here</a>
                    </p>

                </form>
            </div>

        </div>
    </div>
</div>
<?php endif; ?>