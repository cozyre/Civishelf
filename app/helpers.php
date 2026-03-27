<?php

/**
 * Redirect to a path, prepending BASE_URL.
 * Usage: redirect('/books') or redirect('/')
 */
function redirect(string $path): void {
    header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
    exit;
}

/**
 * Set a one-time flash message in the session.
 * Type maps to Bootstrap alert classes: success, danger, warning, info
 * Usage: flash('success', 'You have logged in.')
 */
function flash(string $type, string $message): void {
    $_SESSION['flash'] = [
        'type'    => $type,
        'message' => $message,
    ];
}