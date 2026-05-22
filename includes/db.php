<?php
// ============================================================
//  Library System — includes/db.php
//  Database connection ONLY — no auth functions here
//  IS312 AT3 | Team: Jasmine, Sebastian & Joseph
// ============================================================

session_start();

// Database credentials
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "library_system";

// Site configuration
define('SITE_URL',  'http://localhost/library_system');
define('SITE_NAME', 'Library System');

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die('<div style="font-family:sans-serif;padding:30px;
                     background:#ffe0e0;border:2px solid red;
                     margin:20px;border-radius:8px;">
            <h3>Database Connection Failed</h3>
            <p>Please ensure MySQL is running in XAMPP.</p>
         </div>');
}

// Set character encoding
$conn->set_charset("utf8mb4");

// ── Helper Functions ──────────────────────────────────────────

// Sanitise user input
function clean($conn, $val) {
    return $conn->real_escape_string(
               htmlspecialchars(trim($val)));
}

// Redirect helper
function redirect($url) {
    header("Location: $url");
    exit();
}

// Flash message — set
function setFlash($type, $msg) {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

// Flash message — show and clear
function showFlash() {
    if (!isset($_SESSION['flash'])) return '';
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    $cls = $f['type'] === 'success'
           ? 'alert-success' : 'alert-danger';
    return '<div class="alert ' . $cls . '">'
         . htmlspecialchars($f['msg'])
         . '</div>';
}
// NOTE: Auth functions are in includes/auth.php
// Do NOT add isLoggedIn() or requireLogin() here
?>