<?php
// ============================================================
//  Library System — pages/book_details.php
//  IS312 AT3 | Team: (Your Name), Sebastian & Joseph
// ============================================================

require_once '../includes/db.php';
require_once '../includes/auth.php';

// Validate book ID
$book_id = (int)($_GET['id'] ?? 0);
if (!$book_id) {
    redirect(SITE_URL . '/index.php');
}

// ── Fetch book details ────────────────────────────────────────
$stmt = $conn->prepare("SELECT * FROM Books WHERE BookID = ?");
$stmt->bind_param('i', $book_id);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$book) {
    redirect(SITE_URL . '/index.php');
}

// ── Fetch rating summary ──────────────────────────────────────
$stmt = $conn->prepare(
    "SELECT ROUND(AVG(Rating),1) AS avg_rating,
            COUNT(*)             AS review_count
     FROM Reviews WHERE BookID = ?"
);
$stmt->bind_param('i', $book_id);
$stmt->execute();
$rating_data = $stmt->get_result()->fetch_assoc();
$stmt->close();

// ── Fetch all reviews for this book ──────────────────────────
$stmt = $conn->prepare(
    "SELECT r.*, u.FirstName, u.LastName
     FROM Reviews r
     JOIN Users u ON r.UserID = u.UserID
     WHERE r.BookID = ?
     ORDER BY r.CreatedAt DESC"
);
$stmt->bind_param('i', $book_id);
$stmt->execute();
$reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ── Image path resolver ───────────────────────────────────────
function resolveBookImage(array $book): string {
    $map = [
        'the great gatsby'      => 'gatsby.jpg',
        'to kill a mockingbird' => 'mockingbird.jpg',
        '1984'                  => '1984.jpg',
        'the alchemist'         => 'alchemist.jpg',
        'atomic habits'         => 'atomic.jpg',
        'the power of now'      => 'power_of_now.jpg',
    ];

    // Check database image field first
    if (!empty($book['Image'])) {
        if (file_exists('../images/' . $book['Image']))
            return '../images/' . $book['Image'];
    }

    // Match by title
    $title = strtolower($book['Title']);
    foreach ($map as $key => $file) {
        if (strpos($title, $key) !== false) {
            if (file_exists('../images/' . $file))
                return '../images/' . $file;
        }
    }
    return '';
}

$image_path = resolveBookImage($book);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= htmlspecialchars($book['Title']) ?> | <?= SITE_NAME ?>
    </title>
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

<div class="container">
    <?= showFlash() ?>

    <div class="book-detail">

        <!-- ── Book Header ─────────────────────────────────── -->
        <div class="book-detail-header">

            <!-- Cover Image -->
            <div class="book-detail-cover">
                <?php if ($image_path): ?>
                    <img src="<?= htmlspecialchars($image_path) ?>"
                         alt="<?= htmlspecialchars($book['Title']) ?>"
                         onerror="this.onerror=null;
                                  this.parentElement.innerHTML=
                                  '<div class=\'no-image large\'>
                                   📖</div>'">
                <?php else: ?>
                    <div class="no-image large">📖</div>
                <?php endif; ?>
            </div>

            <!-- Book Info -->
            <div class="book-detail-info">
                <h2><?= htmlspecialchars($book['Title']) ?></h2>
                <p class="author">
                    by <?= htmlspecialchars($book['Author']) ?>
                </p>
                <p class="category">
                    Category: 
                    <?= htmlspecialchars($book['Category']) ?>
                </p>
                <p class="description">
                    <?= nl2br(htmlspecialchars(
                                  $book['Description'])) ?>
                </p>

                <!-- Average Rating -->
                <?php if ($rating_data['review_count'] > 0): ?>
                <div class="rating-summary">
                    <p>Average Rating:
                        <span class="rating">
                            <?php
                            $avg   = (float)$rating_data['avg_rating'];
                            $full  = (int)floor($avg);
                            $half  = ($avg - $full) >= 0.5;
                            echo str_repeat('★', $full);
                            if ($half) echo '½';
                            echo str_repeat('☆', 5 - (int)ceil($avg));
                            echo " ($avg/5)";
                            ?>
                        </span>
                        <small>
                            (<?= $rating_data['review_count'] ?>
                            review<?= $rating_data['review_count'] 
                                       != 1 ? 's' : '' ?>)
                        </small>
                    </p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ── Reviews Section ─────────────────────────────── -->
        <div class="reviews-section">
            <h3>📝 Reviews</h3>

            <!-- Add Review Form — logged in users only -->
            <?php if (isLoggedIn()): ?>
            <div class="add-review-form">
                <h4>Write Your Review</h4>
                <form method="POST"
                      action="<?= SITE_URL ?>/pages/add_review.php">
                    <input type="hidden" name="book_id"
                           value="<?= $book['BookID'] ?>">
                    <select name="rating" required>
                        <option value="">-- Select Rating --</option>
                        <option value="5">★★★★★ Excellent (5)</option>
                        <option value="4">★★★★☆ Good (4)</option>
                        <option value="3">★★★☆☆ Average (3)</option>
                        <option value="2">★★☆☆☆ Poor (2)</option>
                        <option value="1">★☆☆☆☆ Terrible (1)</option>
                    </select>
                    <textarea name="comment" rows="4" required
                        placeholder="Share your thoughts about this book…">
                    </textarea>
                    <button type="submit" class="btn">
                        Submit Review
                    </button>
                </form>
            </div>

            <?php else: ?>
            <div class="login-prompt">
                <p>
                    <a href="<?= SITE_URL ?>/pages/login.php">
                        Login
                    </a>
                    or
                    <a href="<?= SITE_URL ?>/pages/register.php">
                        register
                    </a>
                    to write a review.
                </p>
            </div>
            <?php endif; ?>

            <!-- Reviews List -->
            <div class="reviews-list">
            <?php if (!empty($reviews)): ?>
                <?php foreach ($reviews as $review): ?>
                <div class="review-card">
                    <div class="review-header">
                        <strong>
                            <?= htmlspecialchars(
                                $review['FirstName'] . ' ' .
                                $review['LastName']) ?>
                        </strong>
                        <span class="rating">
                            <?= str_repeat('★', 
                                    (int)$review['Rating']) ?>
                            <?= str_repeat('☆', 
                                    5 - (int)$review['Rating']) ?>
                        </span>
                        <small>
                            <?= date('F j, Y',
                                strtotime($review['CreatedAt'])) ?>
                        </small>
                    </div>

                    <p class="review-text">
                        <?= nl2br(htmlspecialchars(
                                      $review['Comment'])) ?>
                    </p>

                    <!-- Edit/Delete — only for review owner -->
                    <?php if (isLoggedIn() && 
                              getCurrentUserId() == 
                              $review['UserID']): ?>
                    <div class="review-actions">
                        <a href="<?= SITE_URL ?>
                                   /pages/update_review.php
                                   ?id=<?= $review['ReviewID'] ?>">
                            ✏️ Edit
                        </a>
                        <a href="<?= SITE_URL ?>
                                   /pages/delete_review.php
                                   ?id=<?= $review['ReviewID'] ?>
                                   &book_id=<?= $book['BookID'] ?>"
                           onclick="return confirm(
                               'Delete this review? Cannot be undone.')">
                            🗑️ Delete
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>

            <?php else: ?>
                <p class="no-reviews">
                    No reviews yet. Be the first to review this book!
                </p>
            <?php endif; ?>
            </div><!-- /.reviews-list -->

        </div><!-- /.reviews-section -->
    </div><!-- /.book-detail -->
</div><!-- /.container -->

<script src="../js/script.js"></script>
</body>
</html>