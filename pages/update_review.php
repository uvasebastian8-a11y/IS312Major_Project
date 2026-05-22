<?php
// ============================================================
//  Library System — pages/update_review.php
//  IS312 AT3 | Team: Jasmine, Sebastian & Joseph
// ============================================================

require_once '../includes/db.php';
require_once '../includes/auth.php';

// Must be logged in
requireLogin();

// Validate review ID from URL
$review_id = (int)($_GET['id'] ?? 0);
if (!$review_id) {
    redirect(SITE_URL . '/pages/dashboard.php');
}

$user_id = getCurrentUserId();

// ── Fetch review and verify ownership ────────────────────────
// The WHERE clause checks UserID = logged in user
// This means users can ONLY edit their OWN reviews
$stmt = $conn->prepare(
    "SELECT r.*, b.Title AS BookTitle, b.BookID
     FROM Reviews r
     JOIN Books b ON r.BookID = b.BookID
     WHERE r.ReviewID = ? AND r.UserID = ?"
);
$stmt->bind_param('ii', $review_id, $user_id);
$stmt->execute();
$review = $stmt->get_result()->fetch_assoc();
$stmt->close();

// If review not found or doesn't belong to user — redirect
if (!$review) {
    setFlash('danger',
        'Review not found or you do not have 
         permission to edit it.');
    redirect(SITE_URL . '/pages/dashboard.php');
}

// ── Handle POST — process the update ─────────────────────────
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating  = (int)trim($_POST['rating']  ?? 0);
    $comment = trim($_POST['comment'] ?? '');

    // Validate
    if ($rating < 1 || $rating > 5) {
        $error = 'Please select a valid rating (1–5).';
    } elseif (empty($comment)) {
        $error = 'Comment cannot be empty.';
    } else {
        // Ownership enforced again in WHERE clause
        $upd = $conn->prepare(
            "UPDATE Reviews
             SET Rating = ?, Comment = ?
             WHERE ReviewID = ? AND UserID = ?"
        );
        $upd->bind_param('isii',
            $rating, $comment, $review_id, $user_id);

        if ($upd->execute() && $upd->affected_rows > 0) {
            setFlash('success',
                '✅ Your review has been updated successfully!');
            redirect(
                SITE_URL .
                '/pages/book_details.php?id=' .
                $review['BookID']
            );
        } else {
            $error = 'Failed to update review. Please try again.';
        }
        $upd->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Review | <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<!-- ── Navbar ──────────────────────────────────────────────── -->
<nav>
    <div class="nav-container">
        <h1>📚 <?= SITE_NAME ?></h1>
        <ul>
            <li>
                <a href="<?= SITE_URL ?>/index.php">Home</a>
            </li>
            <li>
                <a href="<?= SITE_URL ?>/index.php#books">
                    Books
                </a>
            </li>
            <li>
                <a href="<?= SITE_URL ?>/pages/reviews.php">
                    Reviews
                </a>
            </li>
            <li>
                <a href="<?= SITE_URL ?>/pages/dashboard.php">
                    My Account
                </a>
            </li>
            <li>
                <a href="<?= SITE_URL ?>/pages/logout.php">
                    Logout
                    (<?= htmlspecialchars(getCurrentUserName()) ?>)
                </a>
            </li>
        </ul>
    </div>
</nav>

<!-- ── Edit Review Form ─────────────────────────────────────── -->
<div class="container">
    <div class="auth-box" style="max-width:600px;margin:40px auto;">

        <h2>
            ✏️ Edit Review
        </h2>
        <p style="color:#666;margin-bottom:20px;">
            Editing review for:
            <strong>
                <?= htmlspecialchars($review['BookTitle']) ?>
            </strong>
        </p>

        <!-- Error Message -->
        <?php if ($error): ?>
        <div class="alert error">
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <!-- Update Form -->
        <form method="POST" action="">

            <label>Your Rating</label>
            <select name="rating" required>
                <?php
                $options = [
                    5 => '★★★★★ Excellent (5)',
                    4 => '★★★★☆ Good (4)',
                    3 => '★★★☆☆ Average (3)',
                    2 => '★★☆☆☆ Poor (2)',
                    1 => '★☆☆☆☆ Terrible (1)',
                ];
                foreach ($options as $val => $label):
                    $sel = ($review['Rating'] == $val)
                           ? 'selected' : '';
                ?>
                <option value="<?= $val ?>" <?= $sel ?>>
                    <?= $label ?>
                </option>
                <?php endforeach; ?>
            </select>

            <label>Your Comment</label>
            <textarea name="comment" rows="5" required
                      placeholder="Update your review…"
                ><?= htmlspecialchars($review['Comment']) ?></textarea>

            <button type="submit" class="btn">
                💾 Save Changes
            </button>

            <a href="<?= SITE_URL ?>/pages/book_details.php
                       ?id=<?= $review['BookID'] ?>"
               class="btn"
               style="background:#95a5a6;margin-left:10px;">
                ✖ Cancel
            </a>

        </form>
    </div>
</div>

<script src="../js/script.js"></script>
</body>
</html>