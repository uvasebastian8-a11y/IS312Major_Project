// ============================================================
//  Library System — js/script.js
//  IS312 AT3 | Team: Jasmine, Sebastian & Joseph
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

    // ── 1. Auto-dismiss alert/flash messages ─────────────────
    // Alerts from showFlash() disappear after 4 seconds
    var alerts = document.querySelectorAll(
        '.alert-success, .alert-danger, .success, .error'
    );
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity    = '0';
            setTimeout(function () {
                alert.remove();
            }, 500);
        }, 4000);
    });

    // ── 2. Confirm delete — all delete links ─────────────────
    // Catches any link that goes to delete_review.php
    var deleteLinks = document.querySelectorAll(
        'a[href*="delete_review.php"]'
    );
    deleteLinks.forEach(function (link) {
        link.addEventListener('click', function (e) {
            var confirmed = confirm(
                'Are you sure you want to delete this review?\n' +
                'This action cannot be undone.'
            );
            if (!confirmed) {
                e.preventDefault();
            }
        });
    });

    // ── 3. Active nav link highlighter ───────────────────────
    // Adds 'active' class to current page nav link
    var currentPage = window.location.pathname
                           .split('/').pop();
    var navLinks = document.querySelectorAll('nav ul li a');
    navLinks.forEach(function (link) {
        var linkPage = link.getAttribute('href')
                           .split('/').pop()
                           .split('?')[0];
        if (linkPage === currentPage) {
            link.classList.add('active');
        }
    });

    // ── 4. Book filter dropdown (reviews.php) ────────────────
    // Only runs if the filter dropdown exists on the page
    var bookFilter = document.getElementById('book_filter');
    if (bookFilter) {
        bookFilter.addEventListener('change', function () {
            var id  = this.value;
            // Get base URL without query string
            var url = window.location.href.split('?')[0];
            window.location.href = id > 0
                ? url + '?book_id=' + id
                : url;
        });
    }

    // ── 5. Search box (only if it exists on the page) ────────
    // Safely checks element exists before adding listener
    var searchInput = document.querySelector(
        '.search-box input'
    );
    if (searchInput) {
        searchInput.addEventListener('keyup', function (e) {
            var query = e.target.value.toLowerCase();
            // Filter book cards by title in real time
            var bookCards = document.querySelectorAll(
                '.book-card'
            );
            bookCards.forEach(function (card) {
                var title = card.querySelector('h3')
                                .textContent.toLowerCase();
                card.style.display = title.includes(query)
                    ? 'block'
                    : 'none';
            });
        });
    }

    // ── 6. Smooth scroll for anchor links ────────────────────
    // Handles index.php#books link in navbar
    var anchorLinks = document.querySelectorAll('a[href*="#"]');
    anchorLinks.forEach(function (link) {
        link.addEventListener('click', function (e) {
            var targetId = this.getAttribute('href')
                               .split('#')[1];
            var target   = document.getElementById(targetId);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block:    'start'
                });
            }
        });
    });

    // ── 7. Form submit loading state ─────────────────────────
    // Prevents double submission on all forms
    var forms = document.querySelectorAll('form');
    forms.forEach(function (form) {
        form.addEventListener('submit', function () {
            var submitBtn = form.querySelector(
                'button[type="submit"]'
            );
            if (submitBtn) {
                submitBtn.disabled    = true;
                submitBtn.textContent = 'Please wait…';
            }
        });
    });

});