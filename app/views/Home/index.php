<?php include __DIR__ . '/../layouts/header.php'; ?>

<main class="mb-5 pb-5">

    <!-- =====================================================
         HERO SECTION
    ====================================================== -->
    <section id="hero" class="primary hero-banner vh-100 d-flex py-4 align-items-center justify-content-center text-center">
        <div class="hero-content p-2">
            <h1 class="hero-title">Your Campus Library,<br>Always Open.</h1>
            <p class="hero-subtitle">Browse, borrow, and read — anytime, anywhere.</p>
            <a href="/books" class="hero-cta mt-3"><u>Explore the Collection</u></a>
        </div>
    </section>

    <!-- =====================================================
         TOP SEARCHED SECTION
         Backend hook: $topBooks — array of book objects,
         ordered by borrow_request count DESC, limit 10.
         TODO: populate from HomeController after styling is done.
    ====================================================== -->
    <section id="top-searched" class="container-fluid px-3 mt-4">

        <div class="section-header d-flex align-items-center mb-3">
            <h2 class="section-title mb-0 me-3">Top Searched</h2>
            <hr class="flex-grow-1 m-0">
        </div>

        <!-- Horizontal scroll carousel -->
        <div class="book-carousel d-flex gap-3 overflow-auto pb-2">

            <?php
            // TODO: replace with real $topBooks loop from controller
            // foreach ($topBooks as $book): ?>

            <!-- Placeholder cards — remove once backend is wired -->
            <?php for ($i = 0; $i < 6; $i++): ?>
            <div class="book-card-wrapper flex-shrink-0">
                <a href="/books/show/<?= $i /* replace with $book->book_id */ ?>" class="book-card d-block text-decoration-none position-relative overflow-hidden rounded">
                    <!-- Cover image -->
                    <img src="/assets/images/placeholder-cover.jpg"
                         alt="Book Cover"
                         class="book-cover w-100 h-100 object-fit-cover">
                    <!-- Overlay footer -->
                    <div class="book-card-overlay position-absolute bottom-0 start-0 end-0 d-flex align-items-center justify-content-between px-2 py-1">
                        <span class="book-card-label small">Details &amp; read by</span>
                        <i class="bi bi-arrow-right-circle"></i>
                    </div>
                </a>
            </div>
            <?php endfor; ?>

            <!-- Real loop (uncomment after backend is ready):
            <?php // foreach ($topBooks as $book): ?>
            <div class="book-card-wrapper flex-shrink-0">
                <a href="/books/show/<?= $book->book_id ?>" class="book-card d-block text-decoration-none position-relative overflow-hidden rounded">
                    <img src="/assets/images/covers/<?= htmlspecialchars($book->cover_image) ?>"
                         alt="<?= htmlspecialchars($book->book_title) ?>"
                         class="book-cover w-100 h-100 object-fit-cover">
                    <div class="book-card-overlay position-absolute bottom-0 start-0 end-0 d-flex align-items-center justify-content-between px-2 py-1">
                        <span class="book-card-label small"><?= htmlspecialchars($book->book_title) ?></span>
                        <i class="bi bi-arrow-right-circle"></i>
                    </div>
                </a>
            </div>
            <?php // endforeach; ?>
            -->

        </div>

        <div class="text-center mt-3">
            <a href="/books" class="btn show-more-btn">Show More</a>
        </div>

    </section>

    <!-- =====================================================
         MAJOR NEEDS SECTION
         Backend hook:
           $categories — all rows from categories table
           $filteredBooks — books filtered by selected category, limit 8
           $activeCategoryId — the currently selected category_id (from GET param)
         TODO: populate from HomeController after styling is done.
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
        <!-- 
            Active chip is determined server-side via $activeCategoryId.
            JS below also handles client-side visual toggle for UX.
            Clicking a chip navigates to /?category=ID so the controller
            can filter $filteredBooks accordingly.
        -->
        <div class="category-chips d-flex gap-2 flex-wrap mb-4" id="categoryChips">

            <?php
            // TODO: replace hardcoded chips with real $categories loop
            // foreach ($categories as $cat):
            //   $isActive = ($cat->category_id == ($activeCategoryId ?? null));
            // ?>

            <!-- Placeholder chips — replace with loop above -->
            <?php
            $placeholderChips = ['Computer Science', 'Business', 'Industrial Engineering', 'Psychology', 'Communication', 'Law', 'Medicine'];
            foreach ($placeholderChips as $chip):
            ?>
            <button type="button"
                    class="btn chip-btn <?= $chip === 'Computer Science' ? 'chip-active' : '' ?>"
                    data-category="0">
                <?= htmlspecialchars($chip) ?>
            </button>
            <?php endforeach; ?>

        </div>

        <!-- Book grid — 4 columns, 2 rows = 8 books -->
        <div class="row g-3 book-grid" id="majorBooksGrid">

            <?php
            // TODO: replace with real $filteredBooks loop
            // foreach ($filteredBooks as $book): ?>

            <!-- Placeholder grid cards -->
            <?php for ($i = 0; $i < 8; $i++): ?>
            <div class="col-6 col-md-3">
                <a href="/books/show/<?= $i ?>" class="book-grid-card d-block position-relative overflow-hidden rounded text-decoration-none">
                    <img src="/assets/images/placeholder-cover.jpg"
                         alt="Book Cover"
                         class="w-100 book-grid-cover object-fit-cover">
                    <div class="book-grid-overlay position-absolute bottom-0 start-0 end-0 px-2 py-1">
                        <span class="small">Details &amp; read by</span>
                    </div>
                </a>
            </div>
            <?php endfor; ?>

            <!-- Real loop (uncomment after backend is ready):
            <?php // foreach ($filteredBooks as $book): ?>
            <div class="col-6 col-md-3">
                <a href="/books/show/<?= $book->book_id ?>" class="book-grid-card d-block position-relative overflow-hidden rounded text-decoration-none">
                    <img src="/assets/images/covers/<?= htmlspecialchars($book->cover_image) ?>"
                         alt="<?= htmlspecialchars($book->book_title) ?>"
                         class="w-100 book-grid-cover object-fit-cover">
                    <div class="book-grid-overlay position-absolute bottom-0 start-0 end-0 px-2 py-1">
                        <span class="small"><?= htmlspecialchars($book->book_title) ?></span>
                    </div>
                </a>
            </div>
            <?php // endforeach; ?>
            -->

        </div>

        <div class="text-center mt-4">
            <a href="/books" class="btn explore-btn">Explore</a>
        </div>

    </section>

</main>

<script>
// -----------------------------------------------------------------------
// Category chip active state (client-side visual only)
// Full filtering is server-side — this just keeps the UI snappy.
// -----------------------------------------------------------------------
document.querySelectorAll('.chip-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.chip-btn').forEach(b => b.classList.remove('chip-active'));
        this.classList.add('chip-active');

        // TODO: wire up AJAX or page redirect for server-side filtering
        // var categoryId = this.dataset.category;
        // window.location.href = '/?category=' + categoryId;
    });
});

// -----------------------------------------------------------------------
// Category search — filters visible chips by text match
// -----------------------------------------------------------------------
document.getElementById('categorySearch').addEventListener('input', function () {
    var query = this.value.toLowerCase();
    document.querySelectorAll('.chip-btn').forEach(function (btn) {
        btn.style.display = btn.textContent.trim().toLowerCase().includes(query) ? '' : 'none';
    });
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>