/**
 * Resident Assign Search page
 * 
 * Handles:
 * - Dynamic load of households based on selected Purok/Sitio.
 * - Enabling/disabling relationship dropdown based on checkbox selection.
 * - Validation before bulk assignment.
 * 
 * FIXES:
 * - Corrected AJAX endpoint from 'resident/getHouseholdsBySitio' to 
 *   'households/getHouseholdsBySitio' (method belongs to HouseholdController).
 * - Added CSRF token refresh after AJAX calls.
 */
$(document).ready(function() {
    const vars = $('#js-variables').data();
    const BASE_URL = vars.baseUrl;
    const CSRF_TOKEN = vars.csrfToken;
    let CSRF_HASH = vars.csrfHash;
    const targetHouseholdId = vars.householdId;
    
    /**
     * Enables/disables the relationship dropdown based on checkbox state.
     * @param {number} id - Resident ID
     */
    function toggleRelation(id) {
        const checkbox = document.getElementById('check_' + id);
        const dropdown = document.getElementById('rel_' + id);
        if (checkbox.checked) {
            dropdown.disabled = false;
            dropdown.focus();
        } else {
            dropdown.disabled = true;
            dropdown.value = "";
        }
    }
    
    /**
     * Validates that every selected resident has a relationship before submitting.
     */
    function validateAndSubmit() {
        const checkboxes = document.querySelectorAll('input[name="selected_residents[]"]:checked');
        if (checkboxes.length === 0) {
            alert("Please select at least one resident.");
            return;
        }
        let valid = true;
        checkboxes.forEach(function(box) {
            const id = box.value;
            const dropdown = document.getElementById('rel_' + id);
            if (dropdown.value === "") {
                valid = false;
                const nameCell = dropdown.closest('tr').querySelector('strong');
                alert("Please select a relationship for " + (nameCell ? nameCell.innerText : 'resident'));
            }
        });
        if (valid) {
            document.getElementById('bulkForm').submit();
        }
    }
    
    /**
     * Loads households belonging to a given Purok/Sitio via AJAX.
     * Updates the household filter dropdown and optionally preselects a household.
     * 
     * FIX: Endpoint corrected to 'households/getHouseholdsBySitio'
     */
    function loadHouseholds(purok, preSelectId) {
        if (!purok) {
            $('#filter_household_id').html('<option value="">All Houses</option>');
            return;
        }
        $.ajax({
            url: BASE_URL + 'households/getHouseholdsBySitio',  // ✅ Fixed endpoint
            type: "GET",
            data: { sitio: purok },
            dataType: "json",
            success: function(data) {
                // Update CSRF token if provided (for future POST requests)
                if (data.csrf_hash) {
                    CSRF_HASH = data.csrf_hash;
                    $('input[name="' + CSRF_TOKEN + '"]').val(CSRF_HASH);
                }
                let options = '<option value="">All Houses</option>';
                $.each(data.data, function(i, item) {
                    let selected = (preSelectId && item.id == preSelectId) ? 'selected' : '';
                    options += `<option value="${item.id}" ${selected}>Household #${item.household_no}</option>`;
                });
                $('#filter_household_id').html(options);
            },
            error: function(xhr, status, error) {
                console.error('Failed to load households:', error);
                $('#filter_household_id').html('<option value="">Error loading houses</option>');
            }
        });
    }
    
    // Event bindings
    $('#filter_purok').on('change', function() {
        const purok = $(this).val();
        $('#hidden_purok').val(purok);
        $('#hidden_household').val('');
        loadHouseholds(purok);
    });
    
    $('#filter_household_id').on('change', function() {
        $('#hidden_household').val($(this).val());
    });
    
    $('#assignSelectedBtn').on('click', function() {
        validateAndSubmit();
    });
    
    // Initial load if a purok is preselected (e.g., after form validation error)
    const initialPurok = $('#filter_purok').val();
    const initialHouse = $('#hidden_household').val();
    if (initialPurok) {
        loadHouseholds(initialPurok, initialHouse);
    }
    
    // Attach toggleRelation to dynamically loaded checkboxes
    $(document).on('change', 'input[name="selected_residents[]"]', function() {
        const id = $(this).val();
        toggleRelation(id);
    });
});