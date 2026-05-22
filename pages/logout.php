<?php
// ============================================================
//  Library System — pages/logout.php
//  IS312 AT3 | Team: Jasmine, Sebastian & Joseph
// ============================================================

require_once '../includes/db.php';

// Step 1 — Clear all session variables
$_SESSION = [];

// Step 2 — Destroy the session cookie in the browser
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Step 3 — Destroy the session on the server
session_destroy();

// Step 4 — Redirect to home with goodbye message
// We use a GET parameter since session is destroyed
header('Location: ' . SITE_URL . '/index.php?msg=loggedout');
exit();
?>