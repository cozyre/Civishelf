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
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">

    <script>
        const BASE_URL = "<?= BASE_URL ?>";
        // Exposes login state to main.js without leaking session data
        const CIVISHELF_USER = <?= (isset($_SESSION['user_id']) || isset($_SESSION['admin_id'])) ? 1 : 0 ?>;
    </script>
</head>
<body>

<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$currentPath = rtrim($currentPath, '/') ?: '/';

function navTabClass(string $currentPath, string $path): string {
    return $path === $currentPath ? 'secondary' : 'primary-light';
}
?>

<nav class="navbar primary pb-0 mb-1">
    <div class="w-100 primary d-flex justify-content-between align-items-center fixed-top px-2 py-0 my-0">
        
        <a href="."><img src="<?= BASE_URL ?>/assets/images/logos/logo.png" class="img-fluid" style="max-height: 2.5rem;" alt="Civishelf"></a>

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

        <div class="nav-item dropstart primary">
            <button class="btn primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle" style="font-size: 1.25rem"></i>
            </button>
            <ul class="dropdown-menu">
                <?php if (isset($_SESSION['admin_id'])): ?>
                    <li><a href="<?= BASE_URL ?>/user/profile" class="dropdown-item"><i class="bi bi-person me-2"></i>My Account</a></li>
                    <li><a href="<?= BASE_URL ?>/mybooks" class="dropdown-item"><i class="bi bi-clock-history me-2"></i>History</a></li>
                    <li><a href="<?= BASE_URL ?>/administrator" class="dropdown-item"><i class="bi bi-shield-lock me-2"></i>Admin Panel</a></li>
                    <li><a href="<?= BASE_URL ?>/contact" class="dropdown-item"><i class="bi bi-envelope me-2"></i>Contact Us</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a href="<?= BASE_URL ?>/admin/logout" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                <?php elseif (isset($_SESSION['user_id'])): ?>
                    <li><a href="<?= BASE_URL ?>/user/profile" class="dropdown-item"><i class="bi bi-person me-2"></i>My Account</a></li>
                    <li><a href="<?= BASE_URL ?>/mybooks" class="dropdown-item"><i class="bi bi-clock-history me-2"></i>History</a></li>
                    <li><a href="<?= BASE_URL ?>/contact" class="dropdown-item"><i class="bi bi-envelope me-2"></i>Contact Us</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a href="<?= BASE_URL ?>/administrator" class="dropdown-item"><i class="bi bi-shield-lock me-2"></i>Admin Login</a></li>
                    <li><a href="<?= BASE_URL ?>/user/logout" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                <?php else: ?>
                    <li>
                        <a href="<?= BASE_URL ?>/" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#loginModal">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Login / Signup
                        </a>
                    </li>
                    <li><a href="<?= BASE_URL ?>/contact" class="dropdown-item"><i class="bi bi-envelope me-2"></i>Contact Us</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="container gap-2 pb-0 mt-5 align-items-end">
        <a href="<?= BASE_URL ?>/"
           class="<?= navTabClass($currentPath, '/Civishelf') ?> col border border-bottom-0 rounded-top menu ps-3 text-decoration-none">
            Home
        </a>
        <a href="<?= BASE_URL ?>/books"
           class="<?= navTabClass($currentPath, '/Civishelf/books') ?> col border border-bottom-0 rounded-top menu ps-3 text-decoration-none">
            Explore
        </a>
        <a href="<?= BASE_URL ?>/news"
           class="<?= navTabClass($currentPath, '/Civishelf/news') ?> col border border-bottom-0 rounded-top menu ps-3 text-decoration-none">
            News
        </a>
        <?php if (isset($_SESSION['user_id']) || isset($_SESSION['admin_id'])): ?>
            <a href="<?= BASE_URL ?>/mybooks"
               class="<?= navTabClass($currentPath, '/Civishelf/mybooks') ?> col border border-bottom-0 rounded-top menu ps-3 text-decoration-none">
                My Books
            </a>
        <?php else: ?>
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
     LOGIN MODAL — guests only
================================================================= -->
<?php if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_id'])): ?>
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content primary">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="loginModalLabel">Login to Civishelf</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body primary-light">
                <?php if (isset($_SESSION['login_error'])): ?>
                    <div class="alert alert-danger py-2 small">
                        <?= htmlspecialchars($_SESSION['login_error']) ?>
                    </div>
                    <?php unset($_SESSION['login_error']); ?>
                <?php endif; ?>
                <form action="<?= BASE_URL ?>/user/login" method="POST">
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
                        <button type="submit" class="btn accent fw-semibold">Login</button>
                    </div>
                    <p class="text-center small mb-0">
                        Don't have an account? <a href="<?= BASE_URL ?>/user/register"><b>Register here</b></a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>


<!-- ================================================================
     BOOK MODAL — available on Home, Explore, and My Books pages.
     Populated dynamically by main.js from data-* attributes.
================================================================= -->
<div class="modal fade" id="bookModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content book-modal-content p-0 overflow-hidden">

            <div class="row g-0" style="min-height: 300px;">

                <div class="col-5 book-modal-cover-wrap">
                    <img src="" alt="" id="modalCover" class="book-modal-cover">
                </div>

                <div class="col-7 book-modal-details d-flex flex-column p-3">

                    <button type="button"
                            class="btn-close align-self-end mb-2"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>

                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <h5 class="book-modal-title mb-0">
                            <span id="modalTitle"></span>
                            <span class="mx-1 opacity-50">|</span>
                            <span id="modalAuthor" class="fw-normal"></span>
                        </h5>
                        <span id="modalCategory" class="book-modal-category small ms-2 flex-shrink-0"></span>
                    </div>

                    <p class="small opacity-50 mb-1">Published: <span id="modalPublished"></span></p>
                    <p id="modalDescription" class="small book-modal-desc"></p>

                    <p class="small mb-1 mt-auto">Available copies: <span id="modalCopies"></span></p>

                    <div class="mb-3">
                        <span class="fw-semibold">Status:</span><br>
                        <span id="modalStatus" class="book-modal-status"></span>
                    </div>

                    <!-- ACTIONS -->
                    <div class="d-flex gap-2 align-items-center">
                        <button class="btn book-modal-btn flex-grow-1" id="modalPreviewBtn">Preview</button>
                        <button class="btn book-modal-btn flex-grow-1" id="modalActionBtn">Borrow</button>
                        <button class="btn book-modal-save" id="modalSaveBtn" title="Save book">
                            <i class="bi bi-bookmark-plus" id="modalSaveIcon"></i>
                        </button>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>