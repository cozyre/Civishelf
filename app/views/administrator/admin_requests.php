<?php
// app/views/administrator/admin_requests.php
$statusFilter = $statusFilter??'';
$pendingAdminCount = $pendingAdminCount??0;
$totalRequests = $totalRequests??0;
$limit = $limit??0;
$page = $page??0;
ob_start(); ?>

<!-- ---- Alert ---- -->
<?php if ((int)($pendingAdminCount ?? 0) > 0 && $statusFilter !== 'pending'): ?>
<div class="alert alert-warning d-flex align-items-center gap-2 py-2 mb-3" style="border-radius:8px;">
    <i class="bi bi-shield-exclamation"></i>
    <span><strong><?= (int)$pendingAdminCount ?></strong> pending admin request<?= $pendingAdminCount !== 1 ? 's' : '' ?>.</span>
    <a href="<?= BASE_URL ?>/administrator/adminRequests?status=pending" class="btn btn-sm btn-warning ms-auto">View</a>
</div>
<?php endif; ?>

<!-- ---- Toolbar ---- -->
<div class="section-bar mb-3">
    <h1 class="section-bar-title flex-grow-1">Admin Access Requests</h1>
    <div class="d-flex gap-1">
        <?php foreach (['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', '' => 'All'] as $val => $label): ?>
        <a href="<?= BASE_URL ?>/administrator/adminRequests<?= $val ? '?status=' . $val : '' ?>"
           class="btn btn-sm <?= $statusFilter === $val ? 'btn-adm-primary' : 'btn-adm-ghost' ?>">
            <?= $label ?>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<p class="small text-muted mb-2"><?= number_format((int)$totalRequests) ?> request<?= $totalRequests !== 1 ? 's' : '' ?></p>

<!-- ---- Table ---- -->
<div class="admin-table mb-3">
    <table class="table mb-0">
        <thead>
            <tr>
                <th>User</th>
                <th>Email</th>
                <th>Joined</th>
                <th>Requested</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($requests)): ?>
                <?php foreach ($requests as $r): ?>
                <tr id="req-row-<?= $r['request_id'] ?>">
                    <td style="font-weight:600;"><?= htmlspecialchars($r['user_name']) ?></td>
                    <td style="font-size:0.8rem;"><?= htmlspecialchars($r['email']) ?></td>
                    <td style="font-size:0.78rem;"><?= date('d M Y', strtotime($r['user_joined'])) ?></td>
                    <td style="font-size:0.78rem;"><?= date('d M Y H:i', strtotime($r['requested_at'])) ?></td>
                    <td>
                        <?php if ($r['status'] === 'pending'): ?>
                            <span class="badge-pending2">Pending</span>
                        <?php elseif ($r['status'] === 'approved'): ?>
                            <span class="badge-approved">Approved</span>
                        <?php else: ?>
                            <span class="badge-rejected">Rejected</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($r['status'] === 'pending'): ?>
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-adm-primary btn-approve-req"
                                    data-request-id="<?= $r['request_id'] ?>"
                                    data-user-name="<?= htmlspecialchars($r['user_name']) ?>">
                                <i class="bi bi-check-lg"></i> Approve
                            </button>
                            <button class="btn btn-sm btn-adm-danger btn-reject-req"
                                    data-request-id="<?= $r['request_id'] ?>">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                        <?php else: ?>
                            <span style="font-size:0.72rem; color:#9ca3af;">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center py-4" style="color:#9ca3af;">No requests found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<?php $totalPages = (int)ceil($totalRequests / $limit); if ($totalPages > 1): ?>
<nav>
    <ul class="pagination pagination-sm justify-content-center">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
            <a class="page-link" href="<?= BASE_URL ?>/administrator/adminRequests?page=<?= $i ?><?= $statusFilter ? '&status=' . $statusFilter : '' ?>">
                <?= $i ?>
            </a>
        </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<script>
(function () {
    document.querySelectorAll('.btn-approve-req').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id   = this.dataset.requestId;
            var name = this.dataset.userName;
            if (!confirm('Promote "' + name + '" to admin?')) return;

            $.post(BASE_URL + '/administrator/adminRequestApprove', { request_id: id }, function (res) {
                if (res.success) {
                    var row = document.getElementById('req-row-' + id);
                    if (row) { row.style.opacity = 0; setTimeout(function () { row.remove(); }, 300); }
                } else alert(res.message || 'Failed.');
            }, 'json').fail(function () { alert('Network error.'); });
        });
    });

    document.querySelectorAll('.btn-reject-req').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = this.dataset.requestId;
            if (!confirm('Reject this admin request?')) return;

            $.post(BASE_URL + '/administrator/adminRequestReject', { request_id: id }, function (res) {
                if (res.success) {
                    var row = document.getElementById('req-row-' + id);
                    if (row) { row.style.opacity = 0; setTimeout(function () { row.remove(); }, 300); }
                } else alert(res.message || 'Failed.');
            }, 'json').fail(function () { alert('Network error.'); });
        });
    });
})();
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/admin_layout.php';
?>