<?php

/**
 * Extend WHMCS admin session lifetime.
 *
 * The default PHP session garbage collection timeout is often very short
 * (24 minutes), causing frequent logouts. This hook extends the session
 * lifetime to 24 hours so admins stay logged in longer.
 */

add_hook('AdminAreaPage', 1, function ($vars) {
    // Extend session lifetime to 24 hours (86400 seconds)
    ini_set('session.gc_maxlifetime', 86400);
    ini_set('session.cookie_lifetime', 86400);

    // Extend the session cookie expiry in the browser
    if (session_status() === PHP_SESSION_ACTIVE && isset($_COOKIE[session_name()])) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            session_id(),
            time() + 86400,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
});
