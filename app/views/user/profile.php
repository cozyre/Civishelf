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

<style>
.profile-avatar {
    font-size: 5rem;
    line-height: 1;
    color: var(--primary);
    width: 96px;
    height: 96px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.profile-name {
    font-family: var(--title-font);
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.2rem;
    color: var(--primary);
}

.profile-email {
    color: #6b7280;
    font-size: 1rem;
    margin-bottom: 0;
}

.profile-stats-card {
    background: #fff;
    border: 1px solid rgba(31,31,31,0.1);
    border-radius: 10px;
    overflow: hidden;
}

.profile-stat-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid rgba(31,31,31,0.07);
}

.profile-stat-row:last-child {
    border-bottom: none;
}

.profile-stat-label {
    font-size: 0.88rem;
    color: var(--primary);
    opacity: 0.6;
}

.profile-stat-value {
    font-family: monospace;
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--primary);
}

.profile-stat-placeholder {
    font-weight: 400;
    opacity: 0.35;
    font-style: italic;
}

.profile-btn {
    background: var(--base-theme);
    border: 1px solid rgba(31,31,31,0.15);
    color: var(--primary);
    border-radius: 8px;
    padding: 0.75rem 1rem;
    font-size: 0.88rem;
    font-weight: 600;
}

.profile-btn:disabled {
    opacity: 0.45;
    cursor: not-allowed;
}

.profile-link {
    color: var(--accent);
    font-size: 0.88rem;
    text-decoration: underline;
}

.profile-link:hover {
    color: var(--accent-light);
}
</style>

<?php include __DIR__ . '/../layouts/footer.php'; ?>