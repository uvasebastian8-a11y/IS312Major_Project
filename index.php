<?php
// ============================================================
//  Library System — index.php (ROOT level)
//  IS312 AT3 | Team: Jasmine, Sebastian & Joseph
// ============================================================

require_once 'includes/db.php';
require_once 'includes/auth.php';

// ── Book image helper ─────────────────────────────────────────
function getBookImagePath(array $book): string {
    $title_image_map = [
        'gatsby'      => ['file' => 'gatsby.jpg',      
                          'keywords' => ['gatsby', 'great gatsby']],
        'mockingbird' => ['file' => 'mockingbird.jpg', 
                          'keywords' => ['mockingbird', 
                                         'to kill a mockingbird']],
        '1984'        => ['file' => '1984.jpg',        
                          'keywords' => ['1984', 
                                         'nineteen eighty-four']],
        'alchemist'   => ['file' => 'alchemist.jpg',   
                          'keywords' => ['alchemist', 
                                         'the alchemist']],
        'atomic'      => ['file' => 'atomic.jpg',      
                          'keywords' => ['atomic', 'atomic habits']],
        'power'       => ['file' => 'power_of_now.jpg',
                          'keywords' => ['power of now',
                                     'power']],
    ];

    // Check database image field first
    if (!empty($book['Image'])) {
        if (file_exists('images/' . $book['Image']))
            return 'images/' . $book['Image'];
    }

    // Match by title keywords
    $title = strtolower($book['Title']);
    foreach ($title_image_map as $data) {
        foreach ($data['keywords'] as $keyword) {
            if (strpos($title, $keyword) !== false) {
                $path = 'images/' . $data['file'];
                if (file_exists($path)) return $path;
            }
        }
    }

    return '';
}

// ── Fetch all books with ratings ──────────────────────────────
$sql = "SELECT b.*,
        ROUND(AVG(r.Rating), 1)  AS avg_rating,
        COUNT(r.ReviewID)        AS review_count
        FROM Books b
        LEFT JOIN Reviews r ON b.BookID = r.BookID
        GROUP BY b.BookID
        ORDER BY b.Title ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- ── Navbar ──────────────────────────────────────────────── -->
<nav>
    <div class="nav-container">
        <h1>📚 <?= SITE_NAME ?></h1>
        <ul>
            <li><a href="<?= SITE_URL ?>/index.php">Home</a></li>
            <li><a href="<?= SITE_URL ?>/index.php#books">Books</a></li>
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

<!-- ── Flash Message ───────────────────────────────────────── -->
<div class="container">
    <?= showFlash() ?>

    <!-- Add this in index.php after showFlash() -->
<?php if (isset($_GET['msg']) && $_GET['msg'] === 'loggedout'): ?>
    <div class="alert alert-success">
        ✅ You have been logged out successfully. 
        See you next time!
    </div>
<?php endif; ?>
</div>

<!-- ── Hero ────────────────────────────────────────────────── -->
<div class="container">
    <div class="hero">
        <h2>Welcome to <?= SITE_NAME ?></h2>
        <p>Discover amazing books and share your reviews</p>
        <?php if (!isLoggedIn()): ?>
            <a href="<?= SITE_URL ?>/pages/register.php" 
               class="btn">
                Join Now — It's Free
            </a>
        <?php endif; ?>
    </div>

    <!-- ── Books Grid ──────────────────────────────────────── -->
    <div class="books-section" id="books">
        <h2>📖 All Books</h2>
        <div class="books-grid">

        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($book = $result->fetch_assoc()): ?>
                <?php $img = getBookImagePath($book); ?>
                <div class="book-card">

                    <!-- Cover Image -->
                    <div class="book-cover">
                        <?php if ($img): ?>
                            <img src="<?= htmlspecialchars($img) ?>"
                                 alt="<?= htmlspecialchars(
                                            $book['Title']) ?>"
                                 loading="lazy"
                                 onerror="this.onerror=null;
                                          this.parentElement
                                          .innerHTML=
                                          '<div class=\'no-image\'>
                                           📖</div>'">
                        <?php else: ?>
                            <div class="no-image">📖</div>
                        <?php endif; ?>
                    </div>

                    <!-- Book Info -->
                    <h3><?= htmlspecialchars($book['Title']) ?></h3>
                    <p class="author">
                        by <?= htmlspecialchars($book['Author']) ?>
                    </p>
                    <p class="category">
                        <?= htmlspecialchars($book['Category']) ?>
                    </p>
                    <p class="description">
                        <?= htmlspecialchars(
                               substr($book['Description'], 0, 100)) 
                        ?>…
                    </p>

                    <!-- Star Rating -->
                    <?php if ($book['review_count'] > 0): ?>
                    <div class="book-rating">
                        <span class="stars">
                            <?php
                            $avg  = (float)$book['avg_rating'];
                            $full = (int)floor($avg);
                            echo str_repeat('★', $full);
                            echo str_repeat('☆', 5 - $full);
                            ?>
                        </span>
                        <small>
                            (<?= $book['review_count'] ?> 
                            review<?= $book['review_count'] != 1 
                                       ? 's' : '' ?>)
                        </small>
                    </div>
                    <?php else: ?>
                    <div class="book-rating">
                        <small class="no-reviews">
                            No reviews yet
                        </small>
                    </div>
                    <?php endif; ?>

                    <a href="<?= SITE_URL ?>/pages/book_details.php
                               ?id=<?= $book['BookID'] ?>" 
                       class="btn">
                        View Details &amp; Reviews
                    </a>
                </div>
            <?php endwhile; ?>

        <?php else: ?>
            <div class="no-books">
                <p>📭 No books found in the library yet.</p>
                <?php if (isLoggedIn()): ?>
                <p>
                    <a href="<?= SITE_URL ?>/admin/add_book.php" 
                       class="btn">
                        Add First Book
                    </a>
                </p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        </div><!-- /.books-grid -->
    </div><!-- /.books-section -->
</div><!-- /.container -->

<script src="js/script.js"></script>
</body>
</html>
<?php require_once 'includes/footer.php'; ?>