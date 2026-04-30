<?php include __DIR__ . '/../layouts/header.php'; ?>

<main class="">
    <div class="container py-4 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">

                <!-- Back arrow + title -->
                <div class="d-flex align-items-center justify-content-between mb-4 gap-3">
                    <a href="javascript:history.back()" class="contact-back-btn">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                    <h2 class="contact-title mb-0">Contact Us</h2>
                    <div></div>
                </div>

                <!-- Form card -->
                <div class="p-4">

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger py-2 mb-3">
                            <?php foreach ($errors as $error): ?>
                                <div class="small"><?= htmlspecialchars($error) ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= BASE_URL ?>/contact/send" method="POST" novalidate>

                        <div class="mb-3">
                            <label class="contact-label" for="contactName">Name</label>
                            <input type="text"
                                   id="contactName"
                                   name="name"
                                   class="form-control contact-input"
                                   placeholder="Name"
                                   value="<?= htmlspecialchars($name ?? '') ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="contact-label" for="contactEmail">Email</label>
                            <input type="email"
                                   id="contactEmail"
                                   name="email"
                                   class="form-control contact-input"
                                   placeholder="name123@email.com"
                                   value="<?= htmlspecialchars($email ?? '') ?>"
                                   required>
                        </div>

                        <div class="mb-4">
                            <label class="contact-label" for="contactMessage">Message</label>
                            <textarea id="contactMessage"
                                      name="message"
                                      class="form-control contact-input contact-textarea"
                                      placeholder="Need help..."
                                      required><?= htmlspecialchars($message ?? '') ?></textarea>
                        </div>

                        <button type="submit" class="btn accent w-100 mb-2">Submit</button>
                    </form>
                    <div class="align-items-center justify-content-center text-center">
                        <div class="opacity-50 py-2">Additional contacts</div>
                        <div><i class="bi bi-envelope"></i> Civishelf@company.com</div>
                        <div><i class="bi bi-telephone"></i> 123-3212-4563</div>
                    </div>
                </div>

                <!-- Success / error notification box -->
                <?php if (isset($sentAt)): ?>
                <div class="contact-notice mt-4 d-flex align-items-center gap-2">
                    <i class="bi bi-info-circle-fill"></i>
                    <span>Message successfully sent at: <?= htmlspecialchars($sentAt) ?></span>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</main>

<style>
.contact-back-btn {
    font-size: 1.4rem;
    color: var(--primary);
    line-height: 1;
}

.contact-back-btn:hover { opacity: 0.6; color: var(--primary); }

.contact-title {
    font-family: var(--title-font);
    font-size: 1.6rem;
}

.contact-card {
    background-color: var(--base-theme);
    border-radius: 12px;
    /* Slight shadow to lift the card off the page background */
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.contact-label {
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 0.35rem;
    display: block;
    color: var(--primary);
}

.contact-input {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    color: var(--primary);
}

.contact-input:focus {
    border-color: var(--primary);
    box-shadow: none;
}

.contact-textarea {
    min-height: 120px;
    resize: vertical;
}

.contact-submit {
    background-color: var(--primary);
    color: var(--base-theme);
    border-radius: 8px;
    font-weight: 600;
    letter-spacing: 0.03em;
}

.contact-submit:hover {
    background-color: var(--primary-light);
    color: var(--base-theme);
}

.contact-notice {
    background-color: var(--base-theme);
    border-radius: 8px;
    padding: 0.85rem 1rem;
    font-size: 0.88rem;
    color: var(--primary);
    box-shadow: 0 1px 6px rgba(0,0,0,0.07);
}

.contact-notice i { font-size: 1.1rem; opacity: 0.7; }
</style>

<?php include __DIR__ . '/../layouts/footer.php'; ?>