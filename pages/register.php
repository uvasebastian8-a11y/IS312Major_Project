<?php
// ============================================================
//  Library System — pages/register.php
//  IS312 AT3 | Team: Jasmine, Sebastian & Joseph
// ============================================================

require_once '../includes/db.php';
require_once '../includes/auth.php';

// If already logged in go to dashboard
if (isLoggedIn()) {
    redirect(SITE_URL . '/pages/dashboard.php');
}

$errors  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitise all inputs
    $firstname  = clean($conn, $_POST['firstname']  ?? '');
    $lastname   = clean($conn, $_POST['lastname']   ?? '');
    $email      = filter_var(
                      trim($_POST['email'] ?? ''),
                      FILTER_SANITIZE_EMAIL);
    $password   = $_POST['password']  ?? '';
    $password2  = $_POST['password2'] ?? '';
    $gender     = clean($conn, $_POST['gender']     ?? '');
    $address    = clean($conn, $_POST['address']    ?? '');
    $postalcode = clean($conn, $_POST['postalcode'] ?? '');
    $postoffice = clean($conn, $_POST['postoffice'] ?? '');
    $contact    = clean($conn, $_POST['contact']    ?? '');

    // Validation
    if (!$firstname)
        $errors[] = 'First name is required.';
    if (!$lastname)
        $errors[] = 'Last name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors[] = 'A valid email address is required.';
    if (strlen($password) < 6)
        $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $password2)
        $errors[] = 'Passwords do not match.';
    if (!in_array($gender, ['Male','Female','Other']))
        $errors[] = 'Please select a valid gender.';
    if (!$address)
        $errors[] = 'Address is required.';
    if (!$contact)
        $errors[] = 'Contact number is required.';

    // Check unique email
    if (empty($errors)) {
        $chk = $conn->prepare(
            "SELECT UserID FROM users WHERE Email = ?"
        );
        $chk->bind_param('s', $email);
        $chk->execute();
        $chk->store_result();

        if ($chk->num_rows > 0) {
            $errors[] = 'This email is already registered.
                         Please login instead.';
        }
        $chk->close();
    }

    // Insert new user
    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare(
            "INSERT INTO users
             (FirstName, LastName, Email, Password, Gender,
              Address, PostalCode, PostOfficeNumber, Contact)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        if ($stmt === false) {
            $errors[] = 'System error: ' . $conn->error;
        } else {
            $stmt->bind_param(
                'sssssssss',
                $firstname, $lastname, $email, $hashed,
                $gender, $address, $postalcode,
                $postoffice, $contact
            );

            if ($stmt->execute()) {
                setFlash('success',
                    '✅ Registration successful! 
                     Please login.');
                redirect(SITE_URL . '/pages/login.php');
            } else {
                $errors[] = 'Registration failed. 
                             Please try again.';
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" 
          content="width=device-width, initial-scale=1.0">
    <title>Register | <?= SITE_NAME ?></title>
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
                <a href="<?= SITE_URL ?>/pages/login.php">
                    Login
                </a>
            </li>
            <li>
                <a href="<?= SITE_URL ?>/pages/register.php"
                   class="active">
                    Register
                </a>
            </li>
        </ul>
    </div>
</nav>

<!-- ── Register Form ───────────────────────────────────────── -->
<div class="auth-container">
    <div class="auth-box" style="max-width:520px;">
        <h2>📝 Create Account</h2>
        <p class="subtitle">
            Join <?= SITE_NAME ?> to write book reviews
        </p>

        <!-- Show errors -->
        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <strong>Please fix the following:</strong>
            <ul style="margin:8px 0 0 18px;">
                <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="POST" action="">

            <label>First Name *</label>
            <input type="text" name="firstname"
                   placeholder="e.g. John" required
                   value="<?= htmlspecialchars(
                                  $_POST['firstname'] ?? '') ?>">

            <label>Last Name *</label>
            <input type="text" name="lastname"
                   placeholder="e.g. Doe" required
                   value="<?= htmlspecialchars(
                                  $_POST['lastname'] ?? '') ?>">

            <label>Gender *</label>
            <select name="gender" required>
                <option value="">-- Select Gender --</option>
                <?php
                foreach (['Male','Female','Other'] as $g):
                    $sel = (($_POST['gender'] ?? '') === $g)
                           ? 'selected' : '';
                ?>
                <option value="<?= $g ?>" <?= $sel ?>>
                    <?= $g ?>
                </option>
                <?php endforeach; ?>
            </select>

            <label>Address *</label>
            <input type="text" name="address"
                   placeholder="e.g. Madang Town" required
                   value="<?= htmlspecialchars(
                                  $_POST['address'] ?? '') ?>">

            <label>Postal Code</label>
            <input type="text" name="postalcode"
                   placeholder="e.g. 511"
                   value="<?= htmlspecialchars(
                                  $_POST['postalcode'] ?? '') ?>">

            <label>Post Office Number</label>
            <input type="text" name="postoffice"
                   placeholder="e.g. PO Box 61"
                   value="<?= htmlspecialchars(
                                  $_POST['postoffice'] ?? '') ?>">

            <label>Contact Number *</label>
            <input type="text" name="contact"
                   placeholder="e.g. 70000001" required
                   value="<?= htmlspecialchars(
                                  $_POST['contact'] ?? '') ?>">

            <label>Email Address *</label>
            <input type="email" name="email"
                   placeholder="you@example.com" required
                   value="<?= htmlspecialchars(
                                  $_POST['email'] ?? '') ?>">

            <label>Password *</label>
            <input type="password" name="password"
                   placeholder="Min. 6 characters" required>

            <label>Confirm Password *</label>
            <input type="password" name="password2"
                   placeholder="Repeat password" required>

            <button type="submit">
                Create My Account
            </button>
        </form>

        <p>Already have an account?
            <a href="<?= SITE_URL ?>/pages/login.php">
                Login here
            </a>
        </p>
    </div>
</div>

<script src="../js/script.js"></script>
</body>
</html>