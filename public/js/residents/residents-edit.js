/**
 * Residents Management - Edit Page JavaScript
 * Handles form submission, dependent dropdowns, file upload preview,
 * age calculation, and validation error display.
 *
 * Globals expected (set in edit.php before this script loads):
 *   BASE_URL, CSRF_TOKEN_NAME, CSRF_TOKEN_VALUE, CURRENT_SITIO, CURRENT_HOUSEHOLD_ID
 */

$(document).ready(function () {

    console.log('Residents edit page loaded');

    // ============================================
    // DEPENDENT DROPDOWN: SITIO -> HOUSEHOLD
    // ============================================

    function initDependentDropdown() {
        var $sitio     = $('#sitioSelect');
        var $household = $('#householdSelect');

        if (!$sitio.length || !$household.length) {
            console.warn('Sitio or Household select not found');
            return;
        }

        $sitio.on('change', function () {
            var selectedSitio = $(this).val();

            $household.empty();

            if (!selectedSitio) {
                $household.append('<option value="" disabled selected>First select a Sitio</option>');
                $household.prop('disabled', true);
                return;
            }

            $household.append('<option value="" disabled selected>Loading households…</option>');
            $household.prop('disabled', true);

            $.ajax({
                url:      BASE_URL + 'resident/getHouseholdsBySitio',
                type:     'GET',
                data:     { sitio: selectedSitio },
                dataType: 'json',
                timeout:  10000,
                success: function (response) {
                    $household.empty();
                    $household.prop('disabled', false);

                    if (response.status === 'success') {
                        var households = response.data || response.households || [];

                        if (households.length > 0) {
                            $household.append('<option value="" disabled>Select Household</option>');
                            $.each(households, function (i, h) {
                                var label = '#' + h.household_no;
                                if (h.street_address) { label += ' - ' + h.street_address; }
                                else if (h.address)   { label += ' - ' + h.address; }

                                var $opt = $('<option></option>').attr('value', h.id).text(label);

                                if (typeof CURRENT_HOUSEHOLD_ID !== 'undefined' &&
                                    h.id == CURRENT_HOUSEHOLD_ID) {
                                    $opt.prop('selected', true);
                                }
                                $household.append($opt);
                            });
                        } else {
                            $household.append('<option value="" disabled selected>No households found in this sitio</option>');
                        }
                    } else {
                        $household.append('<option value="" disabled selected>Error: ' + (response.message || 'Unknown') + '</option>');
                    }
                },
                error: function () {
                    $household.empty();
                    $household.prop('disabled', false);
                    $household.append('<option value="" disabled selected>Error loading. Please try again.</option>');
                    showNotification('error', 'Failed to load households. Please refresh the page.');
                }
            });
        });

        // On page load, trigger change so the household list is populated
        if (typeof CURRENT_SITIO !== 'undefined' && CURRENT_SITIO) {
            setTimeout(function () {
                $sitio.trigger('change');
            }, 100);
        }
    }

    // ============================================
    // FORM SUBMISSION
    // ============================================

    function initFormSubmission() {
        var $form = $('#residentForm');

        if (!$form.length) {
            console.warn('Resident form not found');
            return;
        }

        $form.on('submit', function (e) {
            e.preventDefault();

            var formData   = new FormData(this);
            var $submitBtn = $(this).find('button[type="submit"]');
            var origHtml   = $submitBtn.html();

            $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating…');

            if (typeof CSRF_TOKEN_NAME !== 'undefined') {
                formData.append(CSRF_TOKEN_NAME, CSRF_TOKEN_VALUE);
            }
            formData.append('_method', 'PUT');

            $.ajax({
                url:         $(this).attr('action'),
                type:        'POST',
                data:        formData,
                processData: false,
                contentType: false,
                dataType:    'json',
                timeout:     30000,
                success: function (response) {
                    if (response.status === 'success') {
                        if (response.csrf_hash) {
                            CSRF_TOKEN_VALUE = response.csrf_hash;
                            $('input[name="' + CSRF_TOKEN_NAME + '"]').val(response.csrf_hash);
                        }
                        showNotification('success', response.message || 'Resident updated successfully!');
                        setTimeout(function () {
                            window.location.href = response.redirect || (BASE_URL + 'resident');
                        }, 1500);
                    } else {
                        var msg = response.message || 'Error updating resident';
                        if (response.errors) {
                            displayValidationErrors(response.errors);
                            msg = 'Please fix the errors below.';
                        }
                        showNotification('error', msg);
                        $submitBtn.prop('disabled', false).html(origHtml);

                        if (response.csrf_hash) {
                            CSRF_TOKEN_VALUE = response.csrf_hash;
                            $('input[name="' + CSRF_TOKEN_NAME + '"]').val(response.csrf_hash);
                        }
                    }
                },
                error: function (xhr) {
                    var msg = 'An error occurred while updating. Please try again.';
                    if (xhr.status === 403) {
                        msg = 'CSRF token mismatch. Please refresh the page and try again.';
                    } else if (xhr.status === 404) {
                        msg = 'Server endpoint not found. Please contact the administrator.';
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        displayValidationErrors(xhr.responseJSON.errors);
                        msg = 'Please fix the errors below.';
                    }
                    showNotification('error', msg);
                    $submitBtn.prop('disabled', false).html(origHtml);
                }
            });
        });
    }

    // ============================================
    // FILE UPLOAD PREVIEW
    // ============================================

    function initFileUploadPreview() {
        $('input[name="profile_picture"]').on('change', function (e) {
            var file = e.target.files[0];
            if (!file) { return; }

            if (file.size > 2 * 1024 * 1024) {
                showNotification('error', 'File size exceeds 2 MB limit');
                $(this).val('');
                return;
            }

            var reader = new FileReader();
            reader.onload = function (ev) {
                $('#newProfilePreview').remove();
                var html =
                    '<div id="newProfilePreview" class="mt-2">' +
                    '<small class="text-muted">New photo preview:</small><br>' +
                    '<img src="' + ev.target.result + '" ' +
                    'class="img-thumbnail rounded-circle" ' +
                    'style="width:60px;height:60px;object-fit:cover;">' +
                    '</div>';

                if ($('.current-photo').length) {
                    $('.current-photo').after(html);
                } else {
                    $('input[name="profile_picture"]').after(html);
                }
            };
            reader.readAsDataURL(file);
        });
    }

    // ============================================
    // AGE CALCULATOR (auto-tick Senior Citizen)
    // ============================================

    function initAgeCalculator() {
        $('input[name="birthdate"]').on('change', function () {
            var birthdate = $(this).val();
            if (birthdate && calculateAge(birthdate) >= 60) {
                $('input[name="is_senior_citizen"]').prop('checked', true);
            }
        });
    }

    function calculateAge(birthdate) {
        var today = new Date();
        var birth = new Date(birthdate);
        var age   = today.getFullYear() - birth.getFullYear();
        var m     = today.getMonth() - birth.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) { age--; }
        return age;
    }

    // ============================================
    // VALIDATION ERROR DISPLAY
    // ============================================

    function displayValidationErrors(errors) {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        $.each(errors, function (field, message) {
            var $input = $('[name="' + field + '"]');
            if ($input.length) {
                $input.addClass('is-invalid');
                $input.after('<div class="invalid-feedback d-block">' + message + '</div>');
            }
        });

        var $first = $('.is-invalid').first();
        if ($first.length) {
            $('html, body').animate({ scrollTop: $first.offset().top - 100 }, 500);
        }
    }

    // ============================================
    // NOTIFICATION HELPER
    // ============================================

    function showNotification(type, message) {
        var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        var icon       = type === 'success' ? 'check-circle'  : 'exclamation-circle';

        var html =
            '<div class="alert ' + alertClass + ' alert-dismissible fade show position-fixed" ' +
            'style="top:20px;right:20px;z-index:9999;min-width:300px;box-shadow:0 4px 6px rgba(0,0,0,.1);" ' +
            'role="alert">' +
            '<i class="fas fa-' + icon + ' mr-2"></i> ' + message +
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
            '<span aria-hidden="true">&times;</span></button></div>';

        $('.alert.position-fixed').remove();
        $('body').append(html);

        setTimeout(function () {
            $('.alert.position-fixed').fadeOut('slow', function () { $(this).remove(); });
        }, 5000);
    }

    // ============================================
    // INIT
    // ============================================

    initDependentDropdown();
    initFormSubmission();
    initFileUploadPreview();
    initAgeCalculator();
});