<?php include __DIR__ . '/../layouts/header.php'; ?>

<main class="mb-5 pb-5">

    <!-- =====================================================
         PAGE HEADER
    ====================================================== -->
    <section class="mybooks-header">
        <div class="container-fluid px-4 py-4">

            <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">

                <div>
                    <span class="mybooks-eyebrow">Your Library Card</span>
                    <h1 class="mybooks-title">My Books</h1>
                </div>

                <div class="d-flex gap-2 align-items-center">
                    <div class="mybooks-count-pill">
                        <span class="count-num"><?= count($savedBooks) ?></span>
                        <span class="count-label">Saved</span>
                    </div>
                    <div class="mybooks-count-pill mybooks-count-pill--accent">
                        <span class="count-num"><?= count($borrowedBooks) ?></span>
                        <span class="count-label">Borrowed</span>
                    </div>
                </div>

            </div>

            <!-- Tab switcher -->
            <div class="mybooks-tabs mt-4">
                <button class="mybooks-tab active" data-tab="saved">
                    <i class="bi bi-bookmark-fill me-2"></i>Saved Books
                </button>
                <button class="mybooks-tab" data-tab="borrowed">
                    <i class="bi bi-book-half me-2"></i>Currently Borrowed
                </button>
            </div>

        </div>
    </section>


    <!-- =====================================================
         SAVED BOOKS
    ====================================================== -->
    <section class="container-fluid px-4 mt-4" id="tab-saved">

        <?php if (empty($savedBooks)): ?>
        <div class="text-center py-5">
            <i class="bi bi-bookmark fs-1 d-block mb-3 opacity-25"></i>
            <p class="opacity-50 mb-0">No saved books yet.<br>
                <a href="<?= BASE_URL ?>/books" class="mybooks-link">Browse the collection</a>
            </p>
        </div>

        <?php else: ?>
        <div class="book-ledger">

            <?php foreach ($savedBooks as $book): ?>
            <?php $cover = BASE_URL . '/assets/images/covers/' . ($book['cover_image'] ?? 'book-placeholder.jpg'); ?>

            <div class="ledger-row position-relative d-flex align-items-center gap-3 px-3 py-2">

                <div class="ledger-spine"></div>

                <!-- Cover -->
                <div class="ledger-cover-wrap flex-shrink-0">
                    <img src="<?= $cover ?>"
                         alt="<?= htmlspecialchars($book['book_title']) ?>"
                         class="ledger-cover">
                </div>

                <!-- Title + meta -->
                <div class="flex-grow-1 overflow-hidden">
                    <div class="ledger-title text-truncate"><?= htmlspecialchars($book['book_title']) ?></div>
                    <div class="ledger-meta text-truncate">
                        <?= htmlspecialchars($book['author_name'] ?? '—') ?>
                        <span class="mx-1 opacity-50">·</span>
                        <?= htmlspecialchars($book['category_name'] ?? '—') ?>
                    </div>
                </div>

                <!-- Saved date — md and up -->
                <div class="ledger-field d-none d-md-flex flex-column align-items-end flex-shrink-0">
                    <span class="ledger-field-label">Saved</span>
                    <span class="ledger-field-value"><?= date('d M Y', strtotime($book['saved_at'])) ?></span>
                </div>

                <!-- Available copies — md and up -->
                <div class="ledger-field d-none d-md-flex flex-column align-items-end flex-shrink-0">
                    <span class="ledger-field-label">Available</span>
                    <span class="ledger-field-value <?= $book['available_copies'] > 0 ? 'copies-ok' : 'copies-none' ?>">
                        <?= (int)$book['available_copies'] ?> cop.
                    </span>
                </div>

                <!-- Actions -->
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <button class="ledger-btn ledger-btn--ghost"
                            data-bs-toggle="modal"
                            data-bs-target="#bookModal"
                            data-id="<?= $book['book_id'] ?>"
                            data-title="<?= htmlspecialchars($book['book_title']) ?>"
                            data-author="<?= htmlspecialchars($book['author_name'] ?? '') ?>"
                            data-category="<?= htmlspecialchars($book['category_name'] ?? '') ?>"
                            data-description="<?= htmlspecialchars($book['description'] ?? '') ?>"
                            data-published="<?= htmlspecialchars($book['published_at'] ?? '') ?>"
                            data-copies="<?= (int)$book['available_copies'] ?>"
                            data-cover="<?= $cover ?>"
                            data-status="none"
                            data-due="">
                        View
                    </button>
                    <button class="ledger-btn ledger-btn--unsave"
                            data-book-id="<?= $book['book_id'] ?>"
                            title="Remove from saved">
                        <i class="bi bi-bookmark-x"></i>
                    </button>
                </div>

            </div>
            <?php endforeach; ?>

        </div>
        <?php endif; ?>

    </section>


    <!-- =====================================================
         CURRENTLY BORROWED
    ====================================================== -->
    <section class="container-fluid px-4 mt-4" id="tab-borrowed" style="display:none;">

        <?php if (empty($borrowedBooks)): ?>
        <div class="text-center py-5">
            <i class="bi bi-book fs-1 d-block mb-3 opacity-25"></i>
            <p class="opacity-50 mb-0">No active borrows.<br>
                <a href="<?= BASE_URL ?>/books" class="mybooks-link">Find something to read</a>
            </p>
        </div>

        <?php else: ?>
        <div class="book-ledger">

            <?php foreach ($borrowedBooks as $book): ?>
            <?php
                $cover    = BASE_URL . '/assets/images/covers/' . ($book['cover_image'] ?? 'book-placeholder.jpg');
                $due      = strtotime($book['due_date']);
                $daysLeft = (int) ceil(($due - time()) / 86400);
                $urgency  = $daysLeft <= 3 ? 'due-urgent' : ($daysLeft <= 7 ? 'due-soon' : 'due-ok');
            ?>

            <div class="ledger-row position-relative d-flex align-items-center gap-3 px-3 py-2">

                <div class="ledger-spine ledger-spine--accent"></div>

                <!-- Cover -->
                <div class="ledger-cover-wrap flex-shrink-0">
                    <img src="<?= $cover ?>"
                         alt="<?= htmlspecialchars($book['book_title']) ?>"
                         class="ledger-cover">
                </div>

                <!-- Title + meta -->
                <div class="flex-grow-1 overflow-hidden">
                    <div class="ledger-title text-truncate"><?= htmlspecialchars($book['book_title']) ?></div>
                    <div class="ledger-meta text-truncate">
                        <?= htmlspecialchars($book['author_name'] ?? '—') ?>
                        <span class="mx-1 opacity-50">·</span>
                        <?= htmlspecialchars($book['category_name'] ?? '—') ?>
                    </div>
                </div>

                <!-- Borrow date — md and up -->
                <div class="ledger-field d-none d-md-flex flex-column align-items-end flex-shrink-0">
                    <span class="ledger-field-label">Borrowed</span>
                    <span class="ledger-field-value"><?= date('d M Y', strtotime($book['borrow_date'])) ?></span>
                </div>

                <!-- Due date — always visible -->
                <div class="ledger-field d-flex flex-column align-items-end flex-shrink-0">
                    <span class="ledger-field-label">Due</span>
                    <span class="ledger-field-value d-flex align-items-center gap-1">
                        <span class="d-none d-sm-inline"><?= date('d M Y', $due) ?></span>
                        <span class="due-badge <?= $urgency ?>">
                            <?= $daysLeft > 0 ? $daysLeft . 'd' : 'OD' ?>
                        </span>
                    </span>
                </div>

                <!-- Actions -->
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <button class="ledger-btn ledger-btn--ghost"
                            data-bs-toggle="modal"
                            data-bs-target="#bookModal"
                            data-id="<?= $book['book_id'] ?>"
                            data-title="<?= htmlspecialchars($book['book_title']) ?>"
                            data-author="<?= htmlspecialchars($book['author_name'] ?? '') ?>"
                            data-category="<?= htmlspecialchars($book['category_name'] ?? '') ?>"
                            data-description="<?= htmlspecialchars($book['description'] ?? '') ?>"
                            data-published="<?= htmlspecialchars($book['published_at'] ?? '') ?>"
                            data-copies="<?= (int)$book['available_copies'] ?>"
                            data-cover="<?= $cover ?>"
                            data-status="borrowed"
                            data-due="<?= htmlspecialchars($book['due_date']) ?>">
                        View
                    </button>
                    <button class="ledger-btn ledger-btn--return"
                            data-request-id="<?= $book['request_id'] ?>"
                            data-book-id="<?= $book['book_id'] ?>">
                        Return
                    </button>
                </div>

            </div>
            <?php endforeach; ?>

        </div>
        <?php endif; ?>

    </section>

