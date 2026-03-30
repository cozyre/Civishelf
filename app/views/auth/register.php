<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container py-4 mb-5 card primary-light">
    <a href="javascript:history.back()" class="contact-back-btn">
        <i class="bi bi-chevron-left" style="font-size: 1.5rem;"></i>
    </a>
    <div class="row align-items-center">
        <!-- Logo Section (Left) -->
        <div class="col-md-6 d-flex justify-content-center align-items-center mb-4 mb-md-0">
            <div class="text-center">
                <img src="<?= BASE_URL ?>/assets/images/logos/logo.png" alt="Civishelf Logo" style="max-width: 80%; height: auto;">
            </div>
        </div>

        <!-- Form Section (Right) -->
        <div class="col-md-6 col-lg-5">

            <h2 class="fw-bold mb-1">Create an Account</h2>
            <p class="mb-4">Join Civishelf and start borrowing books.</p>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger py-2">
                    <?php foreach ($errors as $error): ?>
                        <div class="small"><?= htmlspecialchars($error) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="/user/register" method="POST" novalidate>

                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" id="name" name="name" class="form-control"
                           value="<?= htmlspecialchars($name ?? '') ?>"
                           autocomplete="name" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control"
                           value="<?= htmlspecialchars($email ?? '') ?>"
                           autocomplete="email" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" id="password" name="password"
                               class="form-control" autocomplete="new-password"
                               minlength="8" required>
                        <button class="btn btn-outline-secondary toggle-pw" type="button" tabindex="-1">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <div class="form-text primary-light">Minimum 8 characters.</div>
                </div>

                <div class="mb-4">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password"
                           class="form-control" autocomplete="new-password" required>
                </div>

                <button type="submit" class="btn accent w-100 fw-semibold">Register</button>

            </form>

            <p class="text-center small mt-3 mb-0">
                Already have an account?
                <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal"><b>Log in here</b></a>
            </p>

        </div>
    </div>
</div>

<script>
document.querySelector('.toggle-pw').addEventListener('click', function () {
    var input  = document.getElementById('password');
    var icon   = this.querySelector('i');
    var hidden = input.type === 'password';
    input.type     = hidden ? 'text' : 'password';
    icon.className = hidden ? 'bi bi-eye-slash' : 'bi bi-eye';
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>