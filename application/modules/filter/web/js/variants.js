if (typeof oakcms == "undefined" || !oakcms) {
    var oakcms = {};
}

oakcms.filter = {
    csrf_param: '_csrf',
    csrf_token: '',
    init: function() {
        $(document).on('change', '.oakcms-filter input, .oakcms-filter select', this.choiceVariant);
        $(document).on('click', '.oakcms-filter .new-variant button', this.addVariant);
        $(document).on('keypress', '.oakcms-filter .new-variant input', function(e) {
            if(e.which == 13) {
                $(this).siblings('button').click();
            }
        });
        oakcms.filter.csrf_param = $('meta[name="csrf-param"]').attr('content');
        oakcms.filter.csrf_token = $('meta[name="csrf-token"]').attr('content');
    },
    addVariant: function() {
        var input = $(this).closest('.new-variant').find('input');
        var filter_id = input.data('filter-id');
        var value = input.val();

        input.css('opacity', '0.3');

        var create_action = input.data('create-action');

        console.log(input.data('create-action'));
        var data = {};
        data.FilterVariant = {};
        data.FilterVariant.filter_id = filter_id;
        data.FilterVariant.value = value;
        data.json = true;
        data[oakcms.filter.csrf_param] = oakcms.filter.csrf_token;

        $.post(create_action, data,
            function(json) {
                if(json.result == 'success') {
                    switch (json.type) {
                        case 'radio':
                            input.val('');
                            input.css('opacity', 1);
                            var newState = new Option(value, json.id, true, true);
                            input.closest('.oakcms-filter')
                                .find('#selectVariantsList')
                                .append(newState).trigger('change');
                            break;
                        case 'checkbox':
                            input.val('');
                            input.css('opacity', 1);

                            if(json.new) {
                                input
                                    .closest('.oakcms-filter')
                                    .find('ul.filter-data-container')
                                    .append('<li>' +
                                        '<input ' +
                                        'checked="checked" ' +
                                        'type="checkbox" ' +
                                        'id="filtervariant'+json.id+'" ' +
                                        'name="variant" ' +
                                        'value="1" ' +
                                        'data-id="'+json.id+'">' +
                                        '<label for="filtervariant'+json.id+'">'+value+'</label>' +
                                        '</li>');
                            } else {
                                $('#filtervariant'+json.id).prop('checked', true);
                            }
                            $('#filtervariant'+json.id).change();
                            $("input[type=checkbox]").uniform();
                        break;
                    }
                } else {
                    alert('Error');
                }
            }, "json");
    },
    choiceVariant: function() {
        var li = $(this).parent();

        var variant_id = $(this).data('id');

        var filter_id = $(this).parents('.filter-data-container').data('id');
        var item_id = $(this).parents('.filter-data-container').data('item-id');

        var create_action = $(this).parents('.filter-data-container').data('create-action');
        var update_action = $(this).parents('.filter-data-container').data('update-action');
        var delete_action = $(this).parents('.filter-data-container').data('delete-action');

        if($(this).is('select')) {
            variant_id = $(this).val();
            if(variant_id <= 0) {
                $.post(delete_action, {filter_id: filter_id, variant_id: variant_id, item_id: item_id},
                    function(json) {
                        if(json.result == 'success') {
                            $(li).css('opacity', '1');
                        }
                        else {
                            alert('Error');
                        }
                    }, "json");
            }
            else {
                var data = {};
                data.FilterValue = {};
                data.FilterValue.variant_id = variant_id;
                data.FilterValue.filter_id = filter_id;
                data.FilterValue.item_id = item_id;
                data[oakcms.filter.csrf_param] = oakcms.filter.csrf_token;

                $.post(update_action, data,
                    function(json) {
                        $(li).css('opacity', '1');
                    }, "json");
            }
        }
        else if($(this).prop('checked')) {
            var data = {};
            data.FilterValue = {};
            data.FilterValue.variant_id = variant_id;
            data.FilterValue.filter_id = filter_id;
            data.FilterValue.item_id = item_id;
            data[oakcms.filter.csrf_param] = oakcms.filter.csrf_token;

            $.post(create_action, data,
                function(json) {
                    $(li).css('opacity', '1');
                }, "json");
        }
        else {
            $.post(delete_action, {variant_id: variant_id, item_id: item_id},
                function(json) {
                    if(json.result == 'success') {
                        $(li).css('opacity', '1');
                    }
                    else {
                        alert('Error');
                    }
                }, "json");
        }
    },
};

oakcms.filter.init();
