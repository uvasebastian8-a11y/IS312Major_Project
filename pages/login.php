<?php
// ============================================================
//  Library System — pages/login.php
//  IS312 AT3 | Team: Jasmine, Sebastian & Joseph
// ============================================================

require_once '../includes/db.php';
require_once '../includes/auth.php';

// If already logged in, go to dashboard
if (isLoggedIn()) {
    redirect(SITE_URL . '/pages/dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitise inputs
    $email    = filter_var(trim($_POST['email']    ?? ''), 
                           FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    // Basic validation
    if (!$email || !$password) {
        $error = 'Please enter your email and password.';

    } else {
        // Look up user by email only
        $stmt = $conn->prepare(
            "SELECT * FROM Users WHERE Email = ? LIMIT 1"
        );
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        // Verify hashed password
        if ($user && password_verify($password, $user['Password'])) {

            // Set session variables — consistent key names
            $_SESSION['customer_id']    = $user['UserID'];
            $_SESSION['customer_name']  = $user['FirstName'] . ' ' 
                                        . $user['LastName'];
            $_SESSION['customer_email'] = $user['Email'];

            // Redirect to intended page or dashboard
            $dest = $_SESSION['redirect_after_login'] 
                    ?? SITE_URL . '/pages/dashboard.php';
            unset($_SESSION['redirect_after_login']);

            setFlash('success', 
                'Welcome back, ' . $user['FirstName'] . '! 👋');
            redirect($dest);

        } else {
            // Vague message — don't reveal if email exists
            $error = 'Invalid email or password. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="auth-container">
    <div class="auth-box">
        <h2>Login to <?= SITE_NAME ?></h2>

        <?= showFlash() ?>

        <?php if ($error): ?>
            <div class="alert error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="email" name="email"
                   placeholder="Email Address" required
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

            <input type="password" name="password"
                   placeholder="Password" required>

            <button type="submit">Login</button>
        </form>

        <p>Don't have an account?
            <a href="<?= SITE_URL ?>/pages/register.php">
                Register here
            </a>
        </p>
    </div>
</div>
</body>
</html>