<?php include __DIR__ . '/../layouts/header.php'; ?>

<!--
    BACKEND HOOKS (wire up later in BookController::index()):
    $featuredBooks    — small set (5–6) for the top masonry hero
    $popularBooks     — ordered by borrow_request count DESC, limit ~10
    $books            — main paginated listing, filtered by $activeCategoryId
    $categories       — all rows from categories table
    $activeCategoryId — currently selected category_id (from GET ?category=)

    Each book object needs:
      book_id, book_title, author_name (joined), category_name (joined),
      description, cover_image, available_copies, published_at
    Per logged-in user (resolved server-side):
      borrow_status ('none'|'borrowed'|'online'), due_date
-->

<main class="mb-5 pb-5">

    <!-- =====================================================
         SEARCH + CATEGORY FILTERS
    ====================================================== -->
    <section class="container-fluid px-3 mt-4">

        <!-- Search bar -->
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

        <!-- Category filter chips -->
        <!--
            TODO: replace with $categories loop from controller.
            $activeCategoryId drives which chip gets chip-active.
        -->
        <div class="category-chips d-flex gap-2 flex-wrap mb-4" id="categoryChips">
            <?php
            $placeholderCategories = [
                ['id' => 0, 'name' => 'All'],
                ['id' => 1, 'name' => 'Computer Science'],
                ['id' => 2, 'name' => 'Business'],
                ['id' => 3, 'name' => 'Industrial Engineering'],
                ['id' => 4, 'name' => 'Psychology'],
                ['id' => 5, 'name' => 'Communication'],
                ['id' => 6, 'name' => 'Law'],
                ['id' => 7, 'name' => 'Medicine'],
            ];
            foreach ($placeholderCategories as $i => $cat):
            ?>
            <button type="button"
                    class="btn chip-btn <?= $i === 0 ? 'chip-active' : '' ?>"
                    data-category="<?= $cat['id'] ?>">
                <?= htmlspecialchars($cat['name']) ?>
            </button>
            <?php endforeach; ?>
        </div>

    </section>

    <!-- =====================================================
         HERO MASONRY — top featured books, dynamic layout
         TODO: loop $featuredBooks (5–6 books), keep count odd
         so the masonry fills asymmetrically and looks natural.
    ====================================================== -->
    <section class="container-fluid px-3 mb-5">

        <div class="book-gallery" id="heroGallery">
            <?php
            for ($i = 0; $i < 6; $i++):
            ?>
            <div class="gallery-item"
             data-id="<?= $i + 1 ?>"
             data-title="Book Title <?= $i + 1 ?>"
             data-author="Author Name"
             data-category="Computer Science"
             data-description="Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis lacus felis, aliquet at lorem vitae, blandit maximus lectus. Vivamus venenatis accumsan augue."
             data-published="2023"
             data-copies="<?= rand(0, 5) ?>"
             data-cover="/assets/images/placeholder-cover.jpg"
             data-status="<?= ['none','borrowed','online'][$i % 3] ?>"
             data-due="20/03/2026"
             role="button"
             data-bs-toggle="modal"
             data-bs-target="#bookModal"
             tabindex="0">
            <img src="/Civishelf/public/assets/images/book-placeholder.jpg"
                 alt="Book Title <?= $i + 1 ?>"
                 class="gallery-cover">
            <div class="gallery-item-overlay">
                <span class="small">Book Title <?= $i + 1 ?></span>
            </div>
            </div>
            <?php endfor; ?>
        </div>

    </section>

    <!-- =====================================================
         MOST POPULAR — horizontal scroll carousel
         TODO: loop $popularBooks
    ====================================================== -->
    <section class="container-fluid px-3 mb-5">

        <div class="section-header d-flex align-items-center mb-3">
            <h2 class="section-title mb-0 me-3">Most Popular</h2>
            <hr class="flex-grow-1 m-0">
        </div>

        <div class="book-carousel d-flex gap-3 overflow-auto pb-2">
            <?php for ($i = 0; $i < 8; $i++): ?>
            <div class="book-card-wrapper flex-shrink-0"
                 data-id="<?= $i + 100 ?>"
                 data-title="Popular Book <?= $i + 1 ?>"
                 data-author="Author Name"
                 data-category="Business"
                 data-description="Lorem ipsum dolor sit amet, consectetur adipiscing elit."
                 data-published="2022"
                 data-copies="<?= rand(0, 4) ?>"
                 data-cover="/assets/images/placeholder-cover.jpg"
                 data-status="none"
                 data-due=""
                 role="button"
                 data-bs-toggle="modal"
                 data-bs-target="#bookModal"
                 tabindex="0">
                <div class="book-card position-relative overflow-hidden rounded h-100">
                    <img src="/Civishelf/public/assets/images/book-placeholder.jpg"
                         alt="Popular Book <?= $i + 1 ?>"
                         class="book-cover w-100 h-100 object-fit-cover">
                    <div class="book-card-overlay position-absolute bottom-0 start-0 end-0 d-flex align-items-center justify-content-between px-2 py-1">
                        <span class="book-card-label small">Details &amp; read by</span>
                        <i class="bi bi-arrow-right-circle"></i>
                    </div>
                </div>
            </div>
            <?php endfor; ?>
        </div>

    </section>

    <!-- =====================================================
         MAIN BOOK GRID — 4-column uniform grid
         Paginated, filtered by active category.
         TODO: loop $books
    ====================================================== -->
    <section class="container-fluid px-3">

        <div class="row g-3" id="bookGrid">
            <?php for ($i = 0; $i < 8; $i++): ?>
            <div class="col-6 col-md-3">
                <div class="book-grid-item"
                     data-id="<?= $i + 200 ?>"
                     data-title="Book Title <?= $i + 1 ?>"
                     data-author="Author Name"
                     data-category="Computer Science"
                     data-description="Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis lacus felis, aliquet at lorem vitae."
                     data-published="2024"
                     data-copies="<?= rand(0, 5) ?>"
                     data-cover="/Civishelf/public/assets/images/book-placeholder.jpg"
                     data-status="<?= ['none','borrowed','online'][$i % 3] ?>"
                     data-due="20/03/2026"
                     role="button"
                     data-bs-toggle="modal"
                     data-bs-target="#bookModal"
                     tabindex="0">
                    <div class="position-relative overflow-hidden rounded">
                        <img src="/Civishelf/public/assets/images/book-placeholder.jpg"
                             alt="Book Title <?= $i + 1 ?>"
                             class="w-100 book-grid-cover">
                        <div class="book-grid-overlay position-absolute bottom-0 start-0 end-0 px-2 py-1">
                            <span class="small">Details &amp; read by</span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endfor; ?>
        </div>

        <div class="text-center mt-4">
            <button class="btn show-more-btn" id="loadMoreBtn">Show More</button>
        </div>

    </section>

