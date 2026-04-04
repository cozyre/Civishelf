<?php
// app/views/administrator/books.php
ob_start(); ?>

<!-- ---- Toolbar ---- -->
<div class="section-bar flex-wrap">
    <h1 class="section-bar-title flex-grow-1">Books</h1>

    <form method="GET" action="<?= BASE_URL ?>/administrator/books" class="d-flex gap-2 flex-wrap">
        <select name="category" class="form-select form-select-sm" style="width:150px;" onchange="this.form.submit()">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['category_id'] ?>" <?= $activeCategoryId === (int)$cat['category_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['category_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="search" class="form-control form-control-sm"
               placeholder="Title or author…" style="width:200px;"
               value="<?= htmlspecialchars($search ?? '') ?>">
        <button type="submit" class="btn btn-sm btn-adm-primary">Search</button>
        <?php if (!empty($search) || $activeCategoryId): ?>
            <a href="<?= BASE_URL ?>/administrator/books" class="btn btn-sm btn-adm-ghost">Clear</a>
        <?php endif; ?>
    </form>

    <button class="btn btn-sm btn-adm-primary" data-bs-toggle="modal" data-bs-target="#bookFormModal" id="btnAddBook">
        <i class="bi bi-plus-lg me-1"></i> Add Book
    </button>
    <button class="btn btn-sm btn-adm-ghost" data-bs-toggle="modal" data-bs-target="#categoryModal">
        <i class="bi bi-tag me-1"></i> Categories
    </button>
    <button class="btn btn-sm btn-adm-ghost" data-bs-toggle="modal" data-bs-target="#authorModal">
        <i class="bi bi-person me-1"></i> Authors
    </button>
</div>

<p class="small text-muted mb-2"><?= number_format((int)$totalBooks) ?> book<?= $totalBooks !== 1 ? 's' : '' ?> found</p>

<!-- ---- Table ---- -->
<div class="admin-table mb-3">
    <table class="table mb-0">
        <thead>
            <tr>
                <th></th>
                <th>Title / Author</th>
                <th>Category</th>
                <th>Copies</th>
                <th>Online</th>
                <th>Published</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($books)): ?>
                <?php foreach ($books as $b): ?>
                <tr id="book-row-<?= $b['book_id'] ?>">
                    <td>
                        <img src="<?= BASE_URL ?>/assets/images/covers/<?= htmlspecialchars($b['cover_image'] ?? 'book-placeholder.jpg') ?>"
                             class="book-thumb" alt="">
                    </td>
                    <td>
                        <div style="font-weight:600;"><?= htmlspecialchars($b['book_title']) ?></div>
                        <div style="font-size:0.72rem; color:#6b7280;"><?= htmlspecialchars($b['author_name'] ?? '—') ?></div>
                    </td>
                    <td style="font-size:0.8rem;"><?= htmlspecialchars($b['category_name'] ?? '—') ?></td>
                    <td style="font-size:0.8rem;">
                        <span class="<?= (int)$b['available_copies'] > 0 ? 'badge-active' : 'badge-banned' ?>">
                            <?= (int)$b['available_copies'] ?> / <?= (int)$b['total_copies'] ?>
                        </span>
                    </td>
                    <td>
                        <?php if (!empty($b['is_online'])): ?>
                            <span class="badge-approved"><i class="bi bi-wifi"></i> Yes</span>
                        <?php else: ?>
                            <span style="font-size:0.68rem; color:#9ca3af;">No</span>
                        <?php endif; ?>
                    </td>
                    <td style="font-size:0.78rem;"><?= htmlspecialchars(substr($b['published_at'] ?? '', 0, 10)) ?></td>
                    <td>
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-adm-ghost btn-edit-book"
                                    data-book='<?= htmlspecialchars(json_encode($b), ENT_QUOTES) ?>'
                                    title="Edit">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-adm-danger btn-delete-book"
                                    data-book-id="<?= $b['book_id'] ?>"
                                    data-book-title="<?= htmlspecialchars($b['book_title']) ?>"
                                    title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7" class="text-center py-4" style="color:#9ca3af;">No books found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<?php $totalPages = (int)ceil($totalBooks / $limit); if ($totalPages > 1): ?>
