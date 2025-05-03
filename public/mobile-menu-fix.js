/**
 * GoFit Mobile Menu Fix
 * This script fixes issues with the mobile hamburger menu
 */
document.addEventListener('DOMContentLoaded', function() {
    // Remove all existing overlays and reset menu state on page load
    function cleanupOverlays() {
        // Remove any overlay elements
        const overlays = document.querySelectorAll('#mobile-menu-overlay, .modal-backdrop');
        overlays.forEach(overlay => overlay.remove());

        // Reset body classes that might be added by Bootstrap
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    }

    // Initial cleanup
    cleanupOverlays();

    // Get menu elements
    const navbarToggler = document.querySelector('.navbar-toggler');
    const mobileMenu = document.querySelector('.navbar-collapse');

    if (navbarToggler && mobileMenu) {
        // Track menu state
        let isProcessing = false;

        // Create mobile menu overlay
        const createOverlay = () => {
            // Always clean up first
            cleanupOverlays();

            const overlay = document.createElement('div');
            overlay.id = 'mobile-menu-overlay';
            overlay.style.position = 'fixed';
            overlay.style.top = '0';
            overlay.style.left = '0';
            overlay.style.right = '0';
            overlay.style.bottom = '0';
            overlay.style.backgroundColor = 'transparent';
            overlay.style.zIndex = '1035';
            document.body.appendChild(overlay);

            overlay.addEventListener('click', () => {
                closeMenu();
            });

            return overlay;
        };

        // Function to close the menu
        const closeMenu = () => {
            if (isProcessing) return;
            isProcessing = true;

            if (mobileMenu.classList.contains('show')) {
                cleanupOverlays();

                try {
                    // Use Bootstrap API to close the menu
                    const collapse = bootstrap.Collapse.getInstance(mobileMenu);
                    if (collapse) {
                        collapse.hide();
                    } else {
                        mobileMenu.classList.remove('show');
                    }
                } catch (error) {
                    // Fallback if Bootstrap API fails
                    console.log('Fallback close method used');
                    mobileMenu.classList.remove('show');
                }
            }

            setTimeout(() => {
                isProcessing = false;
                cleanupOverlays(); // Ensure overlays are gone
            }, 300);
        };

        // Set up toggle button
        navbarToggler.addEventListener('click', (e) => {
            e.stopPropagation();

            if (!mobileMenu.classList.contains('show')) {
                // Opening menu
                createOverlay();
            } else {
                // Closing menu
                closeMenu();
            }
        });

        // Set up all close buttons
        document.querySelectorAll('.mobile-menu-close').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                closeMenu();
            });
        });

        // Close on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeMenu();
            }
        });

        // Listen for Bootstrap events
        mobileMenu.addEventListener('hidden.bs.collapse', function () {
            cleanupOverlays(); // Ensure overlays are gone when Bootstrap hides the menu
        });

        // Backup cleanup on page unload
        window.addEventListener('beforeunload', cleanupOverlays);
    }
});
