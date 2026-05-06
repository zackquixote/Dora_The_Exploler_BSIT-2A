$(document).ready(function() {
    let partyIndex = window.blotterConfig.partyIndex || 2;

    function initTomSelect(selector) {
        $(selector).each(function() {
            if (this.tomselect) return;
            new TomSelect(this, {
                valueField: 'id',
                labelField: 'text',
                searchField: ['text'],
                maxOptions: 10,
                load: function(query, callback) {
                    if (!query.length) return callback();
                    $.get(window.blotterConfig.searchUrl, { q: query }, function(res) {
                        callback(res);
                    });
                },
                shouldLoad: function(query) { return query.length >= 2; }
            });
        });
    }

    initTomSelect('.resident-select');

    $('#add-party-btn').click(function() {
        let html = `
        <div class="party-entry card mb-3 border-primary">
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label>Role</label>
                        <select name="parties[${partyIndex}][role]" class="form-control" required>
                            <option value="complainant">👤 Complainant</option>
                            <option value="respondent">👥 Respondent</option>
                            <option value="witness">👁️ Witness</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Type</label>
                        <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                            <label class="btn btn-outline-primary btn-sm">
                                <input type="radio" name="parties[${partyIndex}][type]" value="resident" autocomplete="off"> Resident
                            </label>
                            <label class="btn btn-outline-secondary btn-sm active">
                                <input type="radio" name="parties[${partyIndex}][type]" value="outsider" autocomplete="off" checked> Outsider
                            </label>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="resident-fields" style="display:none;">
                            <label>Search Resident</label>
                            <select name="parties[${partyIndex}][resident_id]" class="resident-select" style="width:100%"></select>
                        </div>
                        <div class="outsider-fields">
                            <div class="row">
                                <div class="col-md-6"><input type="text" name="parties[${partyIndex}][outsider_name]" class="form-control" placeholder="Full name"></div>
                                <div class="col-md-6"><input type="text" name="parties[${partyIndex}][outsider_address]" class="form-control" placeholder="Address"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-auto mt-2">
                        <button type="button" class="btn btn-danger btn-sm remove-party"><i class="fas fa-trash-alt"></i> Remove</button>
                    </div>
                </div>
            </div>
        </div>`;
        let $new = $(html);
        $('#parties-container').append($new);
        initTomSelect($new.find('.resident-select'));
        partyIndex++;
    });

    $(document).on('change', '.btn-group-toggle input', function() {
        let $entry = $(this).closest('.party-entry');
        let isResident = $(this).val() === 'resident';
        $entry.find('.resident-fields').toggle(isResident);
        $entry.find('.outsider-fields').toggle(!isResident);
    });

    $(document).on('click', '.remove-party', function() {
        $(this).closest('.party-entry').remove();
    });

    // Trigger initial toggle for existing radio buttons
    $('.btn-group-toggle input:checked').each(function() {
        $(this).trigger('change');
    });
});