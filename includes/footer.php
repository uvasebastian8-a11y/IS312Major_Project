<?php
// ============================================================
//  Library System — includes/footer.php
//  IS312 AT3 | Team: Jasmine, Sebastian & Joseph
//  Divine Word University
// ============================================================
?>

<style>
    /* ===== PROFESSIONAL FOOTER STYLES ===== */
    .site-footer {
        background: transparent; /* dark background image shows through */
        color: white;
        font-family: 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
        margin-top: 3rem;
        padding: 2rem 0 0;
        border-top: 1px solid rgba(255,255,255,0.15);
    }
    .footer-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 2rem;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1.5rem 2rem;
    }
    .footer-col {
        line-height: 1.6;
    }
    .footer-brand {
        font-size: 1.8rem;
        margin: 0 0 0.5rem;
        letter-spacing: -0.5px;
    }
    .footer-about, .footer-tagline {
        font-size: 0.9rem;
        opacity: 0.9;
    }
    .footer-tagline {
        font-style: italic;
        margin-top: 0.5rem;
    }
    .footer-heading {
        font-size: 1.2rem;
        margin: 0 0 1rem;
        border-left: 3px solid #f5b042;
        padding-left: 0.75rem;
    }
    .footer-links, .footer-contact {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .footer-links li, .footer-contact li {
        margin-bottom: 0.5rem;
    }
    .footer-links a {
        color: #f0f0f0;
        text-decoration: none;
        transition: color 0.2s;
    }
    .footer-links a:hover {
        color: #ffdd88;
        text-decoration: underline;
    }
    .footer-icon {
        display: inline-block;
        width: 1.5rem;
        filter: brightness(0) invert(1);
        opacity: 0.8;
    }
    .footer-bottom {
        background: rgba(0,0,0,0.5);
        text-align: center;
        padding: 1rem;
        font-size: 0.8rem;
        border-top: 1px solid rgba(255,255,255,0.1);
    }
    .footer-bottom-inner p {
        margin: 0.25rem 0;
    }
    @media (max-width: 768px) {
        .footer-container {
            grid-template-columns: 1fr;
            text-align: center;
        }
        .footer-heading {
            border-left: none;
            border-bottom: 1px solid rgba(255,255,255,0.3);
            padding-bottom: 0.5rem;
        }
        .footer-icon {
            width: 1.8rem;
        }
    }
</style>

<footer class="site-footer">
    <div class="footer-container">

        <!-- Column 1: Brand -->
        <div class="footer-col">
            <h3 class="footer-brand">
                📚 <?= SITE_NAME ?>
            </h3>
            <p class="footer-about">
                Your online guide to discovering
                great books and sharing reading
                experiences across Papua New Guinea.
            </p>
            <p class="footer-tagline">
                Read. Review. Inspire.
            </p>
        </div>

        <!-- Column 2: Quick Links -->
        <div class="footer-col">
            <h4 class="footer-heading">Quick Links</h4>
            <ul class="footer-links">
                <li><a href="<?= SITE_URL ?>/index.php">🏠 Home</a></li>
                <li><a href="<?= SITE_URL ?>/pages/reviews.php">📝 All Reviews</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="<?= SITE_URL ?>/pages/dashboard.php">👤 My Dashboard</a></li>
                    <li><a href="<?= SITE_URL ?>/admin/add_book.php">➕ Add a Book</a></li>
                    <li><a href="<?= SITE_URL ?>/pages/logout.php">🚪 Logout</a></li>
                <?php else: ?>
                    <li><a href="<?= SITE_URL ?>/pages/login.php">🔑 Login</a></li>
                    <li><a href="<?= SITE_URL ?>/pages/register.php">📋 Register</a></li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Column 3: Contact Us -->
        <div class="footer-col">
            <h4 class="footer-heading">Contact Us</h4>
            <ul class="footer-contact">
                <li><span class="footer-icon">📍</span> Divine Word University<br>Madang, Papua New Guinea</li>
                <li><span class="footer-icon">📧</span> library@dwu.ac.pg</li>
                <li><span class="footer-icon">📞</span> +675 422 0000</li>
                <li><span class="footer-icon">🕐</span> Mon – Fri: 8:00am – 5:00pm</li>
            </ul>
        </div>

        <!-- Column 4: About Project -->
        <div class="footer-col">
            <h4 class="footer-heading">About Project</h4>
            <ul class="footer-contact">
                <li><span class="footer-icon">🎓</span> IS312 Web Application<br>Development</li>
                <li><span class="footer-icon">👥</span> Jasmine<br>Sebastian<br>Joseph</li>
                <li><span class="footer-icon">📅</span> Semester 1, 2026</li>
            </ul>
        </div>

    </div><!-- /.footer-container -->

    <!-- Bottom Bar -->
    <div class="footer-bottom">
        <div class="footer-bottom-inner">
            <p>
                &copy; <?= date('Y') ?> <?= SITE_NAME ?> — All rights reserved.
            </p>
            <p>
                Built by <strong>Jasmine, Sebastian & Joseph</strong> |
                IS312 Web Application Development |
                Divine Word University
            </p>
        </div>
    </div>
</footer>

<script src="<?= SITE_URL ?>/js/script.js"></script>
</body>
</html>