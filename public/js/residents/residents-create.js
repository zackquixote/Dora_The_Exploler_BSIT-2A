/**
 * Residents Management - Create Page JavaScript
 * Handles form validation, preview, and dynamic household filtering
 */

$(document).ready(function() {
    // Profile picture preview
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
                // Remove existing preview if any
                $('.profile-preview').remove();
                
                // Add preview image
                $('input[name="profile_picture"]').after(
                    '<div class="profile-preview mt-3">' +
                    '<label class="small font-weight-bold text-secondary">Preview:</label><br>' +
                    '<img src="' + e.target.result + '" width="100" height="100" class="rounded-circle img-thumbnail border-primary" style="object-fit: cover;">' +
                    '<button type="button" class="btn btn-sm btn-danger ml-3 remove-preview">' +
                    '<i class="fas fa-trash"></i> Remove</button>' +
                    '</div>'
                );
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Remove preview
    $(document).on('click', '.remove-preview', function() {
        $('input[name="profile_picture"]').val('');
        $('.profile-preview').remove();
    });
    
    // Dynamic household filtering based on selected sitio
    $('#sitioSelect').on('change', function() {
        var sitio = $(this).val();
        var householdSelect = $('#householdSelect');
        
        if (sitio && sitio !== 'Select Sitio') {
            // Show loading state
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
                        householdSelect.append('<option disabled selected>Select Household</option>');
                        $.each(response.households, function(key, household) {
                            var address = household.street_address ? ' - ' + household.street_address : '';
                            householdSelect.append('<option value="' + household.id + '">#' + household.household_no + address + '</option>');
                        });
                    } else {
                        householdSelect.append('<option disabled selected>No households found in this sitio</option>');
                        householdSelect.append('<option value="">Create new household first</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    householdSelect.empty();
                    householdSelect.prop('disabled', false);
                    householdSelect.append('<option disabled selected>Error loading households</option>');
                    alert('Error loading households. Please refresh the page and try again.');
                }
            });
        } else {
            householdSelect.empty();
            householdSelect.prop('disabled', false);
            householdSelect.append('<option disabled selected>First select a Sitio</option>');
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
        }
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
        
        if (!sex || sex === 'Select gender') {
            showFieldError('sex', 'Please select gender');
            hasError = true;
        }
        
        if (!sitio || sitio === 'Select Sitio') {
            showFieldError('sitioSelect', 'Please select a sitio');
            hasError = true;
        }
        
        if (!householdId || householdId === 'Select Household' || householdId === 'First select a Sitio') {
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
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
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
    
    console.log('Create resident page initialized');
});