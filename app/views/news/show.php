<?php include __DIR__ . '/../layouts/header.php'; 
$article = $article??[];
?>

<main class="mb-5 pb-5">
    <div class="container py-5" style="max-width: 720px;">

        <!-- Back -->
        <a href="<?= BASE_URL ?>/news" class="d-inline-flex align-items-center gap-1 mb-4"
           style="color: var(--accent); font-size: 0.85rem; text-decoration: underline;">
            <i class="bi bi-arrow-left"></i> Back to News
        </a>

        <!-- Image -->
        <?php if (!empty($article['image'])): ?>
        <div class="mb-4" style="border-radius: 8px; overflow: hidden; max-height: 420px;">
            <img src="<?= BASE_URL ?>/assets/images/news/<?= htmlspecialchars($article['image']) ?>"
                 alt="<?= htmlspecialchars($article['news_title']) ?>"
                 style="width: 100%; height: 420px; object-fit: cover; display: block;">
        </div>
        <?php endif; ?>

        <!-- Meta -->
        <p style="font-size: 0.72rem; letter-spacing: 0.1em; text-transform: uppercase; opacity: 0.4; font-family: monospace;">
            <?= date('d F Y', strtotime($article['created_at'])) ?>
        </p>

        <!-- Title -->
        <h1 style="font-family: var(--title-font); font-size: 2rem; line-height: 1.25; margin-bottom: 1.5rem;">
            <?= htmlspecialchars($article['news_title']) ?>
        </h1>

        <hr style="opacity: 0.12; margin-bottom: 1.5rem;">

        <!-- Body -->
        <div style="font-size: 1rem; line-height: 1.85; color: var(--primary); opacity: 0.85;">
            <?= nl2br(htmlspecialchars($article['content'])) ?>
        </div>

    </div>
</main>

<?php include __DIR__ . '/../layouts/footer.php'; ?>