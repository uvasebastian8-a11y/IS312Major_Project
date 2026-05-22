<?php
// ============================================================
//  Library System — pages/reviews.php
//  IS312 AT3 | Team: Jasmine, Sebastian & Joseph
// ============================================================

require_once '../includes/db.php';
require_once '../includes/auth.php';

// ── Filter by book (sanitised) ────────────────────────────────
$selected_book = (int)($_GET['book_id'] ?? 0);

// ── Fetch all books for filter dropdown ───────────────────────
$books = $conn->query(
    "SELECT BookID, Title FROM Books ORDER BY Title ASC"
)->fetch_all(MYSQLI_ASSOC);

// ── Fetch reviews — filtered or all ──────────────────────────
if ($selected_book > 0) {
    $stmt = $conn->prepare(
        "SELECT r.*, u.FirstName, u.LastName,
                b.Title AS BookTitle, b.BookID
         FROM Reviews r
         JOIN Users u ON r.UserID = u.UserID
         JOIN Books b ON r.BookID = b.BookID
         WHERE r.BookID = ?
         ORDER BY r.CreatedAt DESC"
    );
    $stmt->bind_param('i', $selected_book);
    $stmt->execute();
    $reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $reviews = $conn->query(
        "SELECT r.*, u.FirstName, u.LastName,
                b.Title AS BookTitle, b.BookID
         FROM Reviews r
         JOIN Users u ON r.UserID = u.UserID
         JOIN Books b ON r.BookID = b.BookID
         ORDER BY r.CreatedAt DESC"
    )->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Reviews | <?= SITE_NAME ?></title>
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
                <a href="<?= SITE_URL ?>/pages/reviews.php"
                   class="active">
                    Reviews
                </a>
            </li>
            <?php if (isLoggedIn()): ?>
                <li>
                    <a href="<?= SITE_URL ?>/pages/dashboard.php">
                        My Account
                    </a>
                </li>
                <li>
                    <a href="<?= SITE_URL ?>/pages/logout.php">
                        Logout
                        (<?= htmlspecialchars(
                               getCurrentUserName()) ?>)
                    </a>
                </li>
            <?php else: ?>
                <li>
                    <a href="<?= SITE_URL ?>/pages/login.php">
                        Login
                    </a>
                </li>
                <li>
                    <a href="<?= SITE_URL ?>/pages/register.php">
                        Register
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<!-- ── Reviews Page ────────────────────────────────────────── -->
<div class="reviews-container">

    <!-- Header -->
    <div class="reviews-header">
        <h2>📝 All Book Reviews</h2>
        <p>
            Read what other readers are saying 
            about their favourite books
        </p>
    </div>

    <?= showFlash() ?>

    <!-- Filter Dropdown -->
    <div class="filter-section">
        <label for="book_filter">
            <strong>Filter by Book:</strong>
        </label>
        <select id="book_filter">
            <option value="0">All Books</option>
            <?php foreach ($books as $b): ?>
            <option value="<?= $b['BookID'] ?>"
                <?= ($selected_book == $b['BookID']) 
                    ? 'selected' : '' ?>>
                <?= htmlspecialchars($b['Title']) ?>
            </option>
            <?php endforeach; ?>
        </select>
        <small style="color:#999;margin-left:10px;">
            <?= count($reviews) ?> 
            review<?= count($reviews) != 1 ? 's' : '' ?> found
        </small>
    </div>

    <!-- Reviews List -->
    <div class="reviews-list">
    <?php if (!empty($reviews)): ?>
        <?php foreach ($reviews as $review): ?>
        <div class="review-item">

            <!-- Book Title Link -->
            <div class="review-book-title">
                <a href="<?= SITE_URL ?>/pages/book_details.php
                           ?id=<?= $review['BookID'] ?>">
                    📖 <?= htmlspecialchars($review['BookTitle']) ?>
                </a>
            </div>

            <!-- Review Meta -->
            <div class="review-meta">
                <span class="reviewer-name">
                    By: <?= htmlspecialchars(
                              $review['FirstName'] . ' ' .
                              $review['LastName']) ?>
                </span>
                <span class="review-rating">
                    <?= str_repeat('★', (int)$review['Rating']) ?>
                    <?= str_repeat('☆', 5-(int)$review['Rating']) ?>
                    <small><?= $review['Rating'] ?>/5</small>
                </span>
                <span class="review-date">
                    <?= date('F j, Y',
                             strtotime($review['CreatedAt'])) ?>
                </span>
            </div>

            <!-- Comment -->
            <div class="review-comment">
                "<?= nl2br(htmlspecialchars($review['Comment'])) ?>"
            </div>

            <!-- Edit/Delete — only shown to review owner -->
            <?php if (isLoggedIn() &&
                      getCurrentUserId() == $review['UserID']): ?>
            <div class="review-actions" style="margin-top:12px;">
                <a href="<?= SITE_URL ?>/pages/update_review.php
                           ?id=<?= $review['ReviewID'] ?>">
                    ✏️ Edit
                </a>
                <a href="<?= SITE_URL ?>/pages/delete_review.php
                           ?id=<?= $review['ReviewID'] ?>
                           &book_id=<?= $review['BookID'] ?>"
                   onclick="return confirm(
                       'Delete this review? Cannot be undone.')">
                    🗑️ Delete
                </a>
            </div>
            <?php endif; ?>

        </div>
        <?php endforeach; ?>

    <?php else: ?>
        <div class="no-reviews">
            <p>😕 No reviews found.</p>
            <p>
                <a href="<?= SITE_URL ?>/index.php#books">
                    Browse books
                </a>
                and be the first to write a review!
            </p>
        </div>
    <?php endif; ?>
    </div><!-- /.reviews-list -->

</div><!-- /.reviews-container -->

<script src="../js/script.js"></script>
<script>
    // Filter dropdown — redirects to filtered URL
    document.getElementById('book_filter')
        .addEventListener('change', function () {
            const id  = this.value;
            const url = '<?= SITE_URL ?>/pages/reviews.php';
            window.location.href = id > 0
                ? url + '?book_id=' + id
                : url;
        });
</script>
</body>
</html>