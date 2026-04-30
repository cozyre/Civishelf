<?php
// app/views/administrator/users.php
$page = $page??1;
$totalUsers = $totalUsers??0;
$limit = $limit??0;
ob_start(); ?>

<!-- ---- Toolbar ---- -->
<div class="section-bar">
    <h1 class="section-bar-title flex-grow-1">Users</h1>
    <form method="GET" action="<?= BASE_URL ?>/administrator/users" class="d-flex gap-2">
        <input type="text" name="search" class="form-control form-control-sm"
               placeholder="Search name or email…" style="width:220px;"
               value="<?= htmlspecialchars($search ?? '') ?>">
        <button type="submit" class="btn btn-sm btn-adm-primary">Search</button>
        <?php if (!empty($search)): ?>
            <a href="<?= BASE_URL ?>/administrator/users" class="btn btn-sm btn-adm-ghost">Clear</a>
        <?php endif; ?>
    </form>
</div>

<!-- ---- Summary ---- -->
<p class="small text-muted mb-2">
    <?= number_format((int)$totalUsers) ?> user<?= $totalUsers !== 1 ? 's' : '' ?> found
    <?= !empty($search) ? 'for "' . htmlspecialchars($search) . '"' : '' ?>
</p>

<!-- ---- Table ---- -->
<div class="admin-table mb-3">
    <table class="table mb-0">
        <thead>
            <tr>
                <th>#</th>
                <th>Name / Email</th>
                <th>Status</th>
                <th>Borrows</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $u): ?>
                <tr id="user-row-<?= $u['user_id'] ?>">
                    <td style="color:#9ca3af; font-size:0.75rem;"><?= (int)$u['user_id'] ?></td>
                    <td>
                        <div style="font-weight:600;"><?= htmlspecialchars($u['user_name']) ?></div>
                        <div style="font-size:0.72rem; color:#6b7280;"><?= htmlspecialchars($u['email']) ?></div>
                    </td>
                    <td>
                        <?php if ($u['user_status'] === 'active'): ?>
                            <span class="badge-active">Active</span>
                        <?php else: ?>
                            <span class="badge-banned">Banned</span>
                        <?php endif; ?>
                    </td>
                    <td style="font-size:0.82rem;">
                        <span title="Total borrows"><?= (int)($u['total_borrows'] ?? 0) ?> total</span>
                        <?php if ((int)($u['active_borrows'] ?? 0) > 0): ?>
                            <span class="badge-approved ms-1"><?= (int)$u['active_borrows'] ?> active</span>
                        <?php endif; ?>
                    </td>
                    <td style="font-size:0.78rem;"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                    <td>
                        <div class="d-flex gap-1 flex-wrap">
                            <!-- Borrow History -->
                            <button class="btn btn-sm btn-adm-ghost btn-history"
                                    data-user-id="<?= $u['user_id'] ?>"
                                    data-user-name="<?= htmlspecialchars($u['user_name']) ?>"
                                    title="View borrow history">
                                <i class="bi bi-clock-history"></i>
                            </button>

                            <!-- Ban / Unban -->
                            <?php if ($u['user_status'] === 'active'): ?>
                                <button class="btn btn-sm btn-adm-ghost btn-status"
                                        data-user-id="<?= $u['user_id'] ?>"
                                        data-new-status="banned"
                                        title="Ban user" style="color:#C30D00; border-color:#fca5a5;">
                                    <i class="bi bi-slash-circle"></i>
                                </button>
                            <?php else: ?>
                                <button class="btn btn-sm btn-adm-ghost btn-status"
                                        data-user-id="<?= $u['user_id'] ?>"
                                        data-new-status="active"
                                        title="Unban user" style="color:#16a34a; border-color:#86efac;">
                                    <i class="bi bi-check-circle"></i>
                                </button>
                            <?php endif; ?>

                            <!-- Delete -->
                            <button class="btn btn-sm btn-adm-danger btn-delete-user"
                                    data-user-id="<?= $u['user_id'] ?>"
                                    data-user-name="<?= htmlspecialchars($u['user_name']) ?>"
                                    title="Delete user">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center py-4" style="color:#9ca3af;">No users found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- ---- Pagination ---- -->
