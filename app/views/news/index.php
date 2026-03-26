<?php include __DIR__ . '/../layouts/header.php'; ?>

<!--
    BACKEND HOOKS (already wired from NewsController::index()):
      $totalBooks    — int, sum of all books' total_copies
      $totalBorrowed — int, books currently out on loan
      $totalUsers    — int, active registered users
      $featuredNews  — array[0..2] for the carousel
      $gridNews      — array[3..8] for the card grid

    Each news row: news_id, news_title, content, image, created_at
-->

<main class="mt-4 mb-5 pb-5">

    <!-- =====================================================
         SECTION 1 — STATS HERO
         Custom CSS-only background: diagonal ruled lines
         layered over --primary dark. The three stat counters
         animate up from 0 on page load via JS.
    ====================================================== -->
    <section class="news-stats-hero">

        <!-- Decorative ruled-line background -->
        <div class="stats-bg-lines" aria-hidden="true"></div>

        <div class="container-fluid px-4 py-5 position-relative">
            <h1 class="stats-eyebrow mb-4">Civishelf / News & Library Stats</h1>

            <div class="row g-4 text-center">

                <div class="col-12 col-md-4">
                    <div class="stat-block">
                        <i class="bi bi-journals stat-icon mb-2"></i>
                        <div class="stat-number" data-target="<?= (int)($totalBooks ?? 0) ?>">0</div>
                        <div class="stat-label">Books in the Library</div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="stat-block stat-block--accent">
                        <i class="bi bi-book-half stat-icon mb-2"></i>
                        <div class="stat-number" data-target="<?= (int)($totalBorrowed ?? 0) ?>">0</div>
                        <div class="stat-label">Currently Being Borrowed</div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="stat-block">
                        <i class="bi bi-people stat-icon mb-2"></i>
                        <div class="stat-number" data-target="<?= (int)($totalUsers ?? 0) ?>">0</div>
                        <div class="stat-label">Active Readers</div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- =====================================================
         SECTION 2 — FEATURED NEWS CAROUSEL
         3 most recent articles from the news table.
         Falls back to a placeholder card if DB is empty.
    ====================================================== -->
    <section class="container-fluid px-3 mt-5">

        <div class="section-header d-flex align-items-center mb-3">
            <h2 class="section-title mb-0 me-3">Featured</h2>
            <hr class="flex-grow-1 m-0">
        </div>

        <div id="newsCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">

                <?php if (!empty($featuredNews)): ?>
                    <?php foreach ($featuredNews as $idx => $item): ?>
                    <div class="carousel-item <?= $idx === 0 ? 'active' : '' ?>">
                        <div class="news-carousel-card mx-auto d-flex overflow-hidden rounded">

                            <!-- Image side -->
                            <div class="news-carousel-img-wrap flex-shrink-0">
                                <?php if (!empty($item['image'])): ?>
                                    <img src="/assets/images/news/<?= htmlspecialchars($item['image']) ?>"
                                         alt="<?= htmlspecialchars($item['news_title']) ?>"
                                         class="news-carousel-img">
                                <?php else: ?>
                                    <div class="news-carousel-img-placeholder d-flex align-items-center justify-content-center">
                                        <i class="bi bi-newspaper" style="font-size:3rem; opacity:.3;"></i>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Text side -->
                            <div class="news-carousel-body p-4 d-flex flex-column justify-content-between">
                                <div>
                                    <p class="news-carousel-date mb-1">
                                        <?= date('d M Y', strtotime($item['created_at'])) ?>
                                    </p>
                                    <h3 class="news-carousel-title"><?= htmlspecialchars($item['news_title']) ?></h3>
                                    <p class="news-carousel-excerpt mt-2">
                                        <?= htmlspecialchars(mb_substr(strip_tags($item['content']), 0, 240)) ?>…
                                    </p>
                                </div>
                                <a href="/news/show/<?= $item['news_id'] ?>" class="news-read-more mt-3 align-self-start">
                                    Read more <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>

                        </div>
                    </div>
                    <?php endforeach; ?>

                <?php else: ?>
                    <!-- Placeholder when no news in DB yet -->
                    <div class="carousel-item active">
                        <div class="news-carousel-card mx-auto d-flex overflow-hidden rounded align-items-center justify-content-center p-5 text-center opacity-50">
                            <p class="mb-0">No featured news yet. Add some from the admin panel.</p>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

            <?php if (count($featuredNews ?? []) > 1): ?>
            <button class="carousel-control-prev" type="button" data-bs-target="#newsCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#newsCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
                <span class="visually-hidden">Next</span>
            </button>
            <?php endif; ?>
        </div>

    </section>

    <!-- =====================================================
         SECTION 3 — NEWS CARD GRID
         Articles 4–9 in a 2-column responsive grid.
    ====================================================== -->
    <section class="container-fluid px-3 mt-5">

        <div class="section-header d-flex align-items-center mb-3">
            <h2 class="section-title mb-0 me-3">Latest News</h2>
            <hr class="flex-grow-1 m-0">
        </div>

        <?php if (!empty($gridNews)): ?>
        <div class="row g-3">
            <?php foreach ($gridNews as $item): ?>
            <div class="col-12 col-md-6">
                <a href="/news/show/<?= $item['news_id'] ?>" class="news-grid-card d-flex text-decoration-none overflow-hidden rounded">

                    <div class="news-grid-img-wrap flex-shrink-0">
                        <?php if (!empty($item['image'])): ?>
                            <img src="/Civishelf/public/assets/images/news/<?= htmlspecialchars($item['image']) ?>"
                                 alt="<?= htmlspecialchars($item['news_title']) ?>"
                                 class="news-grid-img">
                        <?php else: ?>
                            <div class="news-grid-img news-grid-img--placeholder d-flex align-items-center justify-content-center">
                                <i class="bi bi-newspaper opacity-25" style="font-size:1.5rem;"></i>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="news-grid-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <p class="news-grid-date mb-1"><?= date('d M Y', strtotime($item['created_at'])) ?></p>
                            <h5 class="news-grid-title"><?= htmlspecialchars($item['news_title']) ?></h5>
                            <p class="news-grid-excerpt small">
                                <?= htmlspecialchars(mb_substr(strip_tags($item['content']), 0, 120)) ?>…
                            </p>
                        </div>
                        <span class="news-read-more mt-2 d-inline-block">Read more <i class="bi bi-arrow-right ms-1"></i></span>
                    </div>

                </a>
            </div>
            <?php endforeach; ?>
        </div>

        <?php else: ?>
            <p class="text-center opacity-50 py-4">No articles yet.</p>
        <?php endif; ?>

    </section>

