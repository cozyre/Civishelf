<?php include __DIR__ . '/../layouts/header.php'; ?>

<main class="mb-5 pb-5">

    <!-- =====================================================
         SEARCH + CATEGORY FILTERS
    ====================================================== -->
    <section class="container-fluid px-3 mt-4">

        <div class="input-group mb-3 explore-search-wrap">
            <span class="input-group-text search-icon-wrap">
                <i class="bi bi-search"></i>
            </span>
            <input type="text"
                   id="bookSearch"
                   class="form-control explore-search-input"
                   placeholder="Search Title, Author, or category">
            <button class="btn category-search-btn" type="button">
                <i class="bi bi-plus-lg"></i>
            </button>
        </div>

        <!-- Category filter chips — server-side active state -->
        <div class="category-chips d-flex gap-2 flex-wrap mb-4" id="categoryChips">
            <a href="<?= BASE_URL ?>/books"
               class="btn chip-btn <?= $activeCategoryId === null ? 'chip-active' : '' ?>">
                All
            </a>
            <?php foreach ($categories as $cat): ?>
            <a href="<?= BASE_URL ?>/books?category=<?= $cat['category_id'] ?>"
               class="btn chip-btn <?= $activeCategoryId === (int)$cat['category_id'] ? 'chip-active' : '' ?>">
                <?= htmlspecialchars($cat['category_name']) ?>
            </a>
            <?php endforeach; ?>
        </div>

    </section>

    <!-- =====================================================
         HERO MASONRY — featured books
    ====================================================== -->
    <section class="container-fluid px-3 mb-5">
        <div class="book-gallery" id="heroGallery">
            <?php foreach ($featuredBooks as $book): ?>
            <?php $cover = BASE_URL . '/assets/images/covers/' . ($book['cover_image'] ?? 'book-placeholder.jpg'); ?>
            <div class="gallery-item"
                 data-id="<?= $book['book_id'] ?>"
                 data-title="<?= htmlspecialchars($book['book_title']) ?>"
                 data-author="<?= htmlspecialchars($book['author_name'] ?? '') ?>"
                 data-category="<?= htmlspecialchars($book['category_name'] ?? '') ?>"
                 data-description="<?= htmlspecialchars($book['description'] ?? '') ?>"
                 data-published="<?= htmlspecialchars($book['published_at'] ?? '') ?>"
                 data-copies="<?= (int)$book['available_copies'] ?>"
                 data-cover="<?= $cover ?>"
                 data-status="<?= htmlspecialchars($book['status']) ?>"
                 data-due="<?= htmlspecialchars($book['due_date'] ?? '') ?>"
                 role="button"
                 data-bs-toggle="modal"
                 data-bs-target="#bookModal"
                 tabindex="0">
                <img src="<?= $cover ?>"
                     alt="<?= htmlspecialchars($book['book_title']) ?>"
                     class="gallery-cover">
                <div class="gallery-item-overlay">
                    <span class="small"><?= htmlspecialchars($book['book_title']) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- =====================================================
         MOST POPULAR — ordered by borrow count
    ====================================================== -->
    <section class="container-fluid px-3 mb-5">

        <div class="section-header d-flex align-items-center mb-3">
            <h2 class="section-title mb-0 me-3">Most Popular</h2>
            <hr class="flex-grow-1 m-0">
        </div>

        <div class="book-carousel d-flex gap-3 overflow-auto pb-2">
            <?php foreach ($popularBooks as $book): ?>
            <?php $cover = BASE_URL . '/assets/images/covers/' . ($book['cover_image'] ?? 'book-placeholder.jpg'); ?>
            <div class="book-card-wrapper flex-shrink-0"
                 data-id="<?= $book['book_id'] ?>"
                 data-title="<?= htmlspecialchars($book['book_title']) ?>"
                 data-author="<?= htmlspecialchars($book['author_name'] ?? '') ?>"
                 data-category="<?= htmlspecialchars($book['category_name'] ?? '') ?>"
                 data-description="<?= htmlspecialchars($book['description'] ?? '') ?>"
                 data-published="<?= htmlspecialchars($book['published_at'] ?? '') ?>"
                 data-copies="<?= (int)$book['available_copies'] ?>"
                 data-cover="<?= $cover ?>"
                 data-status="<?= htmlspecialchars($book['status']) ?>"
                 data-due="<?= htmlspecialchars($book['due_date'] ?? '') ?>"
                 role="button"
                 data-bs-toggle="modal"
                 data-bs-target="#bookModal"
                 tabindex="0">
                <div class="book-card position-relative overflow-hidden rounded h-100">
                    <img src="<?= $cover ?>"
                         alt="<?= htmlspecialchars($book['book_title']) ?>"
                         class="book-cover w-100 h-100 object-fit-cover">
                    <div class="book-card-overlay position-absolute bottom-0 start-0 end-0 d-flex align-items-center justify-content-between px-2 py-1">
                        <span class="book-card-label small"><?= htmlspecialchars($book['book_title']) ?></span>
                        <i class="bi bi-arrow-right-circle"></i>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    </section>

    <!-- =====================================================
         MAIN BOOK GRID — paginated, filterable
    ====================================================== -->
    <section class="container-fluid px-3">
        <div class="section-header d-flex align-items-center mb-3">
            <h2 class="section-title mb-0 me-3">Look For More</h2>
            <hr class="flex-grow-1 m-0">
        </div>

        <div class="row g-3" id="bookGrid">
            <?php foreach ($books as $book): ?>
            <?php $cover = BASE_URL . '/assets/images/covers/' . ($book['cover_image'] ?? 'book-placeholder.jpg'); ?>
            <div class="col-6 col-md-3">
                <div class="book-grid-item"
                     data-id="<?= $book['book_id'] ?>"
                     data-title="<?= htmlspecialchars($book['book_title']) ?>"
                     data-author="<?= htmlspecialchars($book['author_name'] ?? '') ?>"
                     data-category="<?= htmlspecialchars($book['category_name'] ?? '') ?>"
                     data-description="<?= htmlspecialchars($book['description'] ?? '') ?>"
                     data-published="<?= htmlspecialchars($book['published_at'] ?? '') ?>"
                     data-copies="<?= (int)$book['available_copies'] ?>"
                     data-cover="<?= $cover ?>"
                     data-status="<?= htmlspecialchars($book['status']) ?>"
                     data-due="<?= htmlspecialchars($book['due_date'] ?? '') ?>"
                     role="button"
                     data-bs-toggle="modal"
                     data-bs-target="#bookModal"
                     tabindex="0">
                    <div class="position-relative overflow-hidden rounded">
                        <img src="<?= $cover ?>"
                             alt="<?= htmlspecialchars($book['book_title']) ?>"
                             class="w-100 book-grid-cover">
                        <div class="book-grid-overlay position-absolute bottom-0 start-0 end-0 px-2 py-1">
                            <span class="small"><?= htmlspecialchars($book['book_title']) ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Simple previous/next pagination -->
        <div class="text-center mt-4 d-flex justify-content-center gap-2">
            <?php if ($currentPage > 1): ?>
                <a href="<?= BASE_URL ?>/books?page=<?= $currentPage - 1 ?><?= $activeCategoryId ? '&category=' . $activeCategoryId : '' ?>"
                   class="btn show-more-btn">← Previous</a>
            <?php endif; ?>
            <?php if (count($books) === 8): // if a full page came back, there's likely more ?>
                <a href="<?= BASE_URL ?>/books?page=<?= $currentPage + 1 ?><?= $activeCategoryId ? '&category=' . $activeCategoryId : '' ?>"
                   class="btn show-more-btn">Next →</a>
            <?php endif; ?>
        </div>

    </section>

