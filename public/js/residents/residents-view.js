/**
 * Residents View JavaScript
 * Handles resident profile view actions
 * File location: public/js/residents/residents-view.js
 */

(function() {
    'use strict';
    
    // Configuration
    const config = {
        baseUrl: BASE_URL || '',
        csrfName: CSRF_TOKEN_NAME || '',
        csrfValue: CSRF_TOKEN_VALUE || '',
        residentId: typeof RESIDENT_ID !== 'undefined' ? RESIDENT_ID : '',
        residentName: typeof RESIDENT_NAME !== 'undefined' ? RESIDENT_NAME : ''
    };
    
    /**
     * Initialize the page
     */
    function init() {
        bindEvents();
        initializeTabs();
    }
    
    /**
     * Bind event listeners
     */
    function bindEvents() {
        // Tab persistence
        $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', handleTabShown);
        
        // Print button
        $(document).on('click', '.print-profile', handlePrint);
        
        // Delete button
        $(document).on('click', '.delete-resident', handleDelete);
        
        // Edit button
        $(document).on('click', '.edit-resident', handleEdit);
    }
    
    /**
     * Initialize tabs from URL hash
     */
    function initializeTabs() {
        const hash = window.location.hash;
        if (hash) {
            $(`a[href="${hash}"]`).tab('show');
        }
    }
    
    /**
     * Handle tab shown event
     * @param {Event} e - Tab shown event
     */
    function handleTabShown(e) {
        const tabId = e.target.hash;
        window.location.hash = tabId;
    }
    
    /**
     * Handle print action
     * @param {Event} e - Click event
     */
    function handlePrint(e) {
        e.preventDefault();
        window.print();
    }
    
    /**
     * Handle edit action
     * @param {Event} e - Click event
     */
    function handleEdit(e) {
        e.preventDefault();
        window.location.href = `${config.baseUrl}resident/edit/${config.residentId}`;
    }
    
    /**
     * Handle delete action
     * @param {Event} e - Click event
     */
    function handleDelete(e) {
        e.preventDefault();
        confirmDelete();
    }
    
    /**
     * Confirm and execute delete
     */
    function confirmDelete() {
        const options = {
            title: 'Delete Resident?',
            text: `Are you sure you want to delete ${config.residentName}? This action cannot be undone!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        };
        
        if (typeof Swal !== 'undefined') {
            Swal.fire(options).then((result) => {
                if (result.isConfirmed) {
                    executeDelete();
                }
            });
        } else {
            if (confirm(`Are you sure you want to delete ${config.residentName}?`)) {
                executeDelete();
            }
        }
    }
    
    /**
     * Execute delete AJAX request
     */
    function executeDelete() {
        const data = {};
        data[config.csrfName] = config.csrfValue;
        
        $.ajax({
            url: `${config.baseUrl}resident/delete/${config.residentId}`,
            type: 'POST',
            data: data,
            dataType: 'json',
            beforeSend: function() {
                showLoading(true);
            },
            success: function(response) {
                handleDeleteSuccess(response);
            },
            error: function(xhr, status, error) {
                handleDeleteError(xhr, status, error);
            },
            complete: function() {
                showLoading(false);
            }
        });
    }
    
    /**
     * Handle successful delete response
     * @param {Object} response - API response
     */
    function handleDeleteSuccess(response) {
        if (response.status === 'success') {
            showNotification('Resident deleted successfully', 'success');
            
            setTimeout(function() {
                window.location.href = `${config.baseUrl}resident`;
            }, 1500);
        } else {
            showNotification(response.message || 'Failed to delete resident', 'error');
        }
    }
    
    /**
     * Handle delete error
     * @param {Object} xhr - XHR object
     * @param {string} status - Error status
     * @param {string} error - Error message
     */
    function handleDeleteError(xhr, status, error) {
        console.error('Delete Error:', status, error);
        showNotification('An error occurred while deleting', 'error');
    }
    
    /**
     * Generate certificate
     */
    function generateCertificate() {
        const options = {
            title: 'Generate Certificate',
            text: `Select certificate type for ${config.residentName}`,
            input: 'select',
            inputOptions: {
                'barangay_clearance': 'Barangay Clearance',
                'indigency': 'Certificate of Indigency',
                'residency': 'Certificate of Residency',
                'good_moral': 'Certificate of Good Moral'
            },
            inputPlaceholder: 'Select certificate type',
            showCancelButton: true,
            confirmButtonText: 'Generate',
            cancelButtonText: 'Cancel'
        };
        
        if (typeof Swal !== 'undefined') {
            Swal.fire(options).then((result) => {
                if (result.isConfirmed) {
                    const certType = result.value;
                    window.location.href = `${config.baseUrl}certificates/generate/${config.residentId}/${certType}`;
                }
            });
        } else {
            showNotification('Certificate generation feature coming soon!', 'info');
        }
    }
    
    /**
     * Show/hide loading state
     * @param {boolean} isLoading - Whether loading is active
     */
    function showLoading(isLoading) {
        if (isLoading) {
            $('body').addClass('loading');
        } else {
            $('body').removeClass('loading');
        }
    }
    
    /**
     * Show notification message
     * @param {string} message - Message to display
     * @param {string} type - Notification type
     */
    function showNotification(message, type = 'info') {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                text: message,
                icon: type,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        } else {
            alert(message);
        }
    }
    
    // Initialize when DOM is ready
    $(document).ready(init);
    
    // Expose public methods
    window.ResidentsView = {
        printProfile: function() {
            window.print();
        },
        generateCertificate: generateCertificate,
        deleteResident: confirmDelete
    };
    
})();