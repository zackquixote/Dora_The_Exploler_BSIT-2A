/**
 * Residents Edit Form JavaScript
 * Handles dependent dropdowns, form submission (AJAX), file preview, age calculator, custom checkboxes
 */

(function($) {
    'use strict';

    let config = {
        baseUrl: typeof BASE_URL !== 'undefined' ? BASE_URL : '',
        csrfName: typeof CSRF_TOKEN_NAME !== 'undefined' ? CSRF_TOKEN_NAME : '',
        csrfValue: typeof CSRF_TOKEN_VALUE !== 'undefined' ? CSRF_TOKEN_VALUE : '',
        currentSitio: typeof CURRENT_SITIO !== 'undefined' ? CURRENT_SITIO : '',
        currentHouseholdId: typeof CURRENT_HOUSEHOLD_ID !== 'undefined' ? CURRENT_HOUSEHOLD_ID : ''
    };

    // ---------- Dependent Dropdown: Sitio -> Household ----------
    function initDependentDropdown() {
        const $sitio = $('#sitioSelect');
        const $household = $('#householdSelect');

        if (!$sitio.length || !$household.length) return;

        function loadHouseholds(sitio) {
            $household.empty().append('<option value="" disabled>Loading households…</option>').prop('disabled', true);
            $.ajax({
                url: config.baseUrl + 'resident/getHouseholdsBySitio',
                type: 'GET',
                data: { sitio: sitio },
                dataType: 'json',
                success: function(response) {
                    $household.empty().prop('disabled', false);
                    if (response.status === 'success') {
                        const households = response.data || [];
                        if (households.length) {
                            $household.append('<option value="" disabled>Select Household</option>');
                            households.forEach(function(h) {
                                const label = '#' + h.household_no + (h.street_address ? ' - ' + h.street_address : '');
                                const $opt = $('<option>').val(h.id).text(label);
                                if (h.id == config.currentHouseholdId) $opt.prop('selected', true);
                                $household.append($opt);
                            });
                        } else {
                            $household.append('<option value="" disabled>No households in this sitio</option>');
                        }
                    } else {
                        $household.append('<option value="" disabled>Error loading households</option>');
                    }
                },
                error: function() {
                    $household.empty().prop('disabled', false);
                    $household.append('<option value="" disabled>Error loading. Please refresh.</option>');
                }
            });
        }

        $sitio.on('change', function() {
            const val = $(this).val();
            if (val) loadHouseholds(val);
            else {
                $household.empty().append('<option value="" disabled>First select a Sitio</option>').prop('disabled', true);
            }
        });

        if (config.currentSitio) {
            setTimeout(() => $sitio.trigger('change'), 100);
        }
    }

    // ---------- AJAX Form Submission ----------
    function initFormSubmission() {
        const $form = $('#residentForm');
        if (!$form.length) return;

        $form.on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('_method', 'PUT');
            if (config.csrfName) formData.append(config.csrfName, config.csrfValue);

            const $submitBtn = $(this).find('button[type="submit"]');
            const origHtml = $submitBtn.html();
            $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating…');

            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        showNotification('success', response.message || 'Resident updated successfully!');
                        if (response.csrf_hash) {
                            config.csrfValue = response.csrf_hash;
                            $('input[name="' + config.csrfName + '"]').val(response.csrf_hash);
                        }
                        setTimeout(() => window.location.href = response.redirect || (config.baseUrl + 'resident'), 1500);
                    } else {
                        let msg = response.message || 'Error updating resident';
                        if (response.errors) {
                            displayValidationErrors(response.errors);
                            msg = 'Please fix the errors below.';
                        }
                        showNotification('error', msg);
                        $submitBtn.prop('disabled', false).html(origHtml);
                    }
                },
                error: function(xhr) {
                    let msg = 'An error occurred. Please try again.';
                    if (xhr.status === 403) msg = 'CSRF token mismatch. Refresh the page.';
                    else if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    showNotification('error', msg);
                    $submitBtn.prop('disabled', false).html(origHtml);
                }
            });
        });
    }

    // ---------- File Upload Preview ----------
    function initFileUploadPreview() {
        $('input[name="profile_picture"]').on('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            if (file.size > 2 * 1024 * 1024) {
                showNotification('error', 'File size exceeds 2 MB limit');
                $(this).val('');
                return;
            }
            const reader = new FileReader();
            reader.onload = function(ev) {
                $('#newProfilePreview').remove();
                const html = `<div id="newProfilePreview" class="mt-2">
                    <small class="text-muted">New photo preview:</small><br>
                    <img src="${ev.target.result}" class="img-thumbnail rounded-circle" style="width:60px;height:60px;object-fit:cover;">
                </div>`;
                $('input[name="profile_picture"]').after(html);
            };
            reader.readAsDataURL(file);
        });
    }

    // ---------- Age Calculator (auto-check Senior Citizen) ----------
    function initAgeCalculator() {
        $('input[name="birthdate"]').on('change', function() {
            const birthdate = $(this).val();
            if (birthdate && calculateAge(birthdate) >= 60) {
                $('input[name="is_senior_citizen"]').prop('checked', true);
            }
        });
    }

    function calculateAge(birthdate) {
        const today = new Date();
        const birth = new Date(birthdate);
        let age = today.getFullYear() - birth.getFullYear();
        const m = today.getMonth() - birth.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
        return age;
    }

    // ---------- Custom Checkbox Styling ----------
    function initCheckboxStyling() {
        $('.rc-checkbox input').each(function() {
            const $input = $(this);
            const $svg = $input.next('svg');
            function update() {
                if ($input.prop('checked')) {
                    $svg.css('display', 'block');
                    $input.parent().css({ backgroundColor: 'var(--primary)', borderColor: 'var(--primary)' });
                } else {
                    $svg.css('display', 'none');
                    $input.parent().css({ backgroundColor: 'transparent', borderColor: '#CBD5E0' });
                }
            }
            $input.on('change', update);
            update();
        });
    }

    // ---------- Validation Error Display ----------
    function displayValidationErrors(errors) {
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        $.each(errors, function(field, message) {
            const $input = $('[name="' + field + '"]');
            if ($input.length) {
                $input.addClass('is-invalid');
                $input.after('<div class="invalid-feedback d-block">' + message + '</div>');
            }
        });
        const $first = $('.is-invalid').first();
        if ($first.length) $('html, body').animate({ scrollTop: $first.offset().top - 100 }, 500);
    }

    // ---------- Notification Helper ----------
    function showNotification(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'check-circle' : 'exclamation-circle';
        const html = `<div class="alert ${alertClass} alert-dismissible fade show position-fixed" style="top:20px;right:20px;z-index:9999;min-width:300px;box-shadow:0 4px 6px rgba(0,0,0,.1);" role="alert">
            <i class="fas fa-${icon} mr-2"></i> ${message}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>`;
        $('.alert.position-fixed').remove();
        $('body').append(html);
        setTimeout(() => $('.alert.position-fixed').fadeOut('slow', function() { $(this).remove(); }), 5000);
    }

    // ---------- Initialize ----------
    $(document).ready(function() {
        initDependentDropdown();
        initFormSubmission();
        initFileUploadPreview();
        initAgeCalculator();
        initCheckboxStyling();
    });

})(jQuery);