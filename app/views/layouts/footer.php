<footer class="primary p-2">
    <div class="container pt-2 d-flex align-items-center justify-content-evenly">
        <a href=".">Privacy Policy</a>
        <a href=".">Terms of Service</a>
        <a href="<?= BASE_URL ?>/contact" class="">Contact Us</a>
        <?php if (isset($_SESSION['user_id']) || isset($_SESSION['admin_id'])): ?>
            <a href="/user/logout" class="">Logout</a>
        <?php else: ?>
            <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" class="">Login</a>
        <?php endif; ?>
    </div>
    <div class="row text-center mt-5 mb-2">
        <p><i>Your campus library, Always Open. </i>| &copy; 2026 Civishelf Technologies, Inc. All rights reserved</p>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Custom JS — loaded last so Bootstrap + jQuery are available -->
<script src="<?= BASE_URL ?>/assets/js/main.js"></script>

<script>
// Auto-reopen login modal if login failed
<?php if (isset($_SESSION['login_failed'])): ?>
document.addEventListener('DOMContentLoaded', function () {
    var modalEl = document.getElementById('loginModal');
    if (modalEl) { new bootstrap.Modal(modalEl).show(); }
});
<?php unset($_SESSION['login_failed']); ?>
<?php endif; ?>

// Toggle password visibility (login modal + register page)
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.toggle-pw').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input  = this.closest('.input-group').querySelector('input');
            var icon   = this.querySelector('i');
            var hidden = input.type === 'password';
            input.type     = hidden ? 'text' : 'password';
            icon.className = hidden ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    });
});
</script>
</body>
</html>