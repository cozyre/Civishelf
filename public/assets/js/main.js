/**
 * main.js — Civishelf
 * Handles: book modal population, save toggle, borrow/read action
 */

(function () {
    'use strict';

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    function isLoggedIn() {
        // CIVISHELF_USER is set in header.php as a JS global (1 or 0)
        return window.CIVISHELF_USER === 1;
    }

    function openLoginModal() {
        var el = document.getElementById('loginModal');
        if (el) bootstrap.Modal.getOrCreateInstance(el).show();
    }

    // ----------------------------------------------------------------
    // Book modal — populate from data-* attributes on the trigger
    // ----------------------------------------------------------------

    var bookModal = document.getElementById('bookModal');
    if (!bookModal) return; // modal not present on this page (e.g. news, contact)

    var currentBookId   = null;
    var currentIsOnline = false;

    bookModal.addEventListener('show.bs.modal', function (e) {
        var t = e.relatedTarget;
        if (!t) return;

        currentBookId   = t.dataset.id   || null;
        currentIsOnline = t.dataset.online === '1';

        // Basic fields
        document.getElementById('modalTitle').textContent       = t.dataset.title       || '';
        document.getElementById('modalAuthor').textContent      = t.dataset.author      || '';
        document.getElementById('modalCategory').textContent    = t.dataset.category    || '';
        document.getElementById('modalDescription').textContent = t.dataset.description || '';
        document.getElementById('modalPublished').textContent   = t.dataset.published   || '';
        document.getElementById('modalCopies').textContent      = t.dataset.copies      || '0';
        document.getElementById('modalCover').src               = t.dataset.cover       || '';
        document.getElementById('modalCover').alt               = t.dataset.title       || '';

        // Status label
        var status   = t.dataset.status || 'none';
        var statusEl = document.getElementById('modalStatus');
        var copies   = parseInt(t.dataset.copies, 10) || 0;

        if (status === 'borrowed') {
            statusEl.textContent = 'Borrowed — due ' + (t.dataset.due || '');
        } else if (status === 'online') {
            statusEl.textContent = 'Available to read online';
        } else {
            statusEl.textContent = copies > 0 ? 'Available' : 'No copies available';
        }

        // Action button: Preview
        var previewBtn = document.getElementById('modalPreviewBtn');
        previewBtn.onclick = function () {
            // TODO: wire to /books/preview/{id} when that view exists
            alert('Preview coming soon.');
        };

        // Action button: Borrow / Read
        var actionBtn = document.getElementById('modalActionBtn');
        actionBtn.disabled  = false;
        actionBtn.classList.remove('btn-disabled');

        if (status === 'borrowed') {
            // Already borrowed → offer Read
            actionBtn.textContent = 'Read';
            actionBtn.onclick = function () {
                if (!isLoggedIn()) { openLoginModal(); return; }
                if (currentIsOnline) {
                    window.location.href = BASE_URL + '/books/read/' + currentBookId;
                } else {
                    window.location.href = BASE_URL + '/books/offline';
                }
            };
        } else if (status === 'online') {
            // Open-access online book
            actionBtn.textContent = 'Read';
            actionBtn.onclick = function () {
                if (!isLoggedIn()) { openLoginModal(); return; }
                window.location.href = BASE_URL + '/books/read/' + currentBookId;
            };
        } else {
            // Default: Borrow
            actionBtn.textContent = 'Borrow';
            if (copies === 0) {
                actionBtn.disabled = true;
                actionBtn.textContent = 'Unavailable';
            } else {
                actionBtn.onclick = function () {
                    if (!isLoggedIn()) { openLoginModal(); return; }
                    borrowBook(currentBookId, actionBtn);
                };
            }
        }

        // Save button — check current saved state via AJAX
        updateSaveIcon(false); // optimistic default while request is in flight
        if (isLoggedIn() && currentBookId) {
            $.post(BASE_URL + '/saved/status', { book_id: currentBookId }, function (res) {
                if (res.success) updateSaveIcon(res.saved);
            }, 'json');
        }
    });

    // ----------------------------------------------------------------
    // Save / unsave toggle
    // ----------------------------------------------------------------

    document.getElementById('modalSaveBtn').addEventListener('click', function () {
        if (!isLoggedIn()) { openLoginModal(); return; }
        if (!currentBookId) return;

        var isSaved = this.querySelector('i').classList.contains('bi-bookmark-fill');
        var endpoint = isSaved ? '/saved/unsave' : '/saved/save';

        $.post(BASE_URL + endpoint, { book_id: currentBookId }, function (res) {
            if (res.success) updateSaveIcon(res.saved);
        }, 'json');
    });

    function updateSaveIcon(saved) {
        var icon = document.querySelector('#modalSaveBtn i');
        if (!icon) return;
        icon.className = saved ? 'bi bi-bookmark-fill' : 'bi bi-bookmark-plus';
    }

    // ----------------------------------------------------------------
    // Borrow AJAX
    // ----------------------------------------------------------------

    function borrowBook(bookId, btn) {
        btn.disabled = true;
        btn.textContent = 'Requesting…';

        $.post(BASE_URL + '/borrow/request', { book_id: bookId }, function (res) {
            if (res.success) {
                btn.textContent = 'Requested!';
                // Optimistically update status label
                document.getElementById('modalStatus').textContent = 'Borrow request sent — pending approval';
            } else {
                btn.disabled = false;
                btn.textContent = 'Borrow';
                alert(res.message || 'Could not submit request. Please try again.');
            }
        }, 'json').fail(function () {
            btn.disabled = false;
            btn.textContent = 'Borrow';
            alert('Network error. Please try again.');
        });
    }

    // ----------------------------------------------------------------
    // Auto-reopen login modal if login failed (flag set by UserController)
    // ----------------------------------------------------------------
    // This logic is already in footer.php inline — leave it there.
    // main.js runs before Bootstrap is fully ready on some pages, so
    // the footer inline block (DOMContentLoaded) is safer for that case.

})();