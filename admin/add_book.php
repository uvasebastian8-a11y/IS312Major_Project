<?php
// ============================================================
//  Library System — admin/add_book.php
//  IS312 AT3 | Team: Jasmine, Sebastian & Joseph
//  ADMIN ONLY — requires admin session
// ============================================================

require_once '../includes/db.php';
require_once '../includes/auth.php';

// Only logged-in users can add books
// For IS312 purposes we check isLoggedIn()
// If you have a separate admin role, use requireAdmin() instead
requireLogin();

$errors  = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitise all inputs
    $title       = clean($conn, $_POST['title']       ?? '');
    $author      = clean($conn, $_POST['author']      ?? '');
    $category    = clean($conn, $_POST['category']    ?? '');
    $description = clean($conn, $_POST['description'] ?? '');
    $image       = clean($conn, $_POST['image']       ?? '');

    // Validation
    if (!$title)    $errors[] = 'Book title is required.';
    if (!$author)   $errors[] = 'Author name is required.';
    if (!$category) $errors[] = 'Category is required.';

    if (empty($errors)) {
        // Use prepared statement — safe from SQL injection
        $stmt = $conn->prepare(
            "INSERT INTO Books 
             (Title, Author, Category, Description, Image)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            'sssss',
            $title, $author, $category, $description, $image
        );

        if ($stmt->execute()) {
            setFlash('success',
                '✅ Book "' . $title . '" added successfully!');
            redirect(SITE_URL . '/index.php');
        } else {
            $errors[] = 'Failed to add book. Please try again.';
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Book | <?= SITE_NAME ?></title>
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

<!-- ── Page Content ────────────────────────────────────────── -->
<div class="container">
    <?= showFlash() ?>

    <div class="auth-box" style="max-width:600px;margin:40px auto;">
        <h2>📚 Add New Book</h2>
        <p style="color:#666;margin-bottom:20px;">
            Fill in the details below to add a new book 
            to the library.
        </p>

        <!-- Validation Errors -->
        <?php if (!empty($errors)): ?>
        <div class="alert error">
            <strong>Please fix the following:</strong>
            <ul style="margin:8px 0 0 20px;">
                <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Add Book Form -->
        <form method="POST" action="">

            <label>Book Title *</label>
            <input type="text" name="title"
                   placeholder="e.g. The Great Gatsby"
                   required
                   value="<?= htmlspecialchars(
                                  $_POST['title'] ?? '') ?>">

            <label>Author *</label>
            <input type="text" name="author"
                   placeholder="e.g. F. Scott Fitzgerald"
                   required
                   value="<?= htmlspecialchars(
                                  $_POST['author'] ?? '') ?>">

            <label>Category *</label>
            <select name="category" required>
                <option value="">-- Select Category --</option>
                <?php
                $categories = [
                    'Fiction', 'Non-Fiction', 'Science',
                    'History', 'Biography', 'Technology',
                    'Self-Help', 'Mystery', 'Romance', 'Other'
                ];
                foreach ($categories as $cat):
                    $sel = (($_POST['category'] ?? '') === $cat)
                           ? 'selected' : '';
                ?>
                <option value="<?= $cat ?>" <?= $sel ?>>
                    <?= $cat ?>
                </option>
                <?php endforeach; ?>
            </select>

            <label>Description</label>
            <textarea name="description" rows="4"
                      placeholder="Brief description of the book…"
                      ><?= htmlspecialchars(
                             $_POST['description'] ?? '') ?></textarea>

            <label>Image Filename</label>
            <input type="text" name="image"
                   placeholder="e.g. gatsby.jpg"
                   value="<?= htmlspecialchars(
                                  $_POST['image'] ?? '') ?>">
            <small style="color:#999;display:block;margin-top:-10px;
                          margin-bottom:14px;">
                Place image file in the 
                <code>images/</code> folder first.
            </small>

            <button type="submit" class="btn">
                📚 Add Book to Library
            </button>
        </form>

        <p style="margin-top:20px;">
            <a href="<?= SITE_URL ?>/index.php">
                ← Back to Library
            </a>
        </p>
    </div>
</div>

<script src="../js/script.js"></script>
</body>
</html>