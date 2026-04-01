<?php include __DIR__ . '/../layouts/header.php'; ?>

<main class="mb-5 pb-5">
    <div class="container py-5 text-center" style="max-width: 520px;">

        <i class="bi bi-building" style="font-size: 3.5rem; opacity: 0.25;"></i>

        <h2 class="mt-3 mb-2" style="font-family: var(--title-font);">
            Not Available Online
        </h2>

        <p class="opacity-75 mb-1">
            This book doesn't have a digital version available for reading on Civishelf.
        </p>

        <p class="opacity-75 mb-4">
            You can access it physically at the library:
        </p>

        <div class="p-3 rounded" style="background: var(--primary); color: var(--base-theme);">
            <div class="fw-bold mb-1" style="font-family: var(--title-font); font-size: 1.1rem;">
                <!-- TODO: replace with real campus name -->
                University Campus Library
            </div>
            <div class="small opacity-75">
                <!-- TODO: replace with real location -->
                Main Building, Ground Floor — Room 001<br>
                Open: Mon–Fri, 8:00 AM – 8:00 PM
            </div>
        </div>

        <a href="javascript:history.back()" class="btn mt-4" style="background: var(--accent); color: var(--base-theme);">
            <i class="bi bi-arrow-left me-1"></i> Go Back
        </a>

    </div>
</main>

<?php include __DIR__ . '/../layouts/footer.php'; ?>