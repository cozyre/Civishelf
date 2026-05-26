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
        /* ---- Variables ---- */
        :root {
            --adm-bg:       #f4f4f4;
            --adm-white:    #ffffff;
            --adm-sidebar:  #1f1f1f;
            --adm-sidebar-hover: #2a2a2a;
            --adm-text:     #1f1f1f;
            --adm-muted:    #6b7280;
            --adm-border:   #e5e7eb;
            --adm-accent:   #C30D00;
            --adm-accent-hover: #FF401F;
            --text-white: #ececec;
            --sidebar-w:    220px;
            
            /* Spacing scale */
            --spacing-xs:   0.25rem;
            --spacing-sm:   0.5rem;
            --spacing-md:   1rem;
            --spacing-lg:   1.25rem;
            --spacing-xl:   1.5rem;
            
            /* Typography */
            --font-xs:      0.6rem;
            --font-sm:      0.8rem;
            --font-base:    1rem;
            --font-lg:      1.1rem;
            --font-xl:      1.2rem;
            --font-2xl:     1.8rem;
        }

        * { box-sizing: border-box; }
        
        body {
            margin: 0;
            font-family: 'Times New Roman', serif;
            background: var(--adm-bg);
            color: var(--adm-text);
            display: flex;
            min-height: 100vh;
        }

        /* ---- Sidebar ---- */
        #adminSidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--adm-sidebar);
            color: var(--text-white);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 200;
            transition: transform 0.25s ease;
        }

        .sidebar-brand {
            padding: var(--spacing-lg) var(--spacing-md) var(--spacing-md);
            border-bottom: 1px solid #333;
        }
        
        .sidebar-brand-name {
            font-size: 1.2rem;
            font-weight: 700;
            letter-spacing: 0.03em;
            color: var(--text-white);
        }
        
        .sidebar-brand-badge {
            font-size: var(--font-xs);
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--adm-accent);
            border: 1px solid var(--adm-accent);
            padding: var(--spacing-xs) var(--spacing-sm);
            border-radius: 3px;
        }

        .sidebar-nav {
            padding: var(--spacing-md) 0;
            flex: 1;
            overflow-y: auto;
        }

        .sidebar-section-label {
            font-size: var(--font-xs);
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #555;
            padding: var(--spacing-md) var(--spacing-md) var(--spacing-sm);
            font-family: monospace;
        }
        
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            padding: 0.55rem var(--spacing-md);
            color: #aaa;
            text-decoration: none;
            font-size: var(--font-base);
            transition: background 0.15s, color 0.15s;
            border-left: 3px solid transparent;
        }
        .sidebar-link:hover  { background: var(--adm-sidebar2); color: var(--text-white); }
        .sidebar-link.active { background: var(--adm-sidebar2); color: var(--text-white); border-left-color: var(--adm-accent); }
        .sidebar-link i { font-size: 1rem; flex-shrink: 0; }

        .sidebar-footer {
            padding: var(--spacing-md);
            border-top: 1px solid #333;
            font-size: var(--font-sm);
            color: #555;
        }
        
        .sidebar-footer a {
            color: #888;
            text-decoration: none;
            font-size: var(--font-sm);
        }
        
        .sidebar-footer a:hover { color: var(--adm-accent-hover); }

        .badge-pending {
            font-size: var(--font-xs);
            background: var(--adm-accent);
            color: var(--adm-white);
            border-radius: 20px;
            padding: var(--spacing-xs) var(--spacing-sm);
            margin-left: auto;
        }

        /* ---- Sidebar overlay (mobile) ---- */
        #sidebarOverlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            z-index: 199;
        }
        
        #sidebarOverlay.show { display: block; }

        /* ---- Main content ---- */
        #adminMain {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            min-width: 0;
        }

        .admin-topbar {
            background: var(--adm-white);
            border-bottom: 1px solid var(--adm-border);
            padding: var(--spacing-md) var(--spacing-xl);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
            gap: var(--spacing-md);
        }
        
        .admin-topbar-title {
            font-size: var(--font-xl);
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .admin-topbar-user {
            font-size: var(--font-sm);
            color: var(--adm-muted);
            white-space: nowrap;
            flex-shrink: 0;
        }

        .admin-body {
            padding: var(--spacing-lg);
            flex: 1;
            min-width: 0;
        }

        /* ---- Stat cards ---- */
        .stat-card {
            background: var(--adm-white);
            border: 1px solid var(--adm-border);
            border-radius: 0.5rem;
            padding: var(--spacing-lg) var(--spacing-xl);
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
        }
        
        .stat-card-icon {
            width: 3rem;
            height: 3rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }
        
        .stat-card-num {
            font-size: var(--font-2xl);
            font-weight: 700;
            line-height: 1;
        }
        
        .stat-card-label {
            font-size: var(--font-xs);
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--adm-muted);
            margin-top: var(--spacing-xs);
        }

        /* ---- Tables ---- */
        .admin-table {
            background: var(--adm-white);
            border: 1px solid var(--adm-border);
            border-radius: 0.5rem;
        }
        
        .table-scroll {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: inherit;
        }
        
        .admin-table table {
            margin: 0;
            font-size: var(--font-base);
            min-width: 560px;
        }
        
        .admin-table thead th {
            background: #f9fafb;
            font-size: var(--font-sm);
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--adm-muted);
            border-bottom: 1px solid var(--adm-border);
            padding: var(--spacing-md);
            font-weight: 600;
            white-space: nowrap;
        }
        
        .admin-table tbody td {
            padding: var(--spacing-md);
            vertical-align: middle;
            border-bottom: 1px solid var(--adm-border);
        }
        
        .admin-table tbody tr:last-child td { border-bottom: none; }
        .admin-table tbody tr:hover { background: #fafafa; }

        /* ---- Section header bar ---- */
        .section-bar {
            background: var(--adm-white);
            border: 1px solid var(--adm-border);
            border-radius: 0.5rem;
            padding: 0.85rem var(--spacing-lg);
            margin-bottom: var(--spacing-md);
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: var(--spacing-sm);
        }
        
        .section-bar-title {
            font-size: var(--font-lg);
            font-weight: 700;
            margin: 0;
        }

        /* ---- Buttons ---- */
        .btn-adm-primary { background: var(--adm-text); color: var(--adm-white); border: none; font-size: var(--font-base); border-radius: 5px; }
        .btn-adm-primary:hover { background: #333; color: var(--adm-white); }
        .btn-adm-danger  { background: var(--adm-accent); color: var(--adm-white); border: none; font-size: var(--font-base); border-radius: 5px; }
        .btn-adm-danger:hover  { background: var(--adm-accent-hover); color: var(--adm-white); }
        .btn-adm-ghost   { background: transparent; border: 1px solid var(--adm-border); color: var(--adm-text); font-size: var(--font-base); border-radius: 5px; }
        .btn-adm-ghost:hover   { background: var(--adm-bg); }

        /* ---- Status badges ---- */
        .badge-active, .badge-approved, .badge-banned, .badge-overdue, .badge-pending2, .badge-returned, .badge-rejected{
            background: var(--adm-bg); font-size: 0.68rem; padding: 0.2rem 0.6rem; border-radius: 20px;
        }
        .badge-active   {color: #166534;  }
        .badge-banned   {color: #991b1b;}
        .badge-pending2 {color: #854d0e;}
        .badge-approved {color: #166534;}
        .badge-rejected {color: #991b1b;}
        .badge-returned {color: #075985;}
        .badge-overdue  {color: #991b1b;border: 1px solid #fca5a5; }

        /* ---- Book cover thumb ---- */
        .book-thumb {
            width: 36px;
            height: 50px;
            object-fit: cover;
            border-radius: 3px;
            display: block;
        }

        /* ---- Responsive ---- */
        @media (max-width: 768px) {
            #adminSidebar { transform: translateX(calc(-1 * var(--sidebar-w))); }
            #adminSidebar.open { transform: translateX(0); }
            #adminMain { margin-left: 0; }
            .admin-topbar { padding: 0.65rem var(--spacing-md); }
            .admin-body { padding: 0.85rem; }
            .stat-card { padding: 0.85rem var(--spacing-md); }
            .stat-card-num { font-size: 1.4rem; }
        }
    </style>
</head>
<body>
    <?php $content = $content??''; ?>

<!-- Overlay (mobile) -->
<div id="sidebarOverlay"></div>

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
        <a href="<?= BASE_URL ?>/administrator/messages"
           class="sidebar-link <?= ($activeNav ?? '') === 'messages' ? 'active' : '' ?>">
            <i class="bi bi-envelope"></i> Messages
        </a>
        <a href="<?= BASE_URL ?>/administrator/adminRequests"
           class="sidebar-link <?= ($activeNav ?? '') === 'adminRequests' ? 'active' : '' ?>">
            <i class="bi bi-shield-plus"></i> Admin Requests
            <?php if (!empty($pendingAdminCount) && $pendingAdminCount > 0): ?>
                <span class="badge-pending"><?= (int)$pendingAdminCount ?></span>
            <?php endif; ?>
        </a>

        <div class="sidebar-section-label">Site</div>
        <a href="<?= BASE_URL ?>/" class="sidebar-link" target="">
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
        <div class="d-flex align-items-center gap-2 min-w-0">
            <button class="d-sm d-md-none btn btn-sm btn-adm-ghost flex-shrink-0" id="sidebarToggle" aria-label="Toggle menu">
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
    <div class="px-3 pt-3">
        <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type']) ?> alert-dismissible fade show py-2 mb-0" role="alert">
            <?= htmlspecialchars($_SESSION['flash']['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <!-- Page content -->
    <div class="admin-body">
        <?= $content ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
(function () {
    var sidebar  = document.getElementById('adminSidebar');
    var overlay  = document.getElementById('sidebarOverlay');
    var toggle   = document.getElementById('sidebarToggle');

    function openSidebar() {
        sidebar.classList.add('open');
        overlay.classList.add('show');
    }
    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('show');
    }

    toggle.addEventListener('click', function () {
        sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
    });

    overlay.addEventListener('click', closeSidebar);
})();
</script>
</body>
</html>