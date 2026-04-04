<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' — Civishelf Admin' : 'Civishelf Admin' ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script>const BASE_URL = "<?= BASE_URL ?>";</script>
    <style>
        /* ---- Base ---- */
        :root {
            --adm-bg:       #f4f4f4;
            --adm-sidebar:  #1f1f1f;
            --adm-sidebar2: #2a2a2a;
            --adm-accent:   #C30D00;
            --adm-accent2:  #FF401F;
            --adm-text:     #1f1f1f;
            --adm-muted:    #6b7280;
            --adm-border:   #e5e7eb;
            --adm-white:    #ffffff;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Times New Roman', serif; background: var(--adm-bg); color: var(--adm-text); display: flex; min-height: 100vh; }

        /* ---- Sidebar ---- */
        #adminSidebar {
            width: 220px;
            min-height: 100vh;
            background: var(--adm-sidebar);
            color: #ececec;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            transition: transform 0.25s ease;
        }
        .sidebar-brand {
            padding: 1.25rem 1rem 1rem;
            border-bottom: 1px solid #333;
        }
        .sidebar-brand-name {
            font-size: 1.2rem;
            font-weight: 700;
            letter-spacing: 0.03em;
            color: #ececec;
        }
        .sidebar-brand-badge {
            font-size: 0.58rem;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--adm-accent);
            border: 1px solid var(--adm-accent);
            padding: 0.1rem 0.4rem;
            border-radius: 3px;
        }
        .sidebar-nav { padding: 0.75rem 0; flex: 1; }
        .sidebar-section-label {
            font-size: 0.58rem;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #555;
            padding: 0.75rem 1rem 0.25rem;
            font-family: monospace;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.55rem 1rem;
            color: #aaa;
            text-decoration: none;
            font-size: 0.875rem;
            transition: background 0.15s, color 0.15s;
            border-left: 3px solid transparent;
        }
        .sidebar-link:hover { background: var(--adm-sidebar2); color: #ececec; }
        .sidebar-link.active { background: var(--adm-sidebar2); color: #ececec; border-left-color: var(--adm-accent); }
        .sidebar-link i { font-size: 1rem; flex-shrink: 0; }
        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid #333;
            font-size: 0.78rem;
            color: #555;
        }
        .sidebar-footer a { color: #888; text-decoration: none; font-size: 0.78rem; }
        .sidebar-footer a:hover { color: var(--adm-accent2); }
        .badge-pending {
            font-size: 0.6rem;
            background: var(--adm-accent);
            color: #fff;
            border-radius: 20px;
            padding: 0.1rem 0.45rem;
            margin-left: auto;
        }

        /* ---- Main content area ---- */
        #adminMain {
            margin-left: 220px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .admin-topbar {
            background: var(--adm-white);
            border-bottom: 1px solid var(--adm-border);
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .admin-topbar-title { font-size: 1.1rem; font-weight: 700; }
        .admin-topbar-user { font-size: 0.82rem; color: var(--adm-muted); }
        .admin-body { padding: 1.5rem; flex: 1; }

        /* ---- Stat cards ---- */
        .stat-card {
            background: var(--adm-white);
            border: 1px solid var(--adm-border);
            border-radius: 8px;
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .stat-card-icon {
            width: 48px; height: 48px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }
        .stat-card-num { font-size: 1.8rem; font-weight: 700; line-height: 1; }
        .stat-card-label { font-size: 0.72rem; letter-spacing: 0.08em; text-transform: uppercase; color: var(--adm-muted); margin-top: 0.2rem; }

        /* ---- Tables ---- */
        .admin-table { background: var(--adm-white); border: 1px solid var(--adm-border); border-radius: 8px; overflow: hidden; }
        .admin-table table { margin: 0; font-size: 0.875rem; }
        .admin-table thead th { background: #f9fafb; font-size: 0.7rem; letter-spacing: 0.1em; text-transform: uppercase; color: var(--adm-muted); border-bottom: 1px solid var(--adm-border); padding: 0.75rem 1rem; font-weight: 600; }
        .admin-table tbody td { padding: 0.7rem 1rem; vertical-align: middle; border-bottom: 1px solid var(--adm-border); }
        .admin-table tbody tr:last-child td { border-bottom: none; }
        .admin-table tbody tr:hover { background: #fafafa; }

        /* ---- Section header ---- */
        .section-bar {
            background: var(--adm-white);
            border: 1px solid var(--adm-border);
            border-radius: 8px;
            padding: 1rem 1.25rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        .section-bar-title { font-size: 1rem; font-weight: 700; margin: 0; }

        /* ---- Buttons ---- */
        .btn-adm-primary { background: var(--adm-text); color: #fff; border: none; font-size: 0.8rem; border-radius: 5px; }
        .btn-adm-primary:hover { background: #333; color: #fff; }
        .btn-adm-danger { background: var(--adm-accent); color: #fff; border: none; font-size: 0.8rem; border-radius: 5px; }
        .btn-adm-danger:hover { background: var(--adm-accent2); color: #fff; }
        .btn-adm-ghost { background: transparent; border: 1px solid var(--adm-border); color: var(--adm-text); font-size: 0.8rem; border-radius: 5px; }
        .btn-adm-ghost:hover { background: var(--adm-bg); }

        /* ---- Status badges ---- */
        .badge-active   { background: #dcfce7; color: #166534; font-size: 0.68rem; padding: 0.2rem 0.6rem; border-radius: 20px; }
        .badge-banned   { background: #fef2f2; color: #991b1b; font-size: 0.68rem; padding: 0.2rem 0.6rem; border-radius: 20px; }
        .badge-pending2 { background: #fef9c3; color: #854d0e; font-size: 0.68rem; padding: 0.2rem 0.6rem; border-radius: 20px; }
        .badge-approved { background: #dcfce7; color: #166534; font-size: 0.68rem; padding: 0.2rem 0.6rem; border-radius: 20px; }
        .badge-rejected { background: #fef2f2; color: #991b1b; font-size: 0.68rem; padding: 0.2rem 0.6rem; border-radius: 20px; }
        .badge-returned { background: #e0f2fe; color: #075985; font-size: 0.68rem; padding: 0.2rem 0.6rem; border-radius: 20px; }
        .badge-overdue  { background: #fef2f2; color: #991b1b; font-size: 0.68rem; padding: 0.2rem 0.6rem; border-radius: 20px; border: 1px solid #fca5a5; }

        /* ---- Book cover thumb ---- */
        .book-thumb { width: 36px; height: 50px; object-fit: cover; border-radius: 3px; display: block; }

        /* ---- Responsive sidebar toggle ---- */
        @media (max-width: 768px) {
            #adminSidebar { transform: translateX(-220px); }
            #adminSidebar.open { transform: translateX(0); }
            #adminMain { margin-left: 0; }
        }
    </style>
</head>
<body>

<!-- ================================================================
     SIDEBAR
================================================================= -->
<aside id="adminSidebar">
    <div class="sidebar-brand">
        <div class="sidebar-brand-name">Civishelf</div>
        <span class="sidebar-brand-badge">Admin Panel</span>
    </div>

    <nav class="sidebar-nav">
        <div class="sidebar-section-label">Overview</div>
        <a href="<?= BASE_URL ?>/administrator"
           class="sidebar-link <?= ($activeNav ?? '') === 'dashboard' ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <div class="sidebar-section-label">Manage</div>
        <a href="<?= BASE_URL ?>/administrator/users"
           class="sidebar-link <?= ($activeNav ?? '') === 'users' ? 'active' : '' ?>">
            <i class="bi bi-people"></i> Users
        </a>
        <a href="<?= BASE_URL ?>/administrator/books"
           class="sidebar-link <?= ($activeNav ?? '') === 'books' ? 'active' : '' ?>">
            <i class="bi bi-journals"></i> Books
        </a>
        <a href="<?= BASE_URL ?>/administrator/borrows"
           class="sidebar-link <?= ($activeNav ?? '') === 'borrows' ? 'active' : '' ?>">
            <i class="bi bi-arrow-left-right"></i> Borrows
            <?php if (!empty($pendingCount) && $pendingCount > 0): ?>
                <span class="badge-pending"><?= (int)$pendingCount ?></span>
            <?php endif; ?>
        </a>
        <a href="<?= BASE_URL ?>/administrator/news"
           class="sidebar-link <?= ($activeNav ?? '') === 'news' ? 'active' : '' ?>">
            <i class="bi bi-newspaper"></i> News
        </a>

        <div class="sidebar-section-label">Site</div>
        <a href="<?= BASE_URL ?>/" class="sidebar-link" target="_blank">
            <i class="bi bi-box-arrow-up-right"></i> View Site
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="mb-1">Logged in as <strong><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></strong></div>
        <a href="<?= BASE_URL ?>/admin/logout"><i class="bi bi-box-arrow-right me-1"></i>Logout</a>
    </div>
</aside>

<!-- ================================================================
     MAIN AREA
================================================================= -->
<div id="adminMain">

    <!-- Topbar -->
    <div class="admin-topbar">
        <div class="d-flex align-items-center gap-3">
            <!-- Mobile toggle -->
            <button class="btn btn-sm btn-adm-ghost d-md-none" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            <span class="admin-topbar-title"><?= htmlspecialchars($pageTitle ?? 'Admin') ?></span>
        </div>
        <div class="admin-topbar-user">
            <i class="bi bi-shield-lock me-1"></i><?= htmlspecialchars($_SESSION['admin_name'] ?? '') ?>
        </div>
    </div>

    <!-- Flash messages -->
    <?php if (isset($_SESSION['flash'])): ?>
    <div class="px-4 pt-3">
        <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type']) ?> alert-dismissible fade show py-2 mb-0" role="alert">
            <?= htmlspecialchars($_SESSION['flash']['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <!-- Page content injected here -->
    <div class="admin-body">
        <?= $content ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
document.getElementById('sidebarToggle')?.addEventListener('click', function () {
    document.getElementById('adminSidebar').classList.toggle('open');
});
</script>
</body>
</html>