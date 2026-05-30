<?php include __DIR__ . '/../layouts/header.php'; 
$savedBooks = $savedBooks??[];
$borrowedBooks = $borrowedBooks??[];
?>

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

                <div class="ledger-cover-wrap flex-shrink-0">
                    <img src="<?= $cover ?>"
                         alt="<?= htmlspecialchars($book['book_title']) ?>"
                         class="ledger-cover">
                </div>

                <div class="flex-grow-1 overflow-hidden">
                    <div class="ledger-title text-truncate"><?= htmlspecialchars($book['book_title']) ?></div>
                    <div class="ledger-meta text-truncate">
                        <?= htmlspecialchars($book['author_name'] ?? '—') ?>
                        <span class="mx-1 opacity-50">·</span>
                        <?= htmlspecialchars($book['category_name'] ?? '—') ?>
                    </div>
                </div>

                <div class="ledger-field d-none d-md-flex flex-column align-items-end flex-shrink-0">
                    <span class="ledger-field-label">Saved</span>
                    <span class="ledger-field-value"><?= date('d M Y', strtotime($book['saved_at'])) ?></span>
                </div>

                <div class="ledger-field d-none d-md-flex flex-column align-items-end flex-shrink-0">
                    <span class="ledger-field-label">Available</span>
                    <span class="ledger-field-value <?= $book['available_copies'] > 0 ? 'copies-ok' : 'copies-none' ?>">
                        <?= (int)$book['available_copies'] ?> cop.
                    </span>
                </div>

                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <!-- View opens the book modal -->
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
                            data-online="<?= !empty($book['is_online']) ? '1' : '0' ?>"
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

                <div class="ledger-cover-wrap flex-shrink-0">
                    <img src="<?= $cover ?>"
                         alt="<?= htmlspecialchars($book['book_title']) ?>"
                         class="ledger-cover">
                </div>

                <div class="flex-grow-1 overflow-hidden">
                    <div class="ledger-title text-truncate"><?= htmlspecialchars($book['book_title']) ?></div>
                    <div class="ledger-meta text-truncate">
                        <?= htmlspecialchars($book['author_name'] ?? '—') ?>
                        <span class="mx-1 opacity-50">·</span>
                        <?= htmlspecialchars($book['category_name'] ?? '—') ?>
                    </div>
                </div>

                <div class="ledger-field d-none d-md-flex flex-column align-items-end flex-shrink-0">
                    <span class="ledger-field-label">Borrowed</span>
                    <span class="ledger-field-value"><?= date('d M Y', strtotime($book['borrow_date'])) ?></span>
                </div>

                <div class="ledger-field d-flex flex-column align-items-end flex-shrink-0">
                    <span class="ledger-field-label">Due</span>
                    <span class="ledger-field-value d-flex align-items-center gap-1">
                        <span class="d-none d-sm-inline"><?= date('d M Y', $due) ?></span>
                        <span class="due-badge <?= $urgency ?>">
                            <?= $daysLeft > 0 ? $daysLeft . 'd' : 'OD' ?>
                        </span>
                    </span>
                </div>

                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <!-- View opens modal in "borrowed" state -->
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
                            data-online="<?= !empty($book['is_online']) ? '1' : '0' ?>"
                            data-due="<?= htmlspecialchars($book['due_date']) ?>">
                        View
                    </button>
                    <!-- Return stays on this page only, not in the modal -->
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
     JS — page-specific only (tabs, unsave, return)
     Modal logic lives in main.js
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
            var self = this;
            var requestId = this.dataset.requestId;
            if (!confirm('Return this book? This cannot be undone.')) return;

            self.disabled = true;
            self.textContent = 'Returning…';

            $.post(BASE_URL + '/borrow/returnBook', { request_id: requestId }, function (res) {
                if (res.success) {
                    var row = self.closest('.ledger-row');
                    row.style.transition = 'opacity 0.3s';
                    row.style.opacity    = '0';
                    setTimeout(function () {
                        row.remove();
                        // Decrement the borrowed count pill
                        var pills = document.querySelectorAll('.mybooks-count-pill--accent .count-num');
                        if (pills.length) pills[0].textContent = Math.max(0, parseInt(pills[0].textContent) - 1);
                    }, 300);
                } else {
                    self.disabled    = false;
                    self.textContent = 'Return';
                    alert(res.message || 'Return failed. Please try again.');
                }
            }, 'json').fail(function () {
                self.disabled    = false;
                self.textContent = 'Return';
                alert('Network error. Please try again.');
            });
        });
    });

})();
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
