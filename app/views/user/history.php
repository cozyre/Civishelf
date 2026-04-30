<?php include __DIR__ . '/../layouts/header.php'; ?>

<main class="mb-5 pb-5">

    <div class="container-fluid px-4 py-4" style="background:var(--primary); color:var(--base-theme); border-bottom:3px solid var(--accent);">
        <span style="display:block; font-size:0.68rem; letter-spacing:0.18em; text-transform:uppercase; opacity:0.4; font-family:monospace; margin-bottom:0.3rem;">Your Activity</span>
        <h1 style="font-family:var(--title-font); font-size:2rem; margin:0;">Borrow History</h1>
    </div>

    <div class="container-fluid px-4 mt-4">

        <?php if (empty($history)): ?>
            <div class="text-center py-5">
                <i class="bi bi-clock-history fs-1 d-block mb-3 opacity-25"></i>
                <p class="opacity-50 mb-0">No borrow history yet.<br>
                    <a href="<?= BASE_URL ?>/books" style="color:var(--accent); text-decoration:underline;">Browse the collection</a>
                </p>
            </div>
        <?php else: ?>

        <div style="border:1px solid rgba(31,31,31,0.12); border-radius:6px; overflow:hidden;">
            <table class="table mb-0" style="font-size:0.875rem;">
                <thead>
                    <tr style="background:#f9fafb;">
                        <th style="font-size:0.7rem; letter-spacing:0.1em; text-transform:uppercase; color:#6b7280; padding:0.75rem 1rem;"></th>
                        <th style="font-size:0.7rem; letter-spacing:0.1em; text-transform:uppercase; color:#6b7280; padding:0.75rem 1rem;">Book</th>
                        <th style="font-size:0.7rem; letter-spacing:0.1em; text-transform:uppercase; color:#6b7280; padding:0.75rem 1rem;">Status</th>
                        <th style="font-size:0.7rem; letter-spacing:0.1em; text-transform:uppercase; color:#6b7280; padding:0.75rem 1rem;">Requested</th>
                        <th style="font-size:0.7rem; letter-spacing:0.1em; text-transform:uppercase; color:#6b7280; padding:0.75rem 1rem;">Due</th>
                        <th style="font-size:0.7rem; letter-spacing:0.1em; text-transform:uppercase; color:#6b7280; padding:0.75rem 1rem;">Returned</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $h): ?>
                    <?php
                        $now      = time();
                        $due      = $h['due_date'] ? strtotime($h['due_date']) : null;
                        $isOverdue = $h['status'] === 'approved' && $due && $due < $now;
                    ?>
                    <tr style="border-bottom:1px solid rgba(31,31,31,0.07);">
                        <td style="padding:0.7rem 1rem;">
                            <img src="<?= BASE_URL ?>/assets/images/covers/<?= htmlspecialchars($h['cover_image'] ?? 'book-placeholder.jpg') ?>"
                                 style="width:36px; height:50px; object-fit:cover; border-radius:3px; display:block;" alt="">
                        </td>
                        <td style="padding:0.7rem 1rem; vertical-align:middle;">
                            <div style="font-weight:600;"><?= htmlspecialchars($h['book_title']) ?></div>
                            <div style="font-size:0.72rem; opacity:0.5;"><?= htmlspecialchars($h['author_name'] ?? '—') ?></div>
                        </td>
                        <td style="padding:0.7rem 1rem; vertical-align:middle;">
                            <?php if ($isOverdue): ?>
                                <span style="background:#fef2f2; color:#991b1b; font-size:0.68rem; padding:0.2rem 0.6rem; border-radius:20px; border:1px solid #fca5a5;">Overdue</span>
                            <?php elseif ($h['status'] === 'pending'): ?>
                                <span style="background:#fef9c3; color:#854d0e; font-size:0.68rem; padding:0.2rem 0.6rem; border-radius:20px;">Pending</span>
                            <?php elseif ($h['status'] === 'approved'): ?>
                                <span style="background:#dcfce7; color:#166534; font-size:0.68rem; padding:0.2rem 0.6rem; border-radius:20px;">Approved</span>
                            <?php elseif ($h['status'] === 'returned'): ?>
                                <span style="background:#e0f2fe; color:#075985; font-size:0.68rem; padding:0.2rem 0.6rem; border-radius:20px;">Returned</span>
                            <?php elseif ($h['status'] === 'rejected'): ?>
                                <span style="background:#fef2f2; color:#991b1b; font-size:0.68rem; padding:0.2rem 0.6rem; border-radius:20px;">Rejected</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding:0.7rem 1rem; vertical-align:middle; font-size:0.78rem;">
                            <?= date('d M Y', strtotime($h['borrow_date'])) ?>
                        </td>
                        <td style="padding:0.7rem 1rem; vertical-align:middle; font-size:0.78rem; <?= $isOverdue ? 'color:#dc2626; font-weight:700;' : '' ?>">
                            <?= $h['due_date'] ? date('d M Y', strtotime($h['due_date'])) : '—' ?>
                        </td>
                        <td style="padding:0.7rem 1rem; vertical-align:middle; font-size:0.78rem; opacity:0.6;">
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