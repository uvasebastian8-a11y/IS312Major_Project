<?php
// ============================================================
//  Library System — pages/add_review.php
//  IS312 AT3 | Team: Jasmine, Sebastian & Joseph
// ============================================================

require_once '../includes/db.php';
require_once '../includes/auth.php';

// Must be logged in to add a review
requireLogin();

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(SITE_URL . '/index.php');
}

// ── Sanitise and validate inputs ──────────────────────────────
$user_id = getCurrentUserId();
$book_id = (int)($_POST['book_id'] ?? 0);
$rating  = (int)($_POST['rating']  ?? 0);
$comment = trim($_POST['comment']  ?? '');

// Validate book ID
if (!$book_id) {
    setFlash('danger', 'Invalid book. Please try again.');
    redirect(SITE_URL . '/index.php');
}

// Validate rating range
if ($rating < 1 || $rating > 5) {
    setFlash('danger', 'Please select a rating between 1 and 5.');
    redirect(SITE_URL . '/pages/book_details.php?id=' . $book_id);
}

// Validate comment
if (empty($comment)) {
    setFlash('danger', 'Please write a comment before submitting.');
    redirect(SITE_URL . '/pages/book_details.php?id=' . $book_id);
}

// ── Check book exists ─────────────────────────────────────────
$chk = $conn->prepare(
    "SELECT BookID FROM Books WHERE BookID = ?"
);
$chk->bind_param('i', $book_id);
$chk->execute();
$chk->store_result();

if ($chk->num_rows === 0) {
    setFlash('danger', 'Book not found.');
    redirect(SITE_URL . '/index.php');
}
$chk->close();

// ── Check for duplicate review ────────────────────────────────
// Spec: users can only review once per book
$dup = $conn->prepare(
    "SELECT ReviewID FROM Reviews 
     WHERE UserID = ? AND BookID = ?"
);
$dup->bind_param('ii', $user_id, $book_id);
$dup->execute();
$dup->store_result();

if ($dup->num_rows > 0) {
    setFlash('danger',
        'You have already reviewed this book. 
         You can edit your existing review instead.');
    redirect(SITE_URL . '/pages/book_details.php?id=' . $book_id);
}
$dup->close();

// ── Insert review ─────────────────────────────────────────────
$stmt = $conn->prepare(
    "INSERT INTO Reviews (UserID, BookID, Rating, Comment)
     VALUES (?, ?, ?, ?)"
);
$stmt->bind_param('iiis', $user_id, $book_id, $rating, $comment);

if ($stmt->execute()) {
    setFlash('success', '✅ Your review has been submitted!');
    redirect(SITE_URL . '/pages/book_details.php?id=' . $book_id);
} else {
    setFlash('danger', 'Failed to submit review. Please try again.');
    redirect(SITE_URL . '/pages/book_details.php?id=' . $book_id);
}

$stmt->close();
?>