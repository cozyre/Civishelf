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
                     data-status="none"
                     data-online="<?= isset($book['is_online']) && $book['is_online'] ? '1' : '0' ?>"
                     data-due=""
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
                         data-status="none"
                         data-online="<?= isset($book['is_online']) && $book['is_online'] ? '1' : '0' ?>"
                         data-due=""
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
            <?php else: ?>
                <p class="opacity-50 py-3">No books in this category yet.</p>
            <?php endif; ?>
        </div>

        <div class="text-center my-4">
            <a href="<?= BASE_URL ?>/books" class="btn explore-btn">Explore</a>
        </div>
    </section>
    
    <!-- =====================================================
         COMPANY DETAILS
    ====================================================== -->
    <section id="company-details" class="primary">
        <div class="mx-4 p-2">
            <img src="<?= BASE_URL ?>/assets/images/logos/logo.png" alt="Civishelf Logo" style="max-height: 4rem;">
            <hr>
        </div>
        <div class="row p-1">
            <div class="col-md-4">
                <ul>
                    <li>Civishelf Technologies, Inc.</li>
                    <li>2024</li>
                    <li>Jakarta, Indonesia</li>
                </ul>
            </div>
            <div class="col-md-4 px-4">
                <p><b>Civishelf Digital Library</b><br>
                "Empowering campus learning through accessible digital knowledge."</p>
            </div>
            <div class="col-md-4">
                <p>Business Hours: Monday - Friday: 08:00 - 18:00 WIB
                    <br>&copy; 2026 Civishelf Technologies, Inc. All rights reserved
                    <br>Privacy Policy | Terms of Service
                </p>
            </div>
        </div>
        <div class="row text-center m-2 py-4 px-2">
            <p>Digital Book Lending • Academic Resources • Research Archives • Campus Integration Systems</p>
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