/**
 * Blotter Edit Page
 * Handles dynamic party management and TomSelect init
 */

$(document).ready(function() {
    let partyIndex = window.blotterConfig.partyIndex || 0;

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
                render: {
                    option: function(data, escape) {
                        return '<div>' + escape(data.text) + '</div>';
                    },
                    item: function(data, escape) {
                        return '<div>' + escape(data.text) + '</div>';
                    }
                },
                shouldLoad: function(query) {
                    return query.length >= 2;
                }
            });
        });
    }

    initTomSelect('.resident-select');

    $('#add-party-btn').click(function() {
        let html = `
        <div class="party-entry card card-outline card-secondary mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <select name="parties[${partyIndex}][role]" class="form-control role-select" required>
                            <option value="complainant">Complainant</option>
                            <option value="respondent">Respondent</option>
                            <option value="witness">Witness</option>
                        </select>
                    </div>
                    <div class="col-md-9">
                        <label>Type</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input type-toggle" type="radio" name="parties[${partyIndex}][type]" value="resident" data-index="${partyIndex}">
                            <label class="form-check-label">Resident</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input type-toggle" type="radio" name="parties[${partyIndex}][type]" value="outsider" checked data-index="${partyIndex}">
                            <label class="form-check-label">Outsider</label>
                        </div>
                    </div>
                </div>
                <div class="resident-fields" style="display:none;">
                    <div class="form-group">
                        <label>Select Resident</label>
                        <select name="parties[${partyIndex}][resident_id]" class="resident-select" style="width:100%;"></select>
                    </div>
                </div>
                <div class="outsider-fields">
                    <div class="row">
                        <div class="col-md-6"><input type="text" name="parties[${partyIndex}][outsider_name]" class="form-control" placeholder="Full name"></div>
                        <div class="col-md-6"><input type="text" name="parties[${partyIndex}][outsider_address]" class="form-control" placeholder="Address"></div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-danger remove-party">Remove</button>
            </div>
        </div>`;
        let newEntry = $(html);
        $('#parties-container').append(newEntry);
        initTomSelect(newEntry.find('.resident-select'));
        partyIndex++;
    });

    $(document).on('change', '.type-toggle', function() {
        let index = $(this).data('index');
        let val = $(this).val();
        let entry = $(this).closest('.party-entry');
        entry.find('.resident-fields').toggle(val === 'resident');
        entry.find('.outsider-fields').toggle(val === 'outsider');
    });

    $(document).on('click', '.remove-party', function() {
        $(this).closest('.party-entry').remove();
    });
});