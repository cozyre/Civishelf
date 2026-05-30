<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Access - Civishelf</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --bg-dark: #1a1a1a;
            --bg-card: #242424;
            --border-dark: #333;
            --text-light: #ececec;
            --text-muted: #888;
            --text-subtle: #666;
            --danger: #C30D00;
            --danger-hover: #FF401F;
            --status-bg: #2a2a2a;
        }

        body { 
            background: var(--bg-dark); 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            min-height: 100vh; 
            margin: 0; 
            font-family: 'Times New Roman', serif;
        }

        .login-card { 
            background: var(--bg-card); 
            border: 1px solid var(--border-dark); 
            border-radius: 8px; 
            padding: 2.5rem; 
            width: 100%; 
            max-width: 400px;
        }

        .login-logo { 
            font-family: 'Times New Roman', serif; 
            font-size: 1.8rem;
            color: var(--text-light);
            letter-spacing: 0.05em;
        }

        .login-badge { 
            font-size: 0.65rem; 
            letter-spacing: 0.15em; 
            text-transform: uppercase; 
            color: var(--danger); 
            border: 1px solid var(--danger); 
            padding: 0.15rem 0.5rem; 
            border-radius: 3px; 
            vertical-align: middle;
        }

        .divider { 
            border-color: var(--border-dark);
        }

        .back-link { 
            color: var(--text-muted); 
            font-size: 0.8rem; 
            text-decoration: none;
        }

        .back-link:hover { 
            color: var(--text-light);
        }

        .btn-request {
            background: var(--danger);
            color: #fff;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            letter-spacing: 0.04em;
            width: 100%;
        }

        .btn-request:hover { 
            background: var(--danger-hover);
            color: #fff;
        }

        .status-pill {
            display: inline-block;
            font-size: 0.72rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-family: monospace;
            border: 1px solid;
        }

        .status-none { 
            background: var(--status-bg); color: var(--text-muted); border-color: var(--border-dark);
        }

        .status-pending { 
            background: #3a2e00; color: #f59e0b; border-color: #78450a;
        }

        .status-approved { 
            background: #0a2e1a; color: #34d399; border-color: #065f46;
        }

        .status-rejected { 
            background: #2e0a0a; color: #f87171; border-color: #7f1d1d;
        }

        .status-label { 
            font-size: 0.78rem; color: var(--text-subtle); margin-bottom: 0.4rem;
        }
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

        <?php if (!isset($_SESSION['user_id'])): ?>
            <!-- Not logged in at all -->
            <div class="alert alert-secondary py-2 small text-center">
                You need to <a href="<?= BASE_URL ?>/user/register" class="text-warning">create an account</a> 
                before requesting admin access.
            </div>

        <?php elseif (isset($_SESSION['admin_id'])): ?>
            <!-- Already an admin — shouldn't normally land here -->
            <div class="alert alert-success py-2 small text-center">
                You already have admin access. <a href="<?= BASE_URL ?>/administrator" class="text-success">Go to panel</a>
            </div>

        <?php else: ?>
            <!-- Logged-in regular user — show request button + status -->
            <p class="text-secondary small mb-3">
                Logged in as <strong class="text-light"><?= htmlspecialchars($_SESSION['user_name']) ?></strong>.<br>
                Submit a request and an existing admin will review it.
            </p>

            <?php
                $status = $requestStatus['status'] ?? null;
                $canRequest = ($status === null || $status === 'rejected');
            ?>

            <?php if ($canRequest): ?>
                <a href="<?= BASE_URL ?>/administrator/promoteAdmin" class="btn btn-request mb-3">
                    <i class="bi bi-shield-plus me-1"></i> Request Admin Access
                </a>
            <?php else: ?>
                <button class="btn btn-request mb-3" disabled style="opacity:0.4;">
                    <i class="bi bi-shield-plus me-1"></i> Request Admin Access
                </button>
            <?php endif; ?>

            <div class="mt-2">
                <div class="status-label">Request status</div>
                <?php if ($status === null): ?>
                    <span class="status-pill status-none"><i class="bi bi-dash me-1"></i>None</span>
                <?php elseif ($status === 'pending'): ?>
                    <span class="status-pill status-pending"><i class="bi bi-hourglass-split me-1"></i>Pending review</span>
                <?php elseif ($status === 'approved'): ?>
                    <span class="status-pill status-approved"><i class="bi bi-check-circle me-1"></i>Approved</span>
                <?php elseif ($status === 'rejected'): ?>
                    <span class="status-pill status-rejected"><i class="bi bi-x-circle me-1"></i>Last request denied — you may reapply</span>
                <?php endif; ?>
            </div>

        <?php endif; ?>

        <hr class="divider mt-4">
        <a href="<?= BASE_URL ?>/" class="back-link"><i class="bi bi-arrow-left me-1"></i>Back to public site</a>
    </div>
</body>
</html>