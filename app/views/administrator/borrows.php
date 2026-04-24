<?php
// app/views/administrator/borrows.php
ob_start(); ?>

<!-- ---- Alert bars ---- -->
<?php if ((int)$pendingCount > 0): ?>
<div class="alert alert-warning d-flex align-items-center gap-2 py-2 mb-3" style="border-radius:8px;">
    <i class="bi bi-hourglass-split"></i>
    <span><strong><?= (int)$pendingCount ?></strong> request<?= $pendingCount !== 1 ? 's' : '' ?> pending approval.</span>
</div>
<?php endif; ?>
<?php if ((int)$overdueCount > 0): ?>
<div class="alert alert-danger d-flex align-items-center gap-2 py-2 mb-3" style="border-radius:8px;">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <span><strong><?= (int)$overdueCount ?></strong> overdue loan<?= $overdueCount !== 1 ? 's' : '' ?> need attention.</span>
</div>
<?php endif; ?>

<!-- ---- Status filter tabs ---- -->
<div class="section-bar mb-3">
    <h1 class="section-bar-title flex-grow-1">Borrow Requests</h1>
    <div class="d-flex gap-1 flex-wrap">
        <?php
        $statuses = [
            ''         => 'All',
            'pending'  => 'Pending',
            'approved' => 'Approved',
            'returned' => 'Returned',
            'rejected' => 'Rejected',
        ];
        foreach ($statuses as $val => $label):
            $active = $statusFilter === $val;
        ?>
        <a href="<?= BASE_URL ?>/administrator/borrows<?= $val ? '?status=' . $val : '' ?>"
           class="btn btn-sm <?= $active ? 'btn-adm-primary' : 'btn-adm-ghost' ?>">
            <?= $label ?>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<p class="small text-muted mb-2"><?= number_format((int)$totalBorrows) ?> request<?= $totalBorrows !== 1 ? 's' : '' ?></p>

<!-- ---- Table ---- -->
<div class="admin-table mb-3">
    <table class="table mb-0">
        <thead>
            <tr>
                <th></th>
                <th>Book</th>
                <th>User</th>
                <th>Status</th>
                <th>Requested</th>
                <th>Due Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($borrows)): ?>
                <?php foreach ($borrows as $br): ?>
                <?php
                    $now      = time();
                    $due      = $br['due_date'] ? strtotime($br['due_date']) : null;
                    $isOverdue = $br['status'] === 'approved' && $due && $due < $now;
                ?>
                <tr id="borrow-row-<?= $br['request_id'] ?>">
                    <td>
                        <img src="<?= BASE_URL ?>/assets/images/covers/<?= htmlspecialchars($br['cover_image'] ?? 'book-placeholder.jpg') ?>"
                             class="book-thumb" alt="">
                    </td>
                    <td style="font-weight:600; font-size:0.85rem;"><?= htmlspecialchars($br['book_title']) ?></td>
                    <td>
                        <div style="font-size:0.82rem;"><?= htmlspecialchars($br['user_name']) ?></div>
                        <div style="font-size:0.7rem; color:#6b7280;"><?= htmlspecialchars($br['email']) ?></div>
                    </td>
                    <td>
                        <?php if ($isOverdue): ?>
                            <span class="badge-overdue">Overdue</span>
                        <?php elseif ($br['status'] === 'pending'): ?>
                            <span class="badge-pending2">Pending</span>
                        <?php elseif ($br['status'] === 'approved'): ?>
                            <span class="badge-approved">Approved</span>
                        <?php elseif ($br['status'] === 'returned'): ?>
                            <span class="badge-returned">Returned</span>
                        <?php elseif ($br['status'] === 'rejected'): ?>
                            <span class="badge-rejected">Rejected</span>
                        <?php endif; ?>
                    </td>
                    <td style="font-size:0.78rem;"><?= date('d M Y', strtotime($br['borrow_date'])) ?></td>
                    <td style="font-size:0.78rem; color:<?= $isOverdue ? '#dc2626' : 'inherit' ?>; font-weight:<?= $isOverdue ? '700' : 'normal' ?>;">
                        <?= $br['due_date'] ? date('d M Y', strtotime($br['due_date'])) : '—' ?>
                        <?php if ($isOverdue): ?>
                            <span class="badge-overdue ms-1">OD</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="d-flex gap-1 flex-wrap">
                            <?php if ($br['status'] === 'pending'): ?>
                                <button class="btn btn-sm btn-adm-primary btn-approve"
                                        data-request-id="<?= $br['request_id'] ?>"
                                        title="Approve">
                                    <i class="bi bi-check-lg"></i> Approve
                                </button>
                                <button class="btn btn-sm btn-adm-danger btn-reject"
                                        data-request-id="<?= $br['request_id'] ?>"
                                        title="Reject">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            <?php elseif ($br['status'] === 'approved'): ?>
                                <button class="btn btn-sm btn-adm-ghost btn-return"
                                        data-request-id="<?= $br['request_id'] ?>"
                                        title="Mark returned">
                                    <i class="bi bi-arrow-return-left"></i> Return
                                </button>
                            <?php else: ?>
                                <span style="font-size:0.72rem; color:#9ca3af;">—</span>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7" class="text-center py-4" style="color:#9ca3af;">No borrow requests found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<?php $totalPages = (int)ceil($totalBorrows / $limit); if ($totalPages > 1): ?>
