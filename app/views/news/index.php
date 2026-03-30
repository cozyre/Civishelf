<?php include __DIR__ . '/../layouts/header.php'; ?>

<main class="mt-4 mb-5 pb-5">

    <!-- =====================================================
         STATS HERO
    ====================================================== -->
    <section class="news-stats-hero">

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
         FEATURED NEWS CAROUSEL
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

                            <div class="news-carousel-img-wrap flex-shrink-0">
                                <?php if (!empty($item['image'])): ?>
                                    <img src="<?= BASE_URL ?>/assets/images/news/<?= htmlspecialchars($item['image']) ?>"
                                         alt="<?= htmlspecialchars($item['news_title']) ?>"
                                         class="news-carousel-img">
                                <?php else: ?>
                                    <div class="news-carousel-img-placeholder d-flex align-items-center justify-content-center">
                                        <i class="bi bi-newspaper" style="font-size:3rem; opacity:.3;"></i>
                                    </div>
                                <?php endif; ?>
                            </div>

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
                                <a href="<?= BASE_URL ?>/news/show/<?= $item['news_id'] ?>" class="news-read-more mt-3 align-self-start">
                                    Read more <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>

                        </div>
                    </div>
                    <?php endforeach; ?>

                <?php else: ?>
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
         NEWS CARD GRID
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
                <a href="<?= BASE_URL ?>/news/show/<?= $item['news_id'] ?>" class="news-grid-card d-flex text-decoration-none overflow-hidden rounded">

                    <div class="news-grid-img-wrap flex-shrink-0">
                        <?php if (!empty($item['image'])): ?>
                            <img src="<?= BASE_URL ?>/assets/images/news/<?= htmlspecialchars($item['image']) ?>"
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.stat-number').forEach(function (el) {
        var target   = parseInt(el.dataset.target, 10) || 0;
        var duration = 1200;
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