<?php
$totalPages = (int)ceil($totalUsers / $limit);
if ($totalPages > 1):
?>
<nav>
    <ul class="pagination pagination-sm justify-content-center">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
            <a class="page-link" href="<?= BASE_URL ?>/administrator/users?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">
                <?= $i ?>
            </a>
        </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<!-- ================================================================
     BORROW HISTORY MODAL
================================================================= -->
<div class="modal fade" id="historyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title mb-0" id="historyModalLabel">Borrow History</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0" id="historyModalBody">
                <div class="text-center py-4 text-muted">Loading…</div>
            </div>
        </div>
    </div>
</div>

<!-- ================================================================
     JS
================================================================= -->
<script>
(function () {

    // ---- Ban / Unban ----
    document.querySelectorAll('.btn-status').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var userId    = this.dataset.userId;
            var newStatus = this.dataset.newStatus;
            var label     = newStatus === 'banned' ? 'ban' : 'unban';
            if (!confirm('Are you sure you want to ' + label + ' this user?')) return;

            $.post(BASE_URL + '/administrator/userStatus', { user_id: userId, status: newStatus }, function (res) {
                if (res.success) location.reload();
                else alert(res.message || 'Action failed.');
            }, 'json').fail(function () { alert('Network error.'); });
        });
    });

    // ---- Delete ----
    document.querySelectorAll('.btn-delete-user').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var userId = this.dataset.userId;
            var name   = this.dataset.userName;
            if (!confirm('Permanently delete "' + name + '"? This cannot be undone.')) return;

            $.post(BASE_URL + '/administrator/userDelete', { user_id: userId }, function (res) {
                if (res.success) {
                    var row = document.getElementById('user-row-' + userId);
                    if (row) { row.style.opacity = 0; setTimeout(function () { row.remove(); }, 300); }
                } else alert(res.message || 'Delete failed.');
            }, 'json').fail(function () { alert('Network error.'); });
        });
    });

    // ---- History ----
    document.querySelectorAll('.btn-history').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var userId = this.dataset.userId;
            var name   = this.dataset.userName;
            document.getElementById('historyModalLabel').textContent = name + ' — Borrow History';
            document.getElementById('historyModalBody').innerHTML     = '<div class="text-center py-4 text-muted">Loading…</div>';

            var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('historyModal'));
            modal.show();

            $.get(BASE_URL + '/administrator/userHistory', { user_id: userId }, function (res) {
                if (!res.success) { document.getElementById('historyModalBody').innerHTML = '<div class="p-3 text-danger">Failed to load.</div>'; return; }

                if (!res.history.length) {
                    document.getElementById('historyModalBody').innerHTML = '<div class="p-4 text-center text-muted">No borrow history.</div>';
                    return;
                }

                var rows = res.history.map(function (h) {
                    var badge = {
                        pending:  '<span class="badge-pending2">Pending</span>',
                        approved: '<span class="badge-approved">Approved</span>',
                        rejected: '<span class="badge-rejected">Rejected</span>',
                        returned: '<span class="badge-returned">Returned</span>',
                    }[h.status] || h.status;

                    return '<tr>' +
                        '<td><img src="' + BASE_URL + '/assets/images/covers/' + (h.cover_image || 'book-placeholder.jpg') + '" class="book-thumb"></td>' +
                        '<td>' + h.book_title + '</td>' +
                        '<td>' + badge + '</td>' +
                        '<td style="font-size:0.78rem;">' + (h.borrow_date ? h.borrow_date.slice(0,10) : '—') + '</td>' +
                        '<td style="font-size:0.78rem;">' + (h.due_date ? h.due_date.slice(0,10) : '—') + '</td>' +
                        '<td style="font-size:0.78rem;">' + (h.return_date ? h.return_date.slice(0,10) : '—') + '</td>' +
                    '</tr>';
                }).join('');

                document.getElementById('historyModalBody').innerHTML =
                    '<table class="table table-sm mb-0" style="font-size:0.82rem;">' +
                    '<thead><tr><th></th><th>Book</th><th>Status</th><th>Borrowed</th><th>Due</th><th>Returned</th></tr></thead>' +
                    '<tbody>' + rows + '</tbody></table>';
            }, 'json').fail(function () {
                document.getElementById('historyModalBody').innerHTML = '<div class="p-3 text-danger">Network error.</div>';
            });
        });
    });

})();
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/admin_layout.php';
?>