</main>


<!-- ================================================================
     BOOK DETAIL MODAL — mirrors books/index.php structure exactly
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

                    <div class="d-flex gap-2">
                        <button class="btn book-modal-btn flex-grow-1" id="modalActionBtn">Borrow</button>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>


<!-- ================================================================
     STYLES — only what Bootstrap can't handle
================================================================= -->
<style>
/* Header */
.mybooks-header {
    background: var(--primary);
    color: var(--base-theme);
    border-bottom: 3px solid var(--accent);
}

.mybooks-eyebrow {
    display: block;
    font-size: 0.68rem;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    opacity: 0.4;
    margin-bottom: 0.3rem;
    font-family: monospace;
}

.mybooks-title {
    font-family: var(--title-font);
    font-size: 2.2rem;
    line-height: 1;
    margin: 0;
}

/* Count pills */
.mybooks-count-pill {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 0.6rem 1.2rem;
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 4px;
    min-width: 70px;
}

.mybooks-count-pill--accent {
    border-color: var(--accent);
    background: rgba(195,13,0,0.08);
}

.count-num {
    font-family: var(--title-font);
    font-size: 1.8rem;
    line-height: 1;
    font-weight: 700;
}

.count-label {
    font-size: 0.62rem;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    opacity: 0.5;
    margin-top: 0.2rem;
}

