/**
 * Residents Management - Edit Page JavaScript
 * Handles form validation, profile picture preview, and dynamic household filtering
 */

$(document).ready(function() {
    // Store original values for change detection
    var originalFormData = {};
    var formChanged = false;
    
    // Store current household ID
    var originalHouseholdId = $('#householdSelect').val();
    
    // Profile picture preview for new image
    $('input[name="profile_picture"]').on('change', function() {
        var file = this.files[0];
        if (file) {
            // Validate file size (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('File size exceeds 2MB. Please choose a smaller file.');
                $(this).val('');
                return;
            }
            
            // Validate file type
            var allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                alert('Please select a valid image file (JPG, PNG, or GIF).');
                $(this).val('');
                return;
            }
            
            var reader = new FileReader();
            reader.onload = function(e) {
                // Hide current photo and show new preview
                $('.current-photo').hide();
                $('.new-preview').remove();
                $('input[name="profile_picture"]').after(
                    '<div class="new-preview mt-3">' +
                    '<label class="small font-weight-bold text-secondary">New photo preview:</label><br>' +
                    '<img src="' + e.target.result + '" width="100" height="100" class="rounded-circle img-thumbnail border-primary" style="object-fit: cover;">' +
                    '<button type="button" class="btn btn-sm btn-danger ml-3 remove-new-preview">' +
                    '<i class="fas fa-times"></i> Cancel</button>' +
                    '</div>'
                );
                formChanged = true;
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Remove new preview and restore current photo display
    $(document).on('click', '.remove-new-preview', function() {
        $('input[name="profile_picture"]').val('');
        $('.new-preview').remove();
        $('.current-photo').show();
        formChanged = true;
    });
    
    // Dynamic household filtering based on selected sitio
    $('#sitioSelect').on('change', function() {
        var sitio = $(this).val();
        var householdSelect = $('#householdSelect');
        var currentHouseholdId = originalHouseholdId;
        
        if (sitio && sitio !== '') {
            // Show loading state
            var currentSelected = householdSelect.find('option:selected').text();
            householdSelect.html('<option disabled selected>Loading households...</option>');
            householdSelect.prop('disabled', true);
            
            $.ajax({
                url: BASE_URL + 'resident/get-households-by-sitio',
                type: 'POST',
                data: {
                    sitio: sitio,
                    [CSRF_TOKEN_NAME]: CSRF_TOKEN_VALUE
                },
                dataType: 'json',
                success: function(response) {
                    householdSelect.empty();
                    householdSelect.prop('disabled', false);
                    
                    if (response.status === 'success' && response.households.length > 0) {
                        householdSelect.append('<option value="">Select Household</option>');
                        $.each(response.households, function(key, household) {
                            var address = household.street_address ? ' - ' + household.street_address : '';
                            var selected = (originalHouseholdId == household.id) ? 'selected' : '';
                            householdSelect.append('<option value="' + household.id + '" ' + selected + '>#' + household.household_no + address + '</option>');
                        });
                    } else {
                        householdSelect.append('<option value="">No households found in this sitio</option>');
                        householdSelect.append('<option value="">Create new household first</option>');
                    }
                    
                    formChanged = true;
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    householdSelect.empty();
                    householdSelect.prop('disabled', false);
                    householdSelect.append('<option value="">Error loading households</option>');
                    alert('Error loading households. Please refresh the page and try again.');
                }
            });
        } else {
            // Reset to original household list if no sitio selected
            location.reload();
        }
    });
    
    // Calculate age from birthdate
    $('input[name="birthdate"]').on('change', function() {
        var birthdate = $(this).val();
        if (birthdate) {
            var today = new Date();
            var birthDate = new Date(birthdate);
            var age = today.getFullYear() - birthDate.getFullYear();
            var m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            
            if (age < 0) {
                alert('Birthdate cannot be in the future!');
                $(this).val('');
            } else if (age > 120) {
                alert('Please check the birthdate. Age cannot exceed 120 years.');
            }
            formChanged = true;
        }
    });
    
    // Track form changes
    $('#residentForm input, #residentForm select').on('change', function() {
        formChanged = true;
    });
    
    $('#residentForm textarea').on('keyup', function() {
        formChanged = true;
    });
    
    // Form validation before submit
    $('#residentForm').on('submit', function(e) {
        var firstName = $('input[name="first_name"]').val().trim();
        var lastName = $('input[name="last_name"]').val().trim();
        var birthdate = $('input[name="birthdate"]').val();
        var sex = $('select[name="sex"]').val();
        var sitio = $('#sitioSelect').val();
        var householdId = $('#householdSelect').val();
        
        // Clear previous errors
        $('.error-message').remove();
        $('.is-invalid').removeClass('is-invalid');
        
        var hasError = false;
        
        if (!firstName) {
            showFieldError('first_name', 'Please enter first name');
            hasError = true;
        }
        
        if (!lastName) {
            showFieldError('last_name', 'Please enter last name');
            hasError = true;
        }
        
        if (!birthdate) {
            showFieldError('birthdate', 'Please select birthdate');
            hasError = true;
        }
        
        if (!sex) {
            showFieldError('sex', 'Please select gender');
            hasError = true;
        }
        
        if (!sitio || sitio === '') {
            showFieldError('sitioSelect', 'Please select a sitio');
            hasError = true;
        }
        
        if (!householdId || householdId === '') {
            showFieldError('householdSelect', 'Please select a household');
            hasError = true;
        }
        
        if (hasError) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $('.error-message:first').offset().top - 100
            }, 500);
            return false;
        }
        
        // Show loading state
        var submitBtn = $('button[type="submit"]').last();
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Updating...').prop('disabled', true);
        window.onbeforeunload = null;
    });
    
    // Helper function to show field error
    function showFieldError(fieldName, message) {
        var field = $('#' + fieldName);
        if (field.length === 0) {
            field = $('[name="' + fieldName + '"]');
        }
        
        field.addClass('is-invalid');
        field.after('<div class="invalid-feedback error-message">' + message + '</div>');
        
        // Remove error when field is changed
        field.one('change focus', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        });
    }
    
    // Remove error styling on input
    $('input, select').on('focus', function() {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });
    
    // Warn before leaving if changes made
    window.onbeforeunload = function() {
        if (formChanged) {
            return 'You have unsaved changes. Are you sure you want to leave?';
        }
    };
    
    console.log('Edit resident page initialized');
});