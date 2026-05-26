<?php include __DIR__ . '/../layouts/header.php'; ?>

<main class="mb-5 pb-5">

    <div class="history-page-header">
        <span class="history-eyebrow">Your Activity</span>
        <h1 class="history-title">Borrow History</h1>
    </div>

    <div class="container-fluid px-4 mt-4">

        <?php if (empty($history)): ?>
            <div class="text-center py-5">
                <i class="bi bi-clock-history fs-1 d-block mb-3 opacity-25"></i>
                <p class="opacity-50 mb-0">No borrow history yet.<br>
                    <a href="<?= BASE_URL ?>/books" class="history-link">Browse the collection</a>
                </p>
            </div>
        <?php else: ?>

        <div class="table-responsive history-table-wrap">
            <table class="table history-table mb-0">
                <thead>
                    <tr>
                        <th></th>
                        <th>Book</th>
                        <th>Status</th>
                        <th>Requested</th>
                        <th>Due</th>
                        <th>Returned</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $h): ?>
                    <?php
                        $now       = time();
                        $due       = $h['due_date'] ? strtotime($h['due_date']) : null;
                        $isOverdue = $h['status'] === 'approved' && $due && $due < $now;
                    ?>
                    <tr>
                        <td>
                            <img src="<?= BASE_URL ?>/assets/images/covers/<?= htmlspecialchars($h['cover_image'] ?? 'book-placeholder.jpg') ?>"
                                 class="history-cover" alt="">
                        </td>
                        <td>
                            <div class="history-book-title"><?= htmlspecialchars($h['book_title']) ?></div>
                            <div class="history-book-author"><?= htmlspecialchars($h['author_name'] ?? '—') ?></div>
                        </td>
                        <td>
                            <?php if ($isOverdue): ?>
                                <span class="history-badge history-badge--overdue">Overdue</span>
                            <?php elseif ($h['status'] === 'pending'): ?>
                                <span class="history-badge history-badge--pending">Pending</span>
                            <?php elseif ($h['status'] === 'approved'): ?>
                                <span class="history-badge history-badge--approved">Approved</span>
                            <?php elseif ($h['status'] === 'returned'): ?>
                                <span class="history-badge history-badge--returned">Returned</span>
                            <?php elseif ($h['status'] === 'rejected'): ?>
                                <span class="history-badge history-badge--rejected">Rejected</span>
                            <?php endif; ?>
                        </td>
                        <td class="history-date"><?= date('d M Y', strtotime($h['borrow_date'])) ?></td>
                        <td class="history-date <?= $isOverdue ? 'history-date--overdue' : '' ?>">
                            <?= $h['due_date'] ? date('d M Y', strtotime($h['due_date'])) : '—' ?>
                        </td>
                        <td class="history-date history-date--muted">
                            <?= $h['return_date'] ? date('d M Y', strtotime($h['return_date'])) : '—' ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php endif; ?>
    </div>
</main>

<?php include __DIR__ . '/../layouts/footer.php'; ?>