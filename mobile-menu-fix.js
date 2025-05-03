/**
 * GoFit Mobile Menu Fix
 * This script fixes issues with the mobile hamburger menu
 */
document.addEventListener('DOMContentLoaded', function() {
    // Perform aggressive cleanup of all modal-related elements
    function forceCleanup() {
        // Remove all overlays and backdrop elements
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

        // Force remove any body classes added by Bootstrap
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    }

    // Run cleanup immediately and after a short delay
    forceCleanup();
    setTimeout(forceCleanup, 100);

    // Get menu elements
    const navbarToggler = document.querySelector('.navbar-toggler');
    const mobileMenu = document.querySelector('.navbar-collapse');

    // Function to close the mobile menu
    function closeMobileMenu() {
        if (mobileMenu) {
            mobileMenu.classList.remove('show');
            mobileMenu.style.transform = 'translateX(-100%)';
            forceCleanup();
        }
    }

    if (navbarToggler && mobileMenu) {
        // Directly handle toggler clicks without relying on Bootstrap
        navbarToggler.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            // Check current state
            if (mobileMenu.classList.contains('show')) {
                // Close menu
                closeMobileMenu();
            } else {
                // Open menu
                forceCleanup(); // Always clean first
                mobileMenu.classList.add('show');
                // Ensure the menu is visible with transform
                mobileMenu.style.transform = 'translateX(0)';
            }
        });

        // Add listener for the close button
        const closeButton = document.querySelector('.btn-close');
        if (closeButton) {
            closeButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                closeMobileMenu();
            });
        }

        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (mobileMenu.classList.contains('show') &&
                !mobileMenu.contains(e.target) &&
                e.target !== navbarToggler &&
                !navbarToggler.contains(e.target)) {

                closeMobileMenu();
            }
        });

        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && mobileMenu.classList.contains('show')) {
                closeMobileMenu();
            }
        });
    }

    // Cleanup on page changes/navigation
    window.addEventListener('beforeunload', forceCleanup);

    // Backup cleanup every few seconds just to be safe
    const intervalCleanup = setInterval(function() {
        // Only run cleanup if menu is not showing
        const menu = document.querySelector('.navbar-collapse');
        if (menu && !menu.classList.contains('show')) {
            forceCleanup();
        }
    }, 2000);

    // Clear interval when page is unloaded
    window.addEventListener('beforeunload', function() {
        clearInterval(intervalCleanup);
    });
});
