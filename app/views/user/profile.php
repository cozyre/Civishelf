<?php include __DIR__ . '/../layouts/header.php'; 
$user = $user??[];
$totalBorrows = $totalBorrows??"";
?>

<main class="mb-5 pb-5">

    <div class="container py-5" style="max-width: 560px;">

        <div class="text-center mb-4">
            <div class="profile-avatar mx-auto mb-3">
                <i class="bi bi-person-circle"></i>
            </div>
            <h2 class="profile-name"><?= htmlspecialchars($user['user_name']) ?></h2>
            <p class="profile-email"><?= htmlspecialchars($user['email']) ?></p>

            <?php if ($user['user_status'] === 'banned'): ?>
                <span class="badge bg-danger mt-1">Account Suspended</span>
            <?php endif; ?>
        </div>

        <!-- ── Stats Card ── -->
        <div class="profile-stats-card mb-4">

            <div class="profile-stat-row">
                <span class="profile-stat-label">Joined Since</span>
                <span class="profile-stat-value">
                    <?= date('d/m/Y', strtotime($user['created_at'])) ?>
                </span>
            </div>

            <div class="profile-stat-row">
                <span class="profile-stat-label">Total Books Borrowed</span>
                <span class="profile-stat-value"><?= $totalBorrows ?></span>
            </div>

            <!-- Placeholder — wired up later -->
            <div class="profile-stat-row">
                <span class="profile-stat-label">Total Books Read</span>
                <span class="profile-stat-value profile-stat-placeholder">— Coming soon</span>
            </div>

        </div>

        <!-- ── Actions ── -->
        <div class="d-flex gap-3">
            <!-- Scaffolded — no form yet -->
            <button class="btn profile-btn flex-grow-1" disabled title="Coming soon">
                <i class="bi bi-pencil me-2"></i>Edit Profile
            </button>
            <button class="btn profile-btn flex-grow-1" disabled title="Coming soon">
                <i class="bi bi-key me-2"></i>Change Password
            </button>
        </div>

        <!-- My Books shortcut -->
        <div class="text-center mt-4">
            <a href="<?= BASE_URL ?>/mybooks" class="profile-link">
                <i class="bi bi-bookmark me-1"></i>View My Books
            </a>
        </div>

    </div>

</main>

<?php include __DIR__ . '/../layouts/footer.php'; ?>