</main>


<!-- ================================================================
     BOOK DETAIL MODAL
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


<!-- ================================================================
     JS
================================================================= -->
<script>
(function () {

    // Modal: populate from clicked element's data-* attributes
    document.getElementById('bookModal').addEventListener('show.bs.modal', function (e) {
        var t = e.relatedTarget;

        document.getElementById('modalTitle').textContent       = t.dataset.title       || '';
        document.getElementById('modalAuthor').textContent      = t.dataset.author      || '';
        document.getElementById('modalCategory').textContent    = t.dataset.category    || '';
        document.getElementById('modalDescription').textContent = t.dataset.description || '';
        document.getElementById('modalPublished').textContent   = t.dataset.published   || '';
        document.getElementById('modalCopies').textContent      = t.dataset.copies      || '0';
        document.getElementById('modalCover').src               = t.dataset.cover       || '';
        document.getElementById('modalCover').alt               = t.dataset.title       || '';

        var status    = t.dataset.status || 'none';
        var statusEl  = document.getElementById('modalStatus');
        var actionBtn = document.getElementById('modalActionBtn');

        if (status === 'borrowed') {
            statusEl.textContent  = 'Borrowed till ' + (t.dataset.due || '');
            actionBtn.textContent = 'Return';
        } else if (status === 'online') {
            statusEl.textContent  = 'Open to read online';
            actionBtn.textContent = 'Read';
        } else {
            statusEl.textContent  = 'None';
            actionBtn.textContent = 'Borrow';
        }

        // TODO: POST /saved/status?book_id= → check saved state, toggle bookmark icon
        // TODO: wire Preview → /books/preview/{id}
        // TODO: wire Borrow  → POST /borrow/request
        // TODO: wire Return  → POST /borrow/return
        // TODO: wire Read    → /books/read/{id} (auth-gated)
    });

    // Client-side search filter (title/author/category in current DOM)
    document.getElementById('bookSearch').addEventListener('input', function () {
        var q = this.value.toLowerCase();

        document.querySelectorAll('.gallery-item').forEach(function (item) {
            var match = (item.dataset.title    || '').toLowerCase().includes(q) ||
                        (item.dataset.author   || '').toLowerCase().includes(q) ||
                        (item.dataset.category || '').toLowerCase().includes(q);
            item.style.display = match ? '' : 'none';
        });

        document.querySelectorAll('#bookGrid [class*="col"]').forEach(function (col) {
            var item  = col.querySelector('.book-grid-item');
            var match = item &&
                        ((item.dataset.title    || '').toLowerCase().includes(q) ||
                         (item.dataset.author   || '').toLowerCase().includes(q) ||
                         (item.dataset.category || '').toLowerCase().includes(q));
            col.style.display = match ? '' : 'none';
        });
    });

})();
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>