<?php
// app/views/administrator/index.php
ob_start(); ?>

<!-- ---- Stat Cards ---- -->
<div class="row g-3 mb-4">

    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-icon" style="background:#f0fdf4; color:#16a34a;">
                <i class="bi bi-people"></i>
            </div>
            <div>
                <div class="stat-card-num"><?= number_format((int)$totalUsers) ?></div>
                <div class="stat-card-label">Total Users</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-icon" style="background:#eff6ff; color:#2563eb;">
                <i class="bi bi-journals"></i>
            </div>
            <div>
                <div class="stat-card-num"><?= number_format((int)$totalBooks) ?></div>
                <div class="stat-card-label">Total Books</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-icon" style="background:#fefce8; color:#ca8a04;">
                <i class="bi bi-book-half"></i>
            </div>
            <div>
                <div class="stat-card-num"><?= number_format((int)$activeLoans) ?></div>
                <div class="stat-card-label">Active Loans</div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-card-icon" style="background:#fef2f2; color:#dc2626;">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div>
                <div class="stat-card-num"><?= number_format((int)$overdueCount) ?></div>
                <div class="stat-card-label">Overdue</div>
            </div>
        </div>
    </div>

</div>

<!-- ---- Quick actions + pending alert ---- -->
<?php if ((int)$pendingReqs > 0): ?>
<div class="alert alert-warning d-flex align-items-center gap-2 py-2 mb-4" style="border-radius:8px;">
    <i class="bi bi-bell-fill"></i>
    <span><strong><?= (int)$pendingReqs ?></strong> borrow request<?= $pendingReqs > 1 ? 's' : '' ?> awaiting approval.</span>
    <a href="<?= BASE_URL ?>/administrator/borrows?status=pending" class="btn btn-sm btn-warning ms-auto">Review</a>
</div>
<?php endif; ?>

<!-- ---- Recent Activity ---- -->
<div class="admin-table">
    <div class="px-3 py-2 border-bottom d-flex align-items-center justify-content-between" style="background:#f9fafb;">
        <span style="font-size:0.82rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:#6b7280;">Recent Activity</span>
        <a href="<?= BASE_URL ?>/administrator/borrows" class="btn btn-sm btn-adm-ghost">View All</a>
    </div>
    <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th>User</th>
                <th>Book</th>
                <th>Status</th>
                <th>Date</th>
                <th>Due</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($recentActivity)): ?>
                <?php foreach ($recentActivity as $row): ?>
                <?php
                    $now = time();
                    $due = $row['due_date'] ? strtotime($row['due_date']) : null;
                    $isOverdue = $row['status'] === 'approved' && $due && $due < $now;
                ?>
                <tr>
                    <td>
                        <div style="font-weight:600;"><?= htmlspecialchars($row['user_name']) ?></div>
                        <div style="font-size:0.72rem; color:#6b7280;"><?= htmlspecialchars($row['email']) ?></div>
                    </td>
                    <td><?= htmlspecialchars($row['book_title']) ?></td>
                    <td>
                        <?php if ($isOverdue): ?>
                            <span class="badge-overdue">Overdue</span>
                        <?php elseif ($row['status'] === 'pending'): ?>
                            <span class="badge-pending2">Pending</span>
                        <?php elseif ($row['status'] === 'approved'): ?>
                            <span class="badge-approved">Approved</span>
                        <?php elseif ($row['status'] === 'returned'): ?>
                            <span class="badge-returned">Returned</span>
                        <?php elseif ($row['status'] === 'rejected'): ?>
                            <span class="badge-rejected">Rejected</span>
                        <?php endif; ?>
                    </td>
                    <td style="font-size:0.78rem;"><?= date('d M Y', strtotime($row['borrow_date'])) ?></td>
                    <td style="font-size:0.78rem; color:<?= ($isOverdue ? '#dc2626' : 'inherit') ?>;">
                        <?= $row['due_date'] ? date('d M Y', strtotime($row['due_date'])) : '—' ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" class="text-center py-4" style="color:#9ca3af;">No recent activity.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/admin_layout.php';
?>