</main>


<!-- ================================================================
     PAGE-SPECIFIC STYLES — move to style.css once finalized
================================================================= -->
<style>

/* ---- Stats Hero ---- */
.news-stats-hero {
    position: relative;
    background-color: var(--primary);
    color: var(--base-theme);
    overflow: hidden;
}

/* Diagonal ruled-line texture as a pseudo-element */
.stats-bg-lines {
    position: absolute;
    inset: 0;
    background-image: repeating-linear-gradient(
        -55deg,
        transparent,
        transparent 18px,
        rgba(255,255,255,0.035) 18px,
        rgba(255,255,255,0.035) 19px
    );
    pointer-events: none;
    z-index: 0;
}

/* Accent bleed on the left edge */
.news-stats-hero::after {
    content: '';
    position: absolute;
    top: 0; left: 0;
    width: 4px;
    height: 100%;
    background: var(--accent);
}

.stats-eyebrow {
    font-family: var(--title-font);
    font-size: 0.85rem;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    opacity: 0.45;
    position: relative;
    z-index: 1;
}

.stat-block {
    position: relative;
    z-index: 1;
    padding: 1.5rem 1rem;
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 6px;
    background: rgba(255,255,255,0.03);
    transition: background 0.2s;
}

.stat-block:hover { background: rgba(255,255,255,0.07); }

.stat-block--accent {
    border-color: var(--accent);
    background: rgba(195,13,0,0.06);
}

.stat-block--accent:hover { background: rgba(195,13,0,0.12); }

.stat-icon {
    display: block;
    font-size: 2.25rem;
    opacity: 0.6;
}

.stat-number {
    font-family: var(--title-font);
    font-size: 3.5rem;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 0.4rem;
}

.stat-block--accent .stat-number { color: var(--accent-light); }

.stat-label {
    font-size: 0.8rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    opacity: 0.55;
}

/* ---- Carousel ---- */
.news-carousel-card {
    background: var(--primary);
    color: var(--base-theme);
    min-height: 280px;
    max-width: 900px;
}

.news-carousel-img-wrap {
    width: 320px;
    min-height: 280px;
    background: #111;
    flex-shrink: 0;
}

.news-carousel-img,
.news-carousel-img-placeholder {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    min-height: 280px;
    background: #111;
}

.news-carousel-date {
    font-size: 0.72rem;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    opacity: 0.4;
}

.news-carousel-title {
    font-family: var(--title-font);
    font-size: 1.4rem;
    line-height: 1.3;
}

.news-carousel-excerpt {
    font-size: 0.85rem;
    opacity: 0.65;
    line-height: 1.6;
    max-height: 100px;
    overflow: hidden;
}

/* ---- Shared "read more" link ---- */
.news-read-more {
    font-size: 0.8rem;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--accent-light);
    text-decoration: none;
    transition: opacity 0.15s;
}

.news-read-more:hover { opacity: 0.7; color: var(--accent-light); }

/* ---- Grid cards ---- */
.news-grid-card {
    background: var(--primary);
    color: var(--base-theme);
    transition: background 0.2s;
}

.news-grid-card:hover { background: var(--primary-light); color: var(--base-theme); }

.news-grid-img-wrap { width: 140px; flex-shrink: 0; }

.news-grid-img,
.news-grid-img--placeholder {
    width: 140px;
    height: 100%;
    min-height: 120px;
    object-fit: cover;
    display: block;
    background: #111;
}

.news-grid-date {
    font-size: 0.68rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    opacity: 0.4;
}

.news-grid-title {
    font-family: var(--title-font);
    font-size: 1rem;
    line-height: 1.35;
    margin-bottom: 0.3rem;
}

.news-grid-excerpt { opacity: 0.6; line-height: 1.5; }

@media (max-width: 576px) {
    .news-carousel-img-wrap { width: 110px; }
    .news-carousel-title { font-size: 1.05rem; }
    .stat-number { font-size: 2.5rem; }
}

</style>


<!-- ================================================================
     JS — animated stat counters
================================================================= -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Counts up from 0 to data-target over ~1.2s
    document.querySelectorAll('.stat-number').forEach(function (el) {
        var target   = parseInt(el.dataset.target, 10) || 0;
        var duration = 1200;
        var step     = Math.max(1, Math.floor(duration / Math.max(target, 1)));
        var current  = 0;

        if (target === 0) { el.textContent = '0'; return; }

        var timer = setInterval(function () {
            current += Math.ceil(target / (duration / 16));
            if (current >= target) { current = target; clearInterval(timer); }
            el.textContent = current.toLocaleString();
        }, 16);
    });
});
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>