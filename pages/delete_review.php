<?php
// ============================================================
//  Library System — pages/delete_review.php
//  IS312 AT3 | Team: Jasmine, Sebastian & Joseph
// ============================================================

require_once '../includes/db.php';
require_once '../includes/auth.php';

// Must be logged in
requireLogin();

$user_id   = getCurrentUserId();
$review_id = (int)($_GET['id']      ?? 0);
$book_id   = (int)($_GET['book_id'] ?? 0);

// If no review ID provided redirect to dashboard
if (!$review_id) {
    setFlash('danger', 'Invalid review.');
    redirect(SITE_URL . '/pages/dashboard.php');
}

// ── Delete review — ownership enforced in WHERE clause ────────
// UserID = $user_id ensures users can ONLY delete their OWN reviews
// Even if someone guesses a ReviewID in the URL,
// it will not delete unless it belongs to them
$stmt = $conn->prepare(
    "DELETE FROM Reviews 
     WHERE ReviewID = ? AND UserID = ?"
);
$stmt->bind_param('ii', $review_id, $user_id);
$stmt->execute();

// ── Check if a row was actually deleted ───────────────────────
if ($stmt->affected_rows > 0) {
    // Successfully deleted
    setFlash('success', '🗑️ Your review has been deleted.');
} else {
    // Nothing deleted — review not found or not owned by user
    setFlash('danger',
        'Could not delete review. 
         You can only delete your own reviews.');
}

$stmt->close();

// ── Redirect back ─────────────────────────────────────────────
// Go back to book page if book_id provided
// Otherwise go to dashboard
if ($book_id) {
    redirect(
        SITE_URL . '/pages/book_details.php?id=' . $book_id
    );
} else {
    redirect(SITE_URL . '/pages/dashboard.php');
}
?>