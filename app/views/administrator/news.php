<?php
// app/views/administrator/news.php
ob_start(); ?>

<!-- ---- Toolbar ---- -->
<div class="section-bar mb-4">
    <h1 class="section-bar-title flex-grow-1">News</h1>
    <button class="btn btn-sm btn-adm-primary" data-bs-toggle="modal" data-bs-target="#newsFormModal" id="btnAddNews">
        <i class="bi bi-plus-lg me-1"></i> New Article
    </button>
</div>

<!-- ---- Article list ---- -->
<?php if (!empty($newsList)): ?>
<div class="admin-table">
    <table class="table mb-0">
        <thead>
            <tr>
                <th style="width:70px;"></th>
                <th>Title</th>
                <th>Published</th>
                <th style="width:120px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($newsList as $n): ?>
            <tr id="news-row-<?= $n['news_id'] ?>">
                <td>
                    <?php if (!empty($n['image'])): ?>
                        <img src="<?= BASE_URL ?>/assets/images/news/<?= htmlspecialchars($n['image']) ?>"
                             style="width:60px; height:42px; object-fit:cover; border-radius:4px; display:block;" alt="">
                    <?php else: ?>
                        <div style="width:60px; height:42px; background:#e5e7eb; border-radius:4px; display:flex; align-items:center; justify-content:center;">
                            <i class="bi bi-image text-muted"></i>
                        </div>
                    <?php endif; ?>
                </td>
                <td>
                    <div style="font-weight:600; font-size:0.9rem;"><?= htmlspecialchars($n['news_title']) ?></div>
                    <div style="font-size:0.72rem; color:#6b7280; margin-top:0.15rem;">
                        <?= htmlspecialchars(mb_substr(strip_tags($n['content']), 0, 100)) ?>…
                    </div>
                </td>
                <td style="font-size:0.78rem; white-space:nowrap;"><?= date('d M Y', strtotime($n['created_at'])) ?></td>
                <td>
                    <div class="d-flex gap-1">
                        <button class="btn btn-sm btn-adm-ghost btn-edit-news"
                                data-news='<?= htmlspecialchars(json_encode($n), ENT_QUOTES) ?>'
                                title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-adm-danger btn-delete-news"
                                data-news-id="<?= $n['news_id'] ?>"
                                data-news-title="<?= htmlspecialchars($n['news_title']) ?>"
                                title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php else: ?>
    <div class="text-center py-5" style="color:#9ca3af;">
        <i class="bi bi-newspaper fs-1 d-block mb-3 opacity-25"></i>
        <p class="mb-0">No articles yet. Create your first one.</p>
    </div>
<?php endif; ?>


<!-- ================================================================
     ADD / EDIT MODAL
================================================================= -->
<div class="modal fade" id="newsFormModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form action="<?= BASE_URL ?>/administrator/newsSave" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="news_id"       id="newsFormId"    value="0">
                <input type="hidden" name="existing_image" id="newsExistImg"  value="">

                <div class="modal-header py-2">
                    <h6 class="modal-title mb-0" id="newsFormTitle">New Article</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Title <span class="text-danger">*</span></label>
                        <input type="text" name="news_title" id="newsTitle" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Content <span class="text-danger">*</span></label>
                        <textarea name="content" id="newsContent" class="form-control form-control-sm" rows="8" required></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Image</label>
                        <input type="file" name="news_image" class="form-control form-control-sm" accept="image/*">
                        <div class="small text-muted mt-1" id="newsCurrentImg"></div>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-adm-ghost" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-adm-primary" id="newsFormSubmit">Publish</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- ================================================================
     JS
================================================================= -->
<script>
(function () {

    // ---- Reset on Add ----
    document.getElementById('btnAddNews').addEventListener('click', function () {
        document.getElementById('newsFormTitle').textContent  = 'New Article';
        document.getElementById('newsFormSubmit').textContent = 'Publish';
        document.getElementById('newsFormId').value           = '0';
        document.getElementById('newsTitle').value            = '';
        document.getElementById('newsContent').value          = '';
        document.getElementById('newsExistImg').value         = '';
        document.getElementById('newsCurrentImg').textContent = '';
    });

    // ---- Edit ----
    document.querySelectorAll('.btn-edit-news').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var n = JSON.parse(this.dataset.news);
            document.getElementById('newsFormTitle').textContent  = 'Edit Article';
            document.getElementById('newsFormSubmit').textContent = 'Update';
            document.getElementById('newsFormId').value           = n.news_id;
            document.getElementById('newsTitle').value            = n.news_title || '';
            document.getElementById('newsContent').value          = n.content || '';
            document.getElementById('newsExistImg').value         = n.image || '';
            document.getElementById('newsCurrentImg').textContent = n.image ? 'Current: ' + n.image : '';
            bootstrap.Modal.getOrCreateInstance(document.getElementById('newsFormModal')).show();
        });
    });

    // ---- Delete ----
    document.querySelectorAll('.btn-delete-news').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var newsId = this.dataset.newsId;
            var title  = this.dataset.newsTitle;
            if (!confirm('Delete "' + title + '"?')) return;

            $.post(BASE_URL + '/administrator/newsDelete', { news_id: newsId }, function (res) {
                if (res.success) {
                    var row = document.getElementById('news-row-' + newsId);
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