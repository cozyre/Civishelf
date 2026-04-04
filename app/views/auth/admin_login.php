<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Civishelf</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body { background: #1a1a1a; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; font-family: 'Times New Roman', serif; }
        .login-card { background: #242424; border: 1px solid #333; border-radius: 8px; padding: 2.5rem; width: 100%; max-width: 400px; }
        .login-logo { font-family: 'Times New Roman', serif; font-size: 1.8rem; color: #ececec; letter-spacing: 0.05em; }
        .login-badge { font-size: 0.65rem; letter-spacing: 0.15em; text-transform: uppercase; color: #C30D00; border: 1px solid #C30D00; padding: 0.15rem 0.5rem; border-radius: 3px; vertical-align: middle; }
        .form-control { background: #1a1a1a; border: 1px solid #444; color: #ececec; border-radius: 5px; }
        .form-control:focus { background: #1a1a1a; border-color: #C30D00; color: #ececec; box-shadow: none; }
        .form-label { color: #aaa; font-size: 0.85rem; }
        .btn-login { background: #C30D00; color: #fff; border: none; border-radius: 5px; font-weight: 600; letter-spacing: 0.04em; }
        .btn-login:hover { background: #FF401F; color: #fff; }
        .divider { border-color: #333; }
        .back-link { color: #888; font-size: 0.8rem; text-decoration: none; }
        .back-link:hover { color: #ececec; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="mb-4">
            <div class="login-logo mb-1">
                Civishelf <span class="login-badge">Admin</span>
            </div>
            <p class="text-secondary small mb-0">Restricted access. Authorised personnel only.</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger py-2 small">
                <?php foreach ($errors as $e): ?>
                    <div><?= htmlspecialchars($e) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/admin/login" method="POST">
            <div class="mb-3">
                <label class="form-label" for="adminEmail">Email</label>
                <input type="email" id="adminEmail" name="email" class="form-control"
                       value="<?= htmlspecialchars($email ?? '') ?>" autocomplete="email" required>
            </div>
            <div class="mb-4">
                <label class="form-label" for="adminPassword">Password</label>
                <div class="input-group">
                    <input type="password" id="adminPassword" name="password"
                           class="form-control" autocomplete="current-password" required>
                    <button class="btn btn-outline-secondary toggle-pw" type="button" tabindex="-1">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn btn-login w-100 mb-3">Sign In</button>
        </form>

        <hr class="divider">
        <a href="<?= BASE_URL ?>/" class="back-link"><i class="bi bi-arrow-left me-1"></i>Back to public site</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.querySelectorAll('.toggle-pw').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input  = this.closest('.input-group').querySelector('input');
            var icon   = this.querySelector('i');
            input.type     = input.type === 'password' ? 'text' : 'password';
            icon.className = input.type === 'text' ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    });
    </script>
</body>
</html>