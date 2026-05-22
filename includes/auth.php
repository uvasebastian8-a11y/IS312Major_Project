<?php
// ============================================================
//  Library System — includes/auth.php
//  IS312 AT3 | Team: Jasmine , Sebastian & Joseph
// ============================================================

// db.php already calls session_start() — include it instead
require_once 'db.php';

// ── Customer Auth ─────────────────────────────────────────────

// Check if a customer is logged in
function isLoggedIn() {
    return isset($_SESSION['customer_id']);
}

// Force login — redirect to login page if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        redirect(SITE_URL . '/pages/login.php');
    }
}

// ── Admin Auth ────────────────────────────────────────────────

// Check if an admin is logged in
function isAdmin() {
    return isset($_SESSION['admin_id']);
}

// Force admin login — redirect if not admin
function requireAdmin() {
    if (!isAdmin()) {
        redirect(SITE_URL . '/pages/login.php');
    }
}

// ── Current User Info ─────────────────────────────────────────

// Get logged-in customer's ID
function getCurrentUserId() {
    return $_SESSION['customer_id'] ?? null;
}

// Get logged-in customer's name
function getCurrentUserName() {
    return $_SESSION['customer_name'] ?? 'Guest';
}
?>