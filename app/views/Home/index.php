<?php include __DIR__ . '/../layouts/header.php'; 
    $categories = $categories??[];
?>

<main class="pb-4 mb-4">

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
            <button class="btn chip-btn chip-active" data-category="">All</button>
            <?php foreach ($categories as $cat): ?>
            <button class="btn chip-btn" data-category="<?= $cat['category_id'] ?>">
                <?= htmlspecialchars($cat['category_name']) ?>
            </button>
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
    <section id="company-details" class="primary row pt-4 px-2">
        <div class="col-md-6 text-center pb-2">
            <img class="" src="<?= BASE_URL ?>/assets/images/logos/logo.png" alt="Civishelf Logo" style="max-height: 8rem;">
            <p class="fs-5 align-content-center text-break">"Empowering campus learning through accessible digital knowledge."</p>
        </div>
        <div class="col-md d-sm-none d-md-flex">
        <div class="vr h-100"></div>
        </div>
        <div class="col-md-5 align-content-center ms-2">
            <ul class="m-0 p-0 lh-lg fs-5">
                <li><i class="bi bi-journal-bookmark me-4"></i>Digital Book Lending</li>
                <li><i class="bi bi-mortarboard me-4"></i>Academic Resources</li>
                <li><i class="bi bi-archive me-4"></i>Research Archives</li>
                <li><i class="bi bi-gear me-4"></i>Campus Integration Systems</li>
            </ul>
        </div>
        <div class="row my-4 p-2 text-center">
            <p class="fs-4 mb-0 me-3">What is Civishelf ?</p>
            <p class="">Civishelf is a campus-focused digital library platform that gives students and faculty 
                easy access to academic books, research materials, and learning resources in one place. 
                It streamlines borrowing, organizing, and discovering content, 
                making studying and research faster, smarter, and fully accessible online.
            </p>
            <div class="d-md-flex justify-content-evenly my-2">
                <div><i class="bi bi-envelope"></i> Civishelf@company.com</div>
                <div><i class="bi bi-telephone"></i> 123-3212-4563</div>
                <div><i class="bi bi-instagram"></i> civishelf.co</div>
                <div><i class="bi bi-buildings"></i> Jakarta, Indonesia</div>
            </div>
        </div>
    </section>
    
</main>

<script>
// Category chip AJAX filter
(function () {
    var coverBase = BASE_URL + '/assets/images/covers/';

    document.querySelectorAll('#categoryChips .chip-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            // Active state
            document.querySelectorAll('#categoryChips .chip-btn').forEach(function (b) {
                b.classList.remove('chip-active');
            });
            this.classList.add('chip-active');

            var categoryId = this.dataset.category;
            var url = BASE_URL + '/home/filterBooks' + (categoryId ? '?category=' + categoryId : '');

            $.get(url, function (res) {
                if (!res.success) return;
                var grid = document.getElementById('majorBooksGrid');
                grid.innerHTML = '';

                if (!res.books.length) {
                    grid.innerHTML = '<p class="opacity-50 py-3">No books in this category yet.</p>';
                    return;
                }

                res.books.forEach(function (book) {
                    var cover = coverBase + (book.cover_image || 'book-placeholder.jpg');
                    var col   = document.createElement('div');
                    col.className = 'col-6 col-md-3';
                    col.innerHTML =
                        '<div class="book-grid-item"' +
                        ' data-id="'          + book.book_id     + '"' +
                        ' data-title="'       + escAttr(book.book_title)      + '"' +
                        ' data-author="'      + escAttr(book.author_name||'') + '"' +
                        ' data-category="'    + escAttr(book.category_name||'') + '"' +
                        ' data-description="' + escAttr(book.description||'') + '"' +
                        ' data-published="'   + escAttr(book.published_at||'') + '"' +
                        ' data-copies="'      + (book.available_copies||0)    + '"' +
                        ' data-cover="'       + cover + '"' +
                        ' data-status="'      + escAttr(book.status||'none')  + '"' +
                        ' data-online="'      + (book.is_online ? '1' : '0')  + '"' +
                        ' data-due="'         + escAttr(book.due_date||'')    + '"' +
                        ' role="button" data-bs-toggle="modal" data-bs-target="#bookModal" tabindex="0">' +
                            '<div class="position-relative overflow-hidden rounded">' +
                                '<img src="' + cover + '" alt="' + escAttr(book.book_title) + '" class="w-100 book-grid-cover">' +
                                '<div class="book-grid-overlay position-absolute bottom-0 start-0 end-0 px-2 py-1">' +
                                    '<span class="small">' + escHtml(book.book_title) + '</span>' +
                                '</div>' +
                            '</div>' +
                        '</div>';
                    grid.appendChild(col);
                });
            }, 'json');
        });
    });

    function escAttr(str) {
        return String(str).replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }
    function escHtml(str) {
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }
})();

// Keep the category search filter (filters chips, not books)
document.getElementById('categorySearch').addEventListener('input', function () {
    var query = this.value.toLowerCase();
    document.querySelectorAll('.chip-btn').forEach(function (btn) {
        btn.style.display = btn.textContent.trim().toLowerCase().includes(query) ? '' : 'none';
    });
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>