</main>


<!-- ================================================================
     BOOK DETAIL MODAL
     Populated by JS from data-* attributes on the clicked element.
     Status drives action button label:
       'none'     → Borrow
       'borrowed' → Return
       'online'   → Read
================================================================= -->
<div class="modal fade" id="bookModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content book-modal-content p-0 overflow-hidden">

            <div class="row g-0" style="min-height: 300px;">

                <!-- Cover -->
                <div class="col-5 book-modal-cover-wrap">
                    <img src="" alt="" id="modalCover" class="book-modal-cover">
                </div>

                <!-- Details -->
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

                    <p class="small mb-1 mt-auto">available copies: <span id="modalCopies"></span></p>

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
     STYLES — move to style.css once finalized
================================================================= -->
<style>

/* --- Masonry hero --- */
.book-gallery {
    columns: 3 120px;
    column-gap: 0.5rem;
}

.gallery-item {
    break-inside: avoid;
    margin-bottom: 0.5rem;
    position: relative;
    cursor: pointer;
    border-radius: 6px;
    overflow: hidden;
}

.gallery-cover {
    width: 100%;
    display: block;
    object-fit: cover;
    border-radius: 6px;
    transition: transform 0.2s ease, filter 0.2s ease;
}

.gallery-item:hover .gallery-cover {
    transform: scale(1.03);
    filter: brightness(0.72);
}

.gallery-item-overlay {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    padding: 0.4rem 0.5rem;
    background: linear-gradient(transparent, rgba(0,0,0,0.65));
    color: #fff;
    opacity: 0;
    transition: opacity 0.2s ease;
    border-radius: 0 0 6px 6px;
}

.gallery-item:hover .gallery-item-overlay { opacity: 1; }

/* --- Popular carousel --- */
.book-card-wrapper {
    max-width: 260px;
    max-height: 400px;
    min-width: 200px;
    width: 20vw;
    height: 60vh;
    cursor: pointer;
}

.book-card-overlay {
    background: linear-gradient(transparent, rgba(0,0,0,0.6));
    color: #fff;
    font-size: 0.7rem;
}

/* --- Uniform grid --- */
.book-grid-cover {
    height: 220px;
    object-fit: cover;
    display: block;
    transition: filter 0.2s ease;
}

.book-grid-item { cursor: pointer; }
.book-grid-item:hover .book-grid-cover { filter: brightness(0.75); }

.book-grid-overlay {
    background: linear-gradient(transparent, rgba(0,0,0,0.6));
    color: #fff;
    font-size: 0.75rem;
}

/* --- Search bar --- */
.explore-search-wrap { max-width: 100%; }

/* --- Modal --- */
.book-modal-content { border-radius: 10px; }

.book-modal-cover-wrap {
    background: #ccc;
    min-height: 300px;
}

.book-modal-cover {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.book-modal-details {
    background-color: var(--base-theme);
    color: var(--primary);
}

.book-modal-title { font-size: 1rem; font-weight: 700; }
.book-modal-category { opacity: 0.55; }

.book-modal-desc {
    overflow-y: auto;
    max-height: 90px;
    opacity: 0.85;
}

.book-modal-btn,
.book-modal-save {
    background-color: #d9d9d9;
    color: var(--primary);
    border: none;
    border-radius: 8px;
}

.book-modal-btn:hover,
.book-modal-save:hover { background-color: #c4c4c4; }

.book-modal-save {
    font-size: 1.1rem;
    padding: 0.375rem 0.65rem;
}

</style>


<!-- ================================================================
     JS
================================================================= -->
<script>
(function () {

    // --- Modal: populate from clicked element's data-* ---
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

    // --- Category chip toggle ---
    document.querySelectorAll('.chip-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.chip-btn').forEach(function (b) {
                b.classList.remove('chip-active');
            });
            this.classList.add('chip-active');
            // TODO: window.location.href = '/books?category=' + this.dataset.category;
        });
    });

    // --- Client-side search (placeholder only — replace with server-side) ---
    document.getElementById('bookSearch').addEventListener('input', function () {
        var q = this.value.toLowerCase();

        // Masonry items
        document.querySelectorAll('.gallery-item').forEach(function (item) {
            var match = (item.dataset.title    || '').toLowerCase().includes(q) ||
                        (item.dataset.author   || '').toLowerCase().includes(q) ||
                        (item.dataset.category || '').toLowerCase().includes(q);
            item.style.display = match ? '' : 'none';
        });

        // Grid items — hide the col wrapper, not just the inner div
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