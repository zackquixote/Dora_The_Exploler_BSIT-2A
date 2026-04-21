/**
 * Residents Create Form JavaScript
 * Handles dynamic household loading and address preview
 * File location: public/js/residents/residents-create.js
 */

(function() {
    'use strict';
    
    // DOM Elements
    let $sitioSelect;
    let $householdSelect;
    let $loadingIndicator;
    let $addressPreview;
    let $addressText;
    let $residentForm;
    
    // Configuration
    const config = {
        baseUrl: BASE_URL || '',
        csrfName: CSRF_TOKEN_NAME || '',
        csrfValue: CSRF_TOKEN_VALUE || '',
        preselectedHousehold: typeof PRESELECTED_HOUSEHOLD !== 'undefined' ? PRESELECTED_HOUSEHOLD : ''
    };
    
    /**
     * Initialize the form
     */
    function init() {
        cacheElements();
        bindEvents();
        initializeForm();
    }
    
    /**
     * Cache DOM elements
     */
    function cacheElements() {
        $sitioSelect = $('#sitioSelect');
        $householdSelect = $('#householdSelect');
        $loadingIndicator = $('#householdLoading');
        $addressPreview = $('#householdAddressPreview');
        $addressText = $('#addressText');
        $residentForm = $('#residentForm');
    }
    
    /**
     * Bind event listeners
     */
    function bindEvents() {
        $sitioSelect.on('change', handleSitioChange);
        $householdSelect.on('change', handleHouseholdChange);
        $residentForm.on('submit', handleFormSubmit);
    }
    
    /**
     * Initialize form with pre-selected values
     */
    function initializeForm() {
        const initialSitio = $sitioSelect.val();
        if (initialSitio) {
            loadHouseholds(initialSitio);
        }
    }
    
    /**
     * Handle sitio selection change
     */
    function handleSitioChange() {
        const selectedSitio = $(this).val();
        loadHouseholds(selectedSitio);
    }
    
    /**
     * Handle household selection change
     */
    function handleHouseholdChange() {
        const selectedHousehold = $(this).val();
        displayHouseholdAddress(selectedHousehold);
    }
    
    /**
     * Handle form submission
     */
    function handleFormSubmit(e) {
        const sitio = $sitioSelect.val();
        
        if (!sitio) {
            e.preventDefault();
            showNotification('Please select a Sitio/Zone', 'warning');
            $sitioSelect.focus();
            return false;
        }
        
        // Form is valid, continue with submission
        return true;
    }
    
    /**
     * Load households based on selected sitio
     * @param {string} sitio - Selected sitio
     */
    function loadHouseholds(sitio) {
        if (!sitio) {
            resetHouseholdSelect('-- First select a Sitio --', true);
            $addressPreview.hide();
            return;
        }
        
        showLoadingState();
        
        $.ajax({
            url: `${config.baseUrl}resident/getHouseholdsBySitio`,
            type: 'GET',
            data: { sitio: sitio },
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                handleHouseholdsResponse(response);
            },
            error: function(xhr, status, error) {
                handleHouseholdsError(xhr, status, error);
            }
        });
    }
    
    /**
     * Handle households API response
     * @param {Object} response - API response
     */
    function handleHouseholdsResponse(response) {
        hideLoadingState();
        
        if (response.status === 'success' && response.data) {
            populateHouseholdOptions(response.data);
        } else {
            showErrorState('Error loading households');
            console.error('Error loading households:', response);
        }
    }
    
    /**
     * Handle households API error
     * @param {Object} xhr - XHR object
     * @param {string} status - Error status
     * @param {string} error - Error message
     */
    function handleHouseholdsError(xhr, status, error) {
        hideLoadingState();
        showErrorState('Error loading households');
        console.error('AJAX Error:', status, error);
    }
    
    /**
     * Populate household select options
     * @param {Array} households - List of households
     */
    function populateHouseholdOptions(households) {
        let options = '<option value="">-- Select household (optional) --</option>';
        
        if (households.length > 0) {
            households.forEach(function(household) {
                const selected = (config.preselectedHousehold == household.id) ? 'selected' : '';
                const address = escapeHtml(household.street_address || '');
                const householdNo = escapeHtml(household.household_no || '');
                
                options += `<option value="${household.id}" ${selected} 
                            data-address="${address}" 
                            data-household-no="${householdNo}">
                            ${householdNo} - ${address || 'No address'}
                            </option>`;
            });
            
            $householdSelect.html(options).prop('disabled', false);
            
            // Trigger change if there's a preselected household
            if (config.preselectedHousehold) {
                $householdSelect.trigger('change');
            }
        } else {
            $householdSelect.html('<option value="">No households found in this sitio</option>');
        }
    }
    
    /**
     * Display selected household address
     * @param {string} householdId - Selected household ID
     */
    function displayHouseholdAddress(householdId) {
        if (!householdId) {
            $addressPreview.hide();
            return;
        }
        
        const $selectedOption = $householdSelect.find('option:selected');
        const address = $selectedOption.data('address') || '';
        const householdNo = $selectedOption.data('household-no') || '';
        const sitio = $sitioSelect.val() || '';
        
        if (address) {
            const fullAddress = `${householdNo} - ${address}, ${sitio}`;
            $addressText.text(fullAddress);
            $addressPreview.show();
        } else {
            // Fallback: fetch from server if data attribute is empty
            fetchHouseholdDetails(householdId, sitio);
        }
    }
    
    /**
     * Fetch household details from server
     * @param {string} householdId - Household ID
     * @param {string} sitio - Sitio name
     */
    function fetchHouseholdDetails(householdId, sitio) {
        $.ajax({
            url: `${config.baseUrl}households/getDetails/${householdId}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' && response.data) {
                    const household = response.data;
                    const fullAddress = `${household.household_no || ''} - ${household.street_address || 'No address'}, ${household.sitio || sitio}`;
                    $addressText.text(fullAddress);
                    $addressPreview.show();
                }
            },
            error: function() {
                console.warn('Could not fetch household details');
            }
        });
    }
    
    /**
     * Reset household select to default state
     * @param {string} message - Message to display
     * @param {boolean} disabled - Whether select should be disabled
     */
    function resetHouseholdSelect(message, disabled = true) {
        $householdSelect.html(`<option value="">${message}</option>`);
        $householdSelect.prop('disabled', disabled);
    }
    
    /**
     * Show loading state
     */
    function showLoadingState() {
        $householdSelect.html('<option value="">Loading households...</option>');
        $householdSelect.prop('disabled', true);
        $loadingIndicator.show();
        $addressPreview.hide();
    }
    
    /**
     * Hide loading state
     */
    function hideLoadingState() {
        $loadingIndicator.hide();
    }
    
    /**
     * Show error state
     * @param {string} message - Error message
     */
    function showErrorState(message) {
        $householdSelect.html(`<option value="">${message}</option>`);
    }
    
    /**
     * Escape HTML to prevent XSS
     * @param {string} text - Text to escape
     * @returns {string} Escaped text
     */
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    /**
     * Show notification message
     * @param {string} message - Message to display
     * @param {string} type - Notification type (success, error, warning, info)
     */
    function showNotification(message, type = 'info') {
        // Check if SweetAlert2 is available
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                text: message,
                icon: type,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        } else {
            alert(message);
        }
    }
    
    /**
     * Calculate age from birthdate
     * @param {string} birthdate - Birthdate in YYYY-MM-DD format
     * @returns {number} Age in years
     */
    function calculateAge(birthdate) {
        const today = new Date();
        const birthDate = new Date(birthdate);
        let age = today.getFullYear() - birthDate.getFullYear();
        const m = today.getMonth() - birthDate.getMonth();
        
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        
        return age;
    }
    
    // Initialize when DOM is ready
    $(document).ready(init);
    
})();