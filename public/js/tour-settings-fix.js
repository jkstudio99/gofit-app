/**
 * Tour Settings Enhancement
 * Replace default browser alerts/confirmations with SweetAlert
 * Add consistent styling to tour settings page
 */

document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on the tour settings page
    if (window.location.pathname.includes('/tour/settings')) {
        console.log('Tour settings enhancement loaded');

        // Override browser confirmations
        setupConfirmationOverrides();

        // Add event listeners to reset buttons
        setupResetButtons();
    }

    // Inject CSRF token meta if not present
    ensureCsrfToken();
});

/**
 * Ensure CSRF token is available
 */
function ensureCsrfToken() {
    if (!document.querySelector('meta[name="csrf-token"]')) {
        console.log('CSRF token meta tag not found, creating one');
        // Create a meta tag for CSRF token with a fallback empty value
        // This will prevent null reference errors, though the request may still fail
        const metaTag = document.createElement('meta');
        metaTag.name = 'csrf-token';
        metaTag.content = '';
        document.head.appendChild(metaTag);
    }
}

/**
 * Setup SweetAlert confirmations for reset actions
 */
function setupResetButtons() {
    // Find reset all tours button
    const resetAllButton = document.getElementById('reset-all-tours');

    if (resetAllButton) {
        resetAllButton.addEventListener('click', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'ยืนยันการรีเซ็ต',
                html: 'คุณแน่ใจหรือที่จะการแนะนำการใช้งานทั้งหมดอีกครั้ง?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2DC679',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'ตกลง',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Clear all tour localStorage items immediately
                    clearAllTourLocalStorage();

                    // ตรวจสอบว่ามี CSRF token หรือไม่
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');

                    if (!csrfToken || !csrfToken.getAttribute('content')) {
                        console.log('No valid CSRF token found, relying on local storage only');
                        // ไม่มี CSRF token ที่ถูกต้อง ให้เสร็จสิ้นโดยใช้ localStorage เท่านั้น
                        handleResetSuccess();
                        return;
                    }

                    // Use XMLHttpRequest with proper error handling for CSRF issues
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', '/tour/reset', true);
                    xhr.setRequestHeader('Content-Type', 'application/json');
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken.getAttribute('content'));
                    xhr.setRequestHeader('Accept', 'application/json');

                    // Set timeout to prevent hanging
                    xhr.timeout = 3000; // 3 seconds

                    xhr.onload = function() {
                        if (xhr.status === 419) {
                            // กรณี CSRF token หมดอายุ (Laravel specific)
                            console.log('CSRF token expired or invalid, but reset done locally');
                            handleResetSuccess();
                        } else if (xhr.status >= 200 && xhr.status < 300) {
                            // Success case
                            try {
                                const data = JSON.parse(xhr.responseText);
                                if (data.success) {
                                    Swal.fire({
                                        title: 'สำเร็จ!',
                                        text: 'รีเซ็ตการแนะนำการใช้งานทั้งหมดเรียบร้อยแล้ว',
                                        icon: 'success',
                                        confirmButtonColor: '#2DC679',
                                        confirmButtonText: 'ตกลง'
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    // Server responded but indicated failure
                                    console.warn('Server indicated failure but reset done locally');
                                    handleResetSuccess();
                                }
                            } catch (e) {
                                console.warn('Could not parse server response');
                                handleResetSuccess();
                            }
                        } else {
                            // Other HTTP errors
                            console.warn(`Server returned status ${xhr.status}, but reset done locally`);
                            handleResetSuccess();
                        }
                    };

                    xhr.ontimeout = function() {
                        console.warn('Request timed out, but reset done locally');
                        handleResetSuccess();
                    };

                    xhr.onerror = function() {
                        console.warn('Network error, but reset done locally');
                        handleResetSuccess();
                    };

                    // Send the request
                    try {
                        xhr.send();
                    } catch (e) {
                        console.warn('Error sending request:', e);
                        handleResetSuccess();
                    }
                }
            });
        });
    }
}

/**
 * Handle success even if the server request fails
 * This ensures a good user experience even with server issues
 */
function handleResetSuccess() {
    Swal.fire({
        title: 'รีเซ็ตแล้ว',
        text: 'รีเซ็ตการแนะนำในอุปกรณ์นี้เรียบร้อยแล้ว',
        icon: 'success',
        confirmButtonColor: '#2DC679',
        confirmButtonText: 'ตกลง'
    }).then(() => {
        window.location.reload();
    });
}

/**
 * Clear all tour-related localStorage items
 */
function clearAllTourLocalStorage() {
    // Get all localStorage keys
    const keys = Object.keys(localStorage);

    // Find and remove all tour-related localStorage items
    keys.forEach(key => {
        if (key.startsWith('tour_') && (key.endsWith('_skipped') || key.endsWith('_completed'))) {
            localStorage.removeItem(key);
        }
    });

    console.log('All tour localStorage items cleared');
}

/**
 * Override default browser confirm/alert dialogs
 */
function setupConfirmationOverrides() {
    // Store the original window.confirm
    const originalConfirm = window.confirm;

    // Override window.confirm to use SweetAlert
    window.confirm = function(message) {
        // If already using SweetAlert, don't override
        if (document.querySelector('.swal2-container')) {
            return originalConfirm(message);
        }

        return new Promise((resolve) => {
            Swal.fire({
                title: 'ยืนยัน',
                html: message,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2DC679',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'ตกลง',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                resolve(result.isConfirmed);
            });
        });
    };

    // Store the original window.alert
    const originalAlert = window.alert;

    // Override window.alert to use SweetAlert
    window.alert = function(message) {
        // If already using SweetAlert, don't override
        if (document.querySelector('.swal2-container')) {
            return originalAlert(message);
        }

        Swal.fire({
            title: 'แจ้งเตือน',
            html: message,
            icon: 'info',
            confirmButtonColor: '#2DC679',
            confirmButtonText: 'ตกลง'
        });
    };
}
