<?php
// ============================================================
//  Library System — pages/dashboard.php
//  IS312 AT3 | Team: Jasmine, Sebastian & Joseph
// ============================================================

require_once '../includes/db.php';
require_once '../includes/auth.php';

// Redirect to login if not logged in
requireLogin();

$user_id = getCurrentUserId();

// ── Fetch real user statistics ────────────────────────────────
// Total reviews by this user
$stat_stmt = $conn->prepare(
    "SELECT COUNT(*)        AS review_count,
            ROUND(AVG(Rating), 1) AS avg_rating
     FROM Reviews
     WHERE UserID = ?"
);
$stat_stmt->bind_param('i', $user_id);
$stat_stmt->execute();
$stats = $stat_stmt->get_result()->fetch_assoc();
$stat_stmt->close();

// Total books in library
$book_count = $conn->query(
    "SELECT COUNT(*) AS cnt FROM Books"
)->fetch_assoc()['cnt'];

// ── Fetch this user's reviews ─────────────────────────────────
$sql = "SELECT r.*, b.Title AS BookTitle, b.BookID
        FROM Reviews r
        JOIN Books b ON r.BookID = b.BookID
        WHERE r.UserID = ?
        ORDER BY r.CreatedAt DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard | <?= SITE_NAME ?></title>
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
                <a href="<?= SITE_URL ?>/index.php#books">Books</a>
            </li>
            <li>
                <a href="<?= SITE_URL ?>/pages/reviews.php">
                    Reviews
                </a>
            </li>
            <li>
                <a href="<?= SITE_URL ?>/pages/dashboard.php"
                   class="active">
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

<!-- ── Flash Message ───────────────────────────────────────── -->
<div class="container">
    <?= showFlash() ?>
</div>

<!-- ── Dashboard ───────────────────────────────────────────── -->
<div class="dashboard-container">

    <!-- Welcome Header -->
    <div class="welcome-header">
        <h2>
            Welcome, 
            <?= htmlspecialchars(getCurrentUserName()) ?>! 👋
        </h2>
        <p>Manage your reviews and track your reading activity</p>
    </div>

    <!-- Stats Grid — all real data -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number">
                <?= $stats['review_count'] ?>
            </div>
            <p>Reviews Written</p>
        </div>
        <div class="stat-card">
            <div class="stat-number">
                <?= $book_count ?>
            </div>
            <p>Books in Library</p>
        </div>
        <div class="stat-card">
            <div class="stat-number">
                <?= $stats['avg_rating'] 
                    ? $stats['avg_rating'] . ' ⭐' 
                    : 'N/A' ?>
            </div>
            <p>Your Avg Rating</p>
        </div>
    </div>

    <!-- My Reviews -->
    <div class="my-reviews">
        <h3>📝 My Reviews</h3>

        <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $review): ?>
            <div class="review-card">
                <h4>
                    <a href="<?= SITE_URL ?>/pages/book_details.php
                               ?id=<?= $review['BookID'] ?>">
                        <?= htmlspecialchars($review['BookTitle']) ?>
                    </a>
                </h4>

                <!-- Star Rating -->
                <div class="rating">
                    <?= str_repeat('★', (int)$review['Rating']) ?>
                    <?= str_repeat('☆', 5 - (int)$review['Rating']) ?>
                    <small><?= $review['Rating'] ?>/5</small>
                </div>

                <!-- Comment -->
                <p>
                    <?= nl2br(htmlspecialchars($review['Comment'])) ?>
                </p>

                <small>
                    Posted: 
                    <?= date('F j, Y', 
                             strtotime($review['CreatedAt'])) ?>
                </small>

                <!-- Edit / Delete — ownership guaranteed 
                     (query filters by UserID = $user_id) -->
                <div class="review-actions">
                    <a href="<?= SITE_URL ?>/pages/update_review.php
                               ?id=<?= $review['ReviewID'] ?>">
                        ✏️ Edit
                    </a>
                    <a href="<?= SITE_URL ?>/pages/delete_review.php
                               ?id=<?= $review['ReviewID'] ?>"
                       onclick="return confirm(
                           'Delete this review? This cannot be undone.')">
                        🗑️ Delete
                    </a>
                </div>
            </div>
            <?php endforeach; ?>

        <?php else: ?>
            <div class="no-reviews">
                <p>
                    You haven't written any reviews yet.<br>
                    <a href="<?= SITE_URL ?>/index.php#books">
                        Browse books
                    </a> 
                    to write your first review!
                </p>
            </div>
        <?php endif; ?>

    </div><!-- /.my-reviews -->
</div><!-- /.dashboard-container -->

<script src="../js/script.js"></script>
</body>
</html>