<nav>
    <ul class="pagination pagination-sm justify-content-center">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
            <a class="page-link" href="<?= BASE_URL ?>/administrator/borrows?page=<?= $i ?><?= $statusFilter ? '&status=' . $statusFilter : '' ?>">
                <?= $i ?>
            </a>
        </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>


<!-- ================================================================
     APPROVE MODAL — lets admin set a custom due date
================================================================= -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title mb-0">Approve Request</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted mb-2">Set due date (default: 14 days).</p>
                <input type="date" id="approveDueDate" class="form-control form-control-sm"
                       value="<?= date('Y-m-d', strtotime('+14 days')) ?>">
                <input type="hidden" id="approveRequestId" value="">
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-sm btn-adm-ghost" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-adm-primary" id="btnConfirmApprove">Confirm Approve</button>
            </div>
        </div>
    </div>
</div>


<!-- ================================================================
     JS
================================================================= -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    var approveModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('approveModal'));

    // ---- Open approve modal ----
    document.querySelectorAll('.btn-approve').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('approveRequestId').value = this.dataset.requestId;
            document.getElementById('approveDueDate').value   = '<?= date('Y-m-d', strtotime('+14 days')) ?>';
            approveModal.show();
        });
    });

    // ---- Confirm approve ----
    document.getElementById('btnConfirmApprove').addEventListener('click', function () {
        var requestId = document.getElementById('approveRequestId').value;
        var dueDate   = document.getElementById('approveDueDate').value;

        if (!dueDate) { alert('Please set a due date.'); return; }

        $.post(BASE_URL + '/administrator/borrowApprove', { request_id: requestId, due_date: dueDate }, function (res) {
            if (res.success) { approveModal.hide(); location.reload(); }
            else alert(res.message || 'Approval failed.');
        }, 'json').fail(function () { alert('Network error.'); });
    });

    // ---- Reject ----
    document.querySelectorAll('.btn-reject').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var requestId = this.dataset.requestId;
            if (!confirm('Reject this borrow request?')) return;

            $.post(BASE_URL + '/administrator/borrowReject', { request_id: requestId }, function (res) {
                if (res.success) location.reload();
                else alert(res.message || 'Failed.');
            }, 'json').fail(function () { alert('Network error.'); });
        });
    });

    // ---- Return ----
    document.querySelectorAll('.btn-return').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var requestId = this.dataset.requestId;
            if (!confirm('Mark this book as returned? Available copies will be incremented.')) return;

            $.post(BASE_URL + '/administrator/borrowReturn', { request_id: requestId }, function (res) {
                if (res.success) location.reload();
                else alert(res.message || 'Failed.');
            }, 'json').fail(function () { alert('Network error.'); });
        });
    });

});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/admin_layout.php';
?>