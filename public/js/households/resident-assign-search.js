/**
 * Resident Assign Search page
 * Handles toggling relationship dropdowns, validation, and AJAX household loading
 */
$(document).ready(function() {
    const vars = $('#js-variables').data();
    const BASE_URL = vars.baseUrl;
    const CSRF_TOKEN = vars.csrfToken;
    let CSRF_HASH = vars.csrfHash;
    const targetHouseholdId = vars.householdId;
    
    // Toggle dropdown enable/disable based on checkbox
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
    
    // Validate before submitting
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
    
    // Load households based on purok selection
    function loadHouseholds(purok, preSelectId) {
        if (!purok) {
            $('#filter_household_id').html('<option value="">All Houses</option>');
            return;
        }
        $.ajax({
            url: BASE_URL + 'resident/getHouseholdsBySitio',
            type: "GET",
            data: { sitio: purok },
            dataType: "json",
            success: function(data) {
                let options = '<option value="">All Houses</option>';
                $.each(data.data, function(i, item) {
                    let selected = (preSelectId && item.id == preSelectId) ? 'selected' : '';
                    options += `<option value="${item.id}" ${selected}>Household #${item.household_no}</option>`;
                });
                $('#filter_household_id').html(options);
            },
            error: function() {
                $('#filter_household_id').html('<option value="">Error loading houses</option>');
            }
        });
    }
    
    // Event handlers
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
    
    // Initial load if a purok is preselected
    const initialPurok = $('#filter_purok').val();
    const initialHouse = $('#hidden_household').val();
    if (initialPurok) {
        loadHouseholds(initialPurok, initialHouse);
    }
    
    // Attach toggleRelation to checkboxes dynamically (since table rows may be paginated)
    $(document).on('change', 'input[name="selected_residents[]"]', function() {
        const id = $(this).val();
        toggleRelation(id);
    });
});