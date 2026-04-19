/**
 * Residents Management - Create Page JavaScript
 * FIXED: Properly waits for jQuery and DOM
 */

(function() {
    // Wait for both jQuery and DOM to be ready
    function initWhenReady() {
        if (typeof jQuery === 'undefined') {
            setTimeout(initWhenReady, 50);
            return;
        }
        
        jQuery(document).ready(function($) {
            console.log('Residents create page loaded. jQuery version:', $.fn.jquery);
            console.log('BASE_URL:', BASE_URL);

            // ============================================
            // DEPENDENT DROPDOWN: SITIO -> HOUSEHOLD
            // ============================================

            function initDependentDropdown() {
                var $sitio     = $('#sitioSelect');
                var $household = $('#householdSelect');
                var $loading   = $('#householdLoading');

                if (!$sitio.length || !$household.length) {
                    console.warn('Sitio or Household select not found');
                    return;
                }

                console.log('Dependent dropdown initialized');

                $sitio.on('change', function () {
                    var selectedSitio = $(this).val();
                    console.log('Sitio changed to:', selectedSitio);

                    $household.empty();
                    $loading.show();

                    if (!selectedSitio) {
                        $household.append('<option value="" disabled selected>First select a Sitio</option>');
                        $household.prop('disabled', true);
                        $loading.hide();
                        return;
                    }

                    $household.append('<option value="" disabled selected>Loading households…</option>');
                    $household.prop('disabled', true);

                    var url = BASE_URL + 'resident/getHouseholdsBySitio';
                    console.log('AJAX URL:', url);
                    console.log('Sending sitio:', selectedSitio);

                    $.ajax({
                        url:      url,
                        type:     'GET',
                        data:     { sitio: selectedSitio },
                        dataType: 'json',
                        timeout:  10000,
                        success: function (response) {
                            console.log('AJAX Response:', response);
                            
                            $household.empty();
                            $household.prop('disabled', false);
                            $loading.hide();

                            var households = [];
                            var status = false;
                            
                            if (response.status === 'success') {
                                status = true;
                                households = response.data || response.households || [];
                            } else if (response.success === true) {
                                status = true;
                                households = response.data || response.households || [];
                            } else if (Array.isArray(response)) {
                                status = true;
                                households = response;
                            } else if (response.households && Array.isArray(response.households)) {
                                status = true;
                                households = response.households;
                            }

                            console.log('Parsed households:', households);
                            console.log('Households length:', households.length);

                            if (status && households.length > 0) {
                                $household.append('<option value="" disabled selected>Select Household</option>');
                                $.each(households, function (i, h) {
                                    var label = '';
                                    if (h.household_no) {
                                        label = '#' + h.household_no;
                                    } else if (h.id) {
                                        label = '#HH-' + h.id;
                                    } else {
                                        label = 'Household ' + (i + 1);
                                    }
                                    
                                    if (h.street_address) {
                                        label += ' - ' + h.street_address;
                                    } else if (h.address) {
                                        label += ' - ' + h.address;
                                    }
                                    
                                    console.log('Adding option:', h.id, label);
                                    $household.append('<option value="' + h.id + '">' + label + '</option>');
                                });
                            } else {
                                console.warn('No households found for sitio:', selectedSitio);
                                $household.append('<option value="" disabled selected>No households found in this sitio</option>');
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('AJAX Error:', {
                                status: status,
                                error: error,
                                xhrStatus: xhr.status,
                                responseText: xhr.responseText
                            });
                            
                            $household.empty();
                            $household.prop('disabled', false);
                            $loading.hide();
                            
                            if (xhr.status === 404) {
                                $household.append('<option value="" disabled selected>Error: Endpoint not found</option>');
                            } else {
                                $household.append('<option value="" disabled selected>Error loading. Try again.</option>');
                            }
                        }
                    });
                });

                // If there's a preselected sitio from old() data
                var currentSitio = $sitio.val();
                if (currentSitio) {
                    setTimeout(function() {
                        $sitio.trigger('change');
                    }, 100);
                }
            }

            // ============================================
            // FORM SUBMISSION
            // ============================================

            function initFormSubmission() {
                $('#residentForm').on('submit', function (e) {
                    e.preventDefault();

                    var form       = this;
                    var formData   = new FormData(form);
                    var $submitBtn = $(form).find('button[type="submit"]');
                    var origHtml   = $submitBtn.html();

                    $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving…');

                    if (typeof CSRF_TOKEN_NAME !== 'undefined') {
                        formData.append(CSRF_TOKEN_NAME, CSRF_TOKEN_VALUE);
                    }

                    $.ajax({
                        url:         $(form).attr('action'),
                        type:        'POST',
                        data:        formData,
                        processData: false,
                        contentType: false,
                        dataType:    'json',
                        success: function (response) {
                            if (response.status === 'success') {
                                if (response.csrf_hash) {
                                    CSRF_TOKEN_VALUE = response.csrf_hash;
                                    $('input[name="' + CSRF_TOKEN_NAME + '"]').val(response.csrf_hash);
                                }
                                alert('Resident saved successfully!');
                                setTimeout(function () {
                                    window.location.href = BASE_URL + 'resident';
                                }, 1000);
                            } else {
                                var msg = response.message || 'Error saving resident';
                                if (response.errors) {
                                    msg = Object.values(response.errors).join('\n');
                                }
                                alert('Error: ' + msg);
                                $submitBtn.prop('disabled', false).html(origHtml);
                            }
                        },
                        error: function () {
                            alert('Error saving resident. Please try again.');
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
                        alert('File size exceeds 2 MB limit');
                        $(this).val('');
                        return;
                    }

                    var reader = new FileReader();
                    reader.onload = function (ev) {
                        $('#profilePreview').remove();
                        $('input[name="profile_picture"]').after(
                            '<div id="profilePreview" class="mt-2">' +
                            '<img src="' + ev.target.result + '" ' +
                            'class="img-thumbnail rounded-circle" ' +
                            'style="width:80px;height:80px;object-fit:cover;">' +
                            '</div>'
                        );
                    };
                    reader.readAsDataURL(file);
                });
            }

            // ============================================
            // AGE CALCULATOR
            // ============================================

            function initAgeCalculator() {
                $('input[name="birthdate"]').on('change', function () {
                    var birthdate = $(this).val();
                    if (!birthdate) { return; }

                    var today = new Date();
                    var birth = new Date(birthdate);
                    var age   = today.getFullYear() - birth.getFullYear();
                    var m     = today.getMonth() - birth.getMonth();
                    if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) { age--; }

                    if (age >= 60) {
                        $('input[name="is_senior_citizen"]').prop('checked', true);
                    }
                });
            }

            // ============================================
            // INIT
            // ============================================

            initDependentDropdown();
            initFormSubmission();
            initFileUploadPreview();
            initAgeCalculator();
        });
    }

    // Start initialization
    initWhenReady();
})();