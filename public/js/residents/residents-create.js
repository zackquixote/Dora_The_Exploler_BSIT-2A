/**
 * Residents Create Form JavaScript
 * Handles file upload preview, custom checkboxes, dynamic household loading
 */

(function($) {
    'use strict';

    // ---------- File Upload Preview ----------
    function initFileUploadPreview() {
        const $fileInput = $('#profile_picture');
        const $fileNameDisplay = $('#fileName');

        if (!$fileInput.length) return;

        $fileInput.on('change', function(e) {
            const fileName = e.target.value.split('\\').pop();
            $fileNameDisplay.text(fileName || 'No file chosen');
        });
    }

    // ---------- Custom Checkbox Styling ----------
    function initCheckboxStyling() {
        $('.rc-checkbox input').each(function() {
            const $input = $(this);
            const $svg = $input.next('svg');

            function updateStyle() {
                if ($input.prop('checked')) {
                    $svg.css('display', 'block');
                    $input.parent().css({
                        backgroundColor: 'var(--primary)',
                        borderColor: 'var(--primary)'
                    });
                } else {
                    $svg.css('display', 'none');
                    $input.parent().css({
                        backgroundColor: 'transparent',
                        borderColor: '#CBD5E0'
                    });
                }
            }

            $input.on('change', updateStyle);
            updateStyle(); // initial state
        });
    }

    // ---------- Dependent Dropdown: Sitio -> Household ----------
    let $sitioSelect, $householdSelect, $loadingIndicator;
    let config = {
        baseUrl: typeof BASE_URL !== 'undefined' ? BASE_URL : '',
        csrfName: typeof CSRF_TOKEN_NAME !== 'undefined' ? CSRF_TOKEN_NAME : '',
        csrfValue: typeof CSRF_TOKEN_VALUE !== 'undefined' ? CSRF_TOKEN_VALUE : ''
    };

    function initDependentDropdown() {
        $sitioSelect = $('#sitioSelect');
        $householdSelect = $('#householdSelect');
        $loadingIndicator = $('#householdLoading');

        if (!$sitioSelect.length || !$householdSelect.length) return;

        $sitioSelect.on('change', function() {
            const selectedSitio = $(this).val();
            if (!selectedSitio) {
                resetHouseholdSelect('-- First select a Sitio --', true);
                return;
            }
            loadHouseholds(selectedSitio);
        });

        // Trigger on page load if a sitio is pre-selected
        if ($sitioSelect.val()) {
            $sitioSelect.trigger('change');
        }
    }

    function loadHouseholds(sitio) {
        showLoadingState();
        $.ajax({
            url: config.baseUrl + 'resident/getHouseholdsBySitio',
            type: 'GET',
            data: { sitio: sitio },
            dataType: 'json',
            success: function(response) {
                hideLoadingState();
                if (response.status === 'success' && response.data) {
                    populateHouseholdOptions(response.data);
                } else {
                    showErrorState('Error loading households');
                }
            },
            error: function() {
                hideLoadingState();
                showErrorState('Error loading households. Please refresh.');
            }
        });
    }

    function populateHouseholdOptions(households) {
        let options = '<option value=""> Select household (optional) </option>';
        if (households.length > 0) {
            households.forEach(function(h) {
                const label = (h.household_no ? '#' + h.household_no : '') +
                              (h.street_address ? ' - ' + h.street_address : '');
                options += `<option value="${h.id}">${escapeHtml(label)}</option>`;
            });
            $householdSelect.html(options).prop('disabled', false);
        } else {
            $householdSelect.html('<option value="">No households found in this sitio</option>');
        }
    }

    function resetHouseholdSelect(message, disabled) {
        $householdSelect.html(`<option value="">${message}</option>`);
        $householdSelect.prop('disabled', disabled);
    }

    function showLoadingState() {
        $householdSelect.html('<option value="">Loading households...</option>').prop('disabled', true);
        if ($loadingIndicator.length) $loadingIndicator.show();
    }

    function hideLoadingState() {
        if ($loadingIndicator.length) $loadingIndicator.hide();
    }

    function showErrorState(message) {
        $householdSelect.html(`<option value="">${message}</option>`);
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    // ---------- Form Validation ----------
    function initFormValidation() {
        $('#residentForm').on('submit', function(e) {
            const sitio = $('#sitioSelect').val();
            if (!sitio) {
                e.preventDefault();
                alert('Please select a Sitio/Zone');
                $('#sitioSelect').focus();
                return false;
            }
            return true;
        });
    }

    // ---------- Initialize ----------
    $(document).ready(function() {
        initFileUploadPreview();
        initCheckboxStyling();
        initDependentDropdown();
        initFormValidation();
    });

})(jQuery);