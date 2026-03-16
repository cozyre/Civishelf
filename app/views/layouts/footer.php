<footer class="primary fixed-bottom">
    <div class="container p-1 d-flex align-items-center justify-content-evenly">
        <!-- change these menus later -->
        <a href="/" class="">Home</a>
        <a href="/books" class="">Explore</a>
        <a href="/news" class="">News</a>
        <a href="/contact" class="">Contact Us</a>
        <?php if (isset($_SESSION['user_id']) || isset($_SESSION['admin_id'])): ?>
            <a href="/user/logout" class="">Logout</a>
        <?php else: ?>
            <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" class="">Login</a>
        <?php endif; ?>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Custom JS -->
<script src="/assets/js/main.js"></script>

<script>
// Auto-reopen login modal if login failed (controller sets session flag)
<?php if (isset($_SESSION['login_failed'])): ?>
document.addEventListener('DOMContentLoaded', function () {
    var modalEl = document.getElementById('loginModal');
    if (modalEl) { new bootstrap.Modal(modalEl).show(); }
});
<?php unset($_SESSION['login_failed']); ?>
<?php endif; ?>

// Toggle password visibility in login modal
document.addEventListener('DOMContentLoaded', function () {
    var toggleBtn = document.querySelector('.toggle-pw');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            var input  = document.getElementById('loginPassword');
            var icon   = this.querySelector('i');
            var hidden = input.type === 'password';
            input.type     = hidden ? 'text' : 'password';
            icon.className = hidden ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    }
});
</script>
</body>
</html>