<nav>
    <ul class="pagination pagination-sm justify-content-center">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
            <a class="page-link" href="<?= BASE_URL ?>/administrator/books?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= $activeCategoryId ? '&category=' . $activeCategoryId : '' ?>">
                <?= $i ?>
            </a>
        </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>


<!-- ================================================================
     BOOK ADD / EDIT MODAL
================================================================= -->
<div class="modal fade" id="bookFormModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form action="<?= BASE_URL ?>/administrator/bookSave" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="book_id"       id="formBookId" value="0">
                <input type="hidden" name="existing_cover" id="formExistingCover" value="">
                <input type="hidden" name="existing_file"  id="formExistingFile"  value="">

                <div class="modal-header py-2">
                    <h6 class="modal-title mb-0" id="bookFormTitle">Add Book</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-8">
                            <label class="form-label small fw-semibold">Title <span class="text-danger">*</span></label>
                            <input type="text" name="book_title" id="formTitle" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Published Date</label>
                            <input type="date" name="published_at" id="formPublished" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Author <span class="text-danger">*</span></label>
                            <select name="author_id" id="formAuthor" class="form-select form-select-sm" required>
                                <option value="">Select Author</option>
                                <?php foreach ($authors as $a): ?>
                                    <option value="<?= $a['author_id'] ?>"><?= htmlspecialchars($a['author_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Category <span class="text-danger">*</span></label>
                            <select name="category_id" id="formCategory" class="form-select form-select-sm" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $c): ?>
                                    <option value="<?= $c['category_id'] ?>"><?= htmlspecialchars($c['category_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold">Description</label>
                            <textarea name="description" id="formDesc" class="form-control form-control-sm" rows="3"></textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Total Copies</label>
                            <input type="number" name="total_copies" id="formCopies" class="form-control form-control-sm" value="1" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Cover Image</label>
                            <input type="file" name="cover_image" class="form-control form-control-sm" accept="image/*">
                            <div class="small text-muted mt-1" id="currentCoverLabel"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">PDF File</label>
                            <input type="file" name="book_file" class="form-control form-control-sm" accept=".pdf">
                            <div class="small text-muted mt-1" id="currentFileLabel"></div>
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input type="checkbox" name="is_online" id="formIsOnline" class="form-check-input" value="1">
                                <label class="form-check-label small" for="formIsOnline">
                                    Available to read online (requires PDF)
                                </label>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-sm btn-adm-ghost" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-adm-primary" id="bookFormSubmit">Save Book</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- ================================================================
     CATEGORY MODAL
================================================================= -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title mb-0">Manage Categories</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Add form -->
                <form action="<?= BASE_URL ?>/administrator/categorySave" method="POST" class="mb-3">
                    <div class="row g-2">
                        <div class="col">
                            <input type="text" name="category_name" class="form-control form-control-sm" placeholder="Category name" required>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-sm btn-adm-primary">Add</button>
                        </div>
                    </div>
                    <input type="text" name="description" class="form-control form-control-sm mt-2" placeholder="Description (optional)">
                </form>
                <!-- List -->
                <table class="table table-sm mb-0" style="font-size:0.82rem;">
                    <tbody>
                        <?php foreach ($categories as $c): ?>
                        <tr id="cat-row-<?= $c['category_id'] ?>">
                            <td><?= htmlspecialchars($c['category_name']) ?></td>
                            <td class="text-end">
                                <button class="btn btn-xs btn-adm-danger btn-del-cat"
                                        data-cat-id="<?= $c['category_id'] ?>"
                                        style="padding:0.15rem 0.5rem; font-size:0.7rem;">
                                    Delete
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- ================================================================
     AUTHOR MODAL
================================================================= -->
<div class="modal fade" id="authorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title mb-0">Add Author</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="<?= BASE_URL ?>/administrator/authorSave" method="POST">
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Author Name <span class="text-danger">*</span></label>
                        <input type="text" name="author_name" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Bio</label>
                        <textarea name="bio" class="form-control form-control-sm" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-sm btn-adm-primary w-100">Save Author</button>
                </form>

                <hr class="my-3">
                <p class="small fw-semibold text-muted mb-2">Existing Authors</p>
                <ul class="list-unstyled small" style="max-height:200px; overflow-y:auto;">
                    <?php foreach ($authors as $a): ?>
                        <li class="py-1 border-bottom"><?= htmlspecialchars($a['author_name']) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>


<!-- ================================================================
     JS
================================================================= -->
<script>
(function () {

    // ---- Edit book ----
    document.querySelectorAll('.btn-edit-book').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var b = JSON.parse(this.dataset.book);

            document.getElementById('bookFormTitle').textContent  = 'Edit Book';
            document.getElementById('bookFormSubmit').textContent = 'Update Book';
            document.getElementById('formBookId').value           = b.book_id;
            document.getElementById('formTitle').value            = b.book_title || '';
            document.getElementById('formDesc').value             = b.description || '';
            document.getElementById('formCopies').value           = b.total_copies || 1;
            document.getElementById('formPublished').value        = (b.published_at || '').slice(0, 10);
            document.getElementById('formAuthor').value           = b.author_id || '';
            document.getElementById('formCategory').value         = b.category_id || '';
            document.getElementById('formIsOnline').checked       = b.is_online == 1;
            document.getElementById('formExistingCover').value    = b.cover_image || '';
            document.getElementById('formExistingFile').value     = b.file_path || '';
            document.getElementById('currentCoverLabel').textContent = b.cover_image ? 'Current: ' + b.cover_image : '';
            document.getElementById('currentFileLabel').textContent  = b.file_path  ? 'Current: ' + b.file_path  : '';

            bootstrap.Modal.getOrCreateInstance(document.getElementById('bookFormModal')).show();
        });
    });

    // Reset modal on Add Book click
    document.getElementById('btnAddBook').addEventListener('click', function () {
        document.getElementById('bookFormTitle').textContent  = 'Add Book';
        document.getElementById('bookFormSubmit').textContent = 'Save Book';
        document.getElementById('formBookId').value           = '0';
        document.getElementById('formTitle').value            = '';
        document.getElementById('formDesc').value             = '';
        document.getElementById('formCopies').value           = '1';
        document.getElementById('formPublished').value        = '';
        document.getElementById('formAuthor').value           = '';
        document.getElementById('formCategory').value         = '';
        document.getElementById('formIsOnline').checked       = false;
        document.getElementById('formExistingCover').value    = '';
        document.getElementById('formExistingFile').value     = '';
        document.getElementById('currentCoverLabel').textContent = '';
        document.getElementById('currentFileLabel').textContent  = '';
    });

    // ---- Delete book ----
    document.querySelectorAll('.btn-delete-book').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var bookId = this.dataset.bookId;
            var title  = this.dataset.bookTitle;
            if (!confirm('Delete "' + title + '"? This cannot be undone.')) return;

            $.post(BASE_URL + '/administrator/bookDelete', { book_id: bookId }, function (res) {
                if (res.success) {
                    var row = document.getElementById('book-row-' + bookId);
                    if (row) { row.style.opacity = 0; setTimeout(function () { row.remove(); }, 300); }
                } else alert(res.message || 'Delete failed.');
            }, 'json').fail(function () { alert('Network error.'); });
        });
    });

    // ---- Delete category ----
    document.querySelectorAll('.btn-del-cat').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var catId = this.dataset.catId;
            if (!confirm('Delete this category? Books in it will be uncategorised.')) return;

            $.post(BASE_URL + '/administrator/categoryDelete', { category_id: catId }, function (res) {
                if (res.success) {
                    var row = document.getElementById('cat-row-' + catId);
                    if (row) row.remove();
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