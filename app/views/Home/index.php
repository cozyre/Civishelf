<?php include __DIR__ . '/../layouts/header.php'; ?>

<main class="mb-5 pb-5">

    <!-- =====================================================
         HERO SECTION
    ====================================================== -->
    <section id="hero" class="primary hero-banner vh-100 d-flex py-4 align-items-center justify-content-center text-center">
        <div class="hero-content p-2">
            <h1 class="hero-title">Your Campus Library,<br>Always Open.</h1>
            <p class="hero-subtitle">Browse, borrow, and read — anytime, anywhere.</p>
            <a href="<?= BASE_URL ?>/books" class="hero-cta mt-3"><u>Explore the Collection</u></a>
        </div>
    </section>

    <!-- =====================================================
         TOP SEARCHED — ordered by save count
    ====================================================== -->
    <section id="top-searched" class="container-fluid px-3 mt-4">

        <div class="section-header d-flex align-items-center mb-3">
            <h2 class="section-title mb-0 me-3">Top Searched</h2>
            <hr class="flex-grow-1 m-0">
        </div>

        <div class="book-carousel d-flex gap-3 overflow-auto pb-2">
            <?php if (!empty($topBooks)): ?>
                <?php foreach ($topBooks as $book): ?>
                <div class="book-card-wrapper flex-shrink-0">
                    <a href="<?= BASE_URL ?>/books/show/<?= $book['book_id'] ?>"
                       class="book-card d-block text-decoration-none position-relative overflow-hidden rounded">
                        <img src="<?= BASE_URL ?>/assets/images/covers/<?= htmlspecialchars($book['cover_image'] ?? 'book-placeholder.jpg') ?>"
                             alt="<?= htmlspecialchars($book['book_title']) ?>"
                             class="book-cover w-100 h-100 object-fit-cover">
                        <div class="book-card-overlay position-absolute bottom-0 start-0 end-0 d-flex align-items-center justify-content-between px-2 py-1">
                            <span class="book-card-label small"><?= htmlspecialchars($book['book_title']) ?></span>
                            <i class="bi bi-arrow-right-circle"></i>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="opacity-50 py-3">No books yet.</p>
            <?php endif; ?>
        </div>

        <div class="text-center mt-3">
            <a href="<?= BASE_URL ?>/books" class="btn show-more-btn">Show More</a>
        </div>

    </section>

    <!-- =====================================================
         MAJOR NEEDS — category filter + book grid
    ====================================================== -->
    <section id="major-needs" class="container-fluid px-3 mt-5">

        <div class="section-header d-flex align-items-center mb-3">
            <h2 class="section-title mb-0 me-3">Major Needs</h2>
            <hr class="flex-grow-1 m-0">
        </div>

        <!-- Category search bar -->
        <div class="category-search-wrap mb-3">
            <div class="input-group">
                <span class="input-group-text search-icon-wrap">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text"
                       id="categorySearch"
                       class="form-control category-search-input"
                       placeholder="What's your Major?">
                <button class="btn category-search-btn" type="button">
                    <i class="bi bi-plus-lg"></i>
                </button>
            </div>
        </div>

        <!-- Category filter chips -->
        <div class="category-chips d-flex gap-2 flex-wrap mb-4" id="categoryChips">
            <!-- "All" chip -->
            <a href="<?= BASE_URL ?>/"
               class="btn chip-btn <?= $activeCategoryId === null ? 'chip-active' : '' ?>">
                All
            </a>

            <?php foreach ($categories as $cat): ?>
            <a href="<?= BASE_URL ?>/?category=<?= $cat['category_id'] ?>"
               class="btn chip-btn <?= $activeCategoryId === (int)$cat['category_id'] ? 'chip-active' : '' ?>">
                <?= htmlspecialchars($cat['category_name']) ?>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- Book grid -->
        <div class="row g-3 book-grid" id="majorBooksGrid">
            <?php if (!empty($filteredBooks)): ?>
                <?php foreach ($filteredBooks as $book): ?>
                <div class="col-6 col-md-3">
                    <a href="<?= BASE_URL ?>/books/show/<?= $book['book_id'] ?>"
                       class="book-grid-card d-block position-relative overflow-hidden rounded text-decoration-none">
                        <img src="<?= BASE_URL ?>/assets/images/covers/<?= htmlspecialchars($book['cover_image'] ?? 'book-placeholder.jpg') ?>"
                             alt="<?= htmlspecialchars($book['book_title']) ?>"
                             class="w-100 book-grid-cover object-fit-cover">
                        <div class="book-grid-overlay position-absolute bottom-0 start-0 end-0 px-2 py-1">
                            <span class="small"><?= htmlspecialchars($book['book_title']) ?></span>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="opacity-50 py-3">No books in this category yet.</p>
            <?php endif; ?>
        </div>

        <div class="text-center mt-4">
            <a href="<?= BASE_URL ?>/books" class="btn explore-btn">Explore</a>
        </div>
    </section>
    
    <!-- =====================================================
         COMPANY DETAILS
    ====================================================== -->
    <section id="company-details" class="primary-light">
        <div class="p-2 mx-4">
            <img src="<?= BASE_URL ?>/assets/images/logos/logo.png" alt="Civishelf Logo" style="max-height: 4rem;">
            <hr>
        </div>
    </section>

</main>

<script>
// Category chip search — filters visible chips by text (client-side UX only)
document.getElementById('categorySearch').addEventListener('input', function () {
    var query = this.value.toLowerCase();
    document.querySelectorAll('.chip-btn').forEach(function (btn) {
        btn.style.display = btn.textContent.trim().toLowerCase().includes(query) ? '' : 'none';
    });
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>