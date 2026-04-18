/**
 * Residents Management - View Page JavaScript
 * Handles print and other view-related functions
 */

$(document).ready(function() {
    // Print resident details
    $('#printBtn').on('click', function() {
        window.print();
    });
    
    // Copy resident information to clipboard
    $('#copyInfoBtn').on('click', function() {
        var residentInfo = '';
        
        // Gather resident info
        $('.profile-username').each(function() {
            residentInfo += 'Name: ' + $(this).text() + '\n';
        });
        
        $('.list-group-item').each(function() {
            var label = $(this).find('b').text();
            var value = $(this).find('.float-right').text();
            if (label && value) {
                residentInfo += label + ': ' + value + '\n';
            }
        });
        
        // Copy to clipboard
        navigator.clipboard.writeText(residentInfo).then(function() {
            showToast('success', 'Resident information copied to clipboard!');
        }).catch(function() {
            showToast('error', 'Failed to copy information');
        });
    });
    
    // Show toast notification
    function showToast(type, message) {
        var toastClass = type === 'success' ? 'bg-success' : 'bg-danger';
        var toastHtml = `
            <div class="toast align-items-center text-white ${toastClass} border-0 position-fixed" style="top: 20px; right: 20px; z-index: 9999;" role="alert" aria-live="assertive" aria-atomic="true" data-autohide="true" data-delay="3000">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        $('.toast').remove();
        $('body').append(toastHtml);
        $('.toast').toast('show');
        
        setTimeout(function() {
            $('.toast').remove();
        }, 3000);
    }
    
    // Add print styles
    var printStyles = `
        <style media="print">
            .main-sidebar, .main-header, .footer, .breadcrumb, .btn, .nav-tabs {
                display: none !important;
            }
            .content-wrapper, .content, .card, .tab-content {
                margin: 0 !important;
                padding: 0 !important;
            }
            .profile-user-img {
                max-width: 150px !important;
            }
            body {
                background: white !important;
            }
        </style>
    `;
    $('head').append(printStyles);
});