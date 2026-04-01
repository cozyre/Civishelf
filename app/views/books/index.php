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
                 data-online="<?= !empty($book['is_online']) ? '1' : '0' ?>"
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
                 data-online="<?= !empty($book['is_online']) ? '1' : '0' ?>"
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
                     data-online="<?= !empty($book['is_online']) ? '1' : '0' ?>"
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

        <div class="text-center mt-4 d-flex justify-content-center gap-2">
            <?php if ($currentPage > 1): ?>
                <a href="<?= BASE_URL ?>/books?page=<?= $currentPage - 1 ?><?= $activeCategoryId ? '&category=' . $activeCategoryId : '' ?>"
                   class="btn show-more-btn">← Previous</a>
            <?php endif; ?>
            <?php if (count($books) === 8): ?>
                <a href="<?= BASE_URL ?>/books?page=<?= $currentPage + 1 ?><?= $activeCategoryId ? '&category=' . $activeCategoryId : '' ?>"
                   class="btn show-more-btn">Next →</a>
            <?php endif; ?>
        </div>

    </section>

</main>

<script>
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
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