/* Tabs */
.mybooks-tabs { border-bottom: 1px solid rgba(255,255,255,0.1); }

.mybooks-tab {
    background: none;
    border: none;
    color: var(--base-theme);
    padding: 0.65rem 1.4rem;
    font-size: 0.82rem;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    opacity: 0.45;
    cursor: pointer;
    border-bottom: 2px solid transparent;
    margin-bottom: -1px;
    transition: opacity 0.15s, border-color 0.15s;
}

.mybooks-tab:hover  { opacity: 0.75; }
.mybooks-tab.active { opacity: 1; border-bottom-color: var(--accent); }

/* Ledger container */
.book-ledger {
    border: 1px solid rgba(31,31,31,0.12);
    border-radius: 6px;
    overflow: hidden;
}

.ledger-row {
    background: #fff;
    border-bottom: 1px solid rgba(31,31,31,0.07);
    transition: background 0.15s;
}

.ledger-row:last-child { border-bottom: none; }
.ledger-row:hover      { background: #f7f5f2; }

/* Spine accent */
.ledger-spine {
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 3px;
    background: rgba(31,31,31,0.08);
}

.ledger-spine--accent { background: var(--accent); }

/* Cover thumbnail */
.ledger-cover-wrap {
    width: 38px;
    height: 54px;
    border-radius: 2px;
    overflow: hidden;
    box-shadow: 2px 2px 6px rgba(0,0,0,0.15);
}

.ledger-cover { width: 100%; height: 100%; object-fit: cover; display: block; }

/* Text */
.ledger-title {
    font-family: var(--title-font);
    font-size: 1rem;
    font-weight: 700;
    color: var(--primary);
}

.ledger-meta {
    font-size: 0.72rem;
    color: var(--primary);
    opacity: 0.5;
    margin-top: 0.15rem;
}

/* Field columns */
.ledger-field { min-width: 80px; }

.ledger-field-label {
    font-size: 0.6rem;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    opacity: 0.35;
    font-family: monospace;
}

.ledger-field-value {
    font-size: 0.78rem;
    font-family: monospace;
    color: var(--primary);
    font-weight: 600;
    margin-top: 0.1rem;
}

.copies-ok   { color: #1a7a3c; }
.copies-none { color: var(--accent); }

/* Due badges */
.due-badge {
    font-size: 0.6rem;
    padding: 0.15rem 0.5rem;
    border-radius: 20px;
    font-weight: 700;
    font-family: monospace;
    white-space: nowrap;
}

.due-ok     { background: #e8f5e9; color: #1a7a3c; }
.due-soon   { background: #fff3e0; color: #b45309; }
.due-urgent { background: #fdecea; color: var(--accent); }

/* Action buttons */
.ledger-btn {
    font-size: 0.72rem;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    padding: 0.35rem 0.85rem;
    border-radius: 3px;
    font-weight: 600;
    transition: background 0.15s;
    cursor: pointer;
    white-space: nowrap;
}

.ledger-btn--ghost {
    background: transparent;
    border: 1px solid rgba(31,31,31,0.2);
    color: var(--primary);
}

.ledger-btn--ghost:hover { background: rgba(31,31,31,0.06); }

.ledger-btn--unsave {
    background: transparent;
    border: 1px solid rgba(195,13,0,0.2);
    color: var(--accent);
    padding: 0.35rem 0.6rem;
    font-size: 0.9rem;
}

.ledger-btn--unsave:hover { background: rgba(195,13,0,0.08); }

.ledger-btn--return {
    background: var(--primary);
    color: var(--base-theme);
    border: none;
}

.ledger-btn--return:hover { background: var(--accent); }

/* Empty state link */
.mybooks-link { color: var(--accent); text-decoration: underline; }
</style>


<!-- ================================================================
     JS
================================================================= -->
<script>
(function () {

    // ---- Tab switching ----
    document.querySelectorAll('.mybooks-tab').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.mybooks-tab').forEach(function (b) { b.classList.remove('active'); });
            this.classList.add('active');
            var t = this.dataset.tab;
            document.getElementById('tab-saved').style.display    = t === 'saved'    ? '' : 'none';
            document.getElementById('tab-borrowed').style.display = t === 'borrowed' ? '' : 'none';
        });
    });

    // ---- Book modal — populate from data-* on View button ----
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
            statusEl.textContent  = 'Borrowed — due ' + (t.dataset.due || '');
            actionBtn.textContent = 'Return';
        } else {
            statusEl.textContent  = 'None';
            actionBtn.textContent = 'Borrow';
        }
    });

    // ---- Unsave AJAX ----
    document.querySelectorAll('.ledger-btn--unsave').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var bookId = this.dataset.bookId;
            var row    = this.closest('.ledger-row');

            $.post(BASE_URL + '/saved/unsave', { book_id: bookId }, function (res) {
                if (res.success) {
                    row.style.transition = 'opacity 0.3s';
                    row.style.opacity    = '0';
                    setTimeout(function () {
                        row.remove();
                        var countEl = document.querySelector('.mybooks-count-pill .count-num');
                        if (countEl) countEl.textContent = Math.max(0, parseInt(countEl.textContent) - 1);
                    }, 300);
                }
            }, 'json');
        });
    });

    // ---- Return stub ----
    document.querySelectorAll('.ledger-btn--return').forEach(function (btn) {
        btn.addEventListener('click', function () {
            // TODO: POST /borrow/return { request_id: this.dataset.requestId }
            alert('Return flow coming soon. Request ID: ' + this.dataset.requestId);
        });
    });

})();
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>