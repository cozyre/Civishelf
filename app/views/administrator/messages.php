<?php
// app/views/administrator/messages.php
$totalMessages = $totalMessages??0;
$limit = $limit??0;
$page = $page??"";
ob_start(); ?>

<div class="section-bar mb-4">
    <h1 class="section-bar-title flex-grow-1">Recieved Messages</h1>
    <span class="small text-muted"><?= number_format((int)$totalMessages) ?> message<?= $totalMessages !== 1 ? 's' : '' ?></span>
</div>

<?php if (!empty($messages)): ?>
<div class="admin-table">
    <table class="table mb-0">
        <thead>
            <tr>
                <th>Sender</th>
                <th>Email</th>
                <th>Message</th>
                <th>Received</th>
                <th style="width:60px;"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($messages as $m): ?>
            <tr id="msg-row-<?= $m['message_id'] ?>">
                <td style="font-weight:600; white-space:nowrap;"><?= htmlspecialchars($m['sender_name']) ?></td>
                <td style="font-size:0.8rem;">
                    <a href="mailto:<?= htmlspecialchars($m['email']) ?>" style="color:var(--adm-text);">
                        <?= htmlspecialchars($m['email']) ?>
                    </a>
                </td>
                <td style="font-size:0.82rem; max-width:400px;">
                    <div class="msg-preview" style="white-space:pre-wrap; max-height:60px; overflow:hidden; cursor:pointer;"
                         onclick="this.style.maxHeight = this.style.maxHeight === 'none' ? '60px' : 'none';">
                        <?= htmlspecialchars($m['message']) ?>
                    </div>
                    <span class="small text-muted" style="font-size:0.68rem;">click to expand</span>
                </td>
                <td style="font-size:0.78rem; white-space:nowrap;"><?= date('d M Y H:i', strtotime($m['created_at'])) ?></td>
                <td>
                    <button class="btn btn-sm btn-adm-danger btn-delete-msg"
                            data-msg-id="<?= $m['message_id'] ?>"
                            title="Delete">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<?php $totalPages = (int)ceil($totalMessages / $limit); if ($totalPages > 1): ?>
<nav class="mt-3">
    <ul class="pagination pagination-sm justify-content-center">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
            <a class="page-link" href="<?= BASE_URL ?>/administrator/messages?page=<?= $i ?>"><?= $i ?></a>
        </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<?php else: ?>
<div class="text-center py-5" style="color:#9ca3af;">
    <i class="bi bi-envelope-open fs-1 d-block mb-3 opacity-25"></i>
    <p class="mb-0">No messages yet.</p>
</div>
<?php endif; ?>

<script>
(function () {
    document.querySelectorAll('.btn-delete-msg').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (!confirm('Delete this message? This cannot be undone.')) return;
            var msgId = this.dataset.msgId;

            $.post(BASE_URL + '/administrator/messageDelete', { message_id: msgId }, function (res) {
                if (res.success) {
                    var row = document.getElementById('msg-row-' + msgId);
                    if (row) { row.style.opacity = 0; setTimeout(function () { row.remove(); }, 300); }
                } else alert(res.message || 'Delete failed.');
            }, 'json').fail(function () { alert('Network error.'); });
        });
    });
})();
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/admin_layout.php';
?>