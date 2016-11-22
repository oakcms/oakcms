if (typeof pistol88 == "undefined" || !pistol88) {
    var pistol88 = {};
}

pistol88.fieldvariant = {
    csrf_param: '_csrf',
    csrf_token: '',
    init: function() {
        $(document).on('change', '.pistol88-field input[type=radio], input[type=checkbox], .pistol88-field select', this.choiceVariant);

        pistol88.fieldvariant.csrf_param = $('meta[name="csrf-param"]').attr('content');
        pistol88.fieldvariant.csrf_token = $('meta[name="csrf-token"]').attr('content');
    },
    choiceVariant: function() {
        var li = $(this).parent();

        var field_id = $(this).parents('.field-data-container').data('id');
        var item_id = $(this).parents('.field-data-container').data('item-id');

        var create_action = $(this).parents('.field-data-container').data('create-action');
        var update_action = $(this).parents('.field-data-container').data('update-action');
        var delete_action = $(this).parents('.field-data-container').data('delete-action');

        if($(this).is('select') | $(this).is('input[type=radio]')) {
            var variant_id = $(this).val();
            if(variant_id <= 0) {
                $.post(delete_action, {field_id: field_id, variant_id: variant_id, item_id: item_id},
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
                data.FieldValue = {};
                data.FieldValue.variant_id = variant_id;
                data.FieldValue.field_id = field_id;
                data.FieldValue.item_id = item_id;
                data[pistol88.fieldvariant.csrf_param] = pistol88.fieldvariant.csrf_token;

                $.post(update_action, data,
                    function(json) {
                        $(li).css('opacity', '1');
                    }, "json");
            }
        }
        else if($(this).prop('checked')) {
            var variant_id = $(this).data('id');
            var data = {};
            data.FieldValue = {};
            data.FieldValue.variant_id = variant_id;
            data.FieldValue.field_id = field_id;
            data.FieldValue.item_id = item_id;
            data[pistol88.fieldvariant.csrf_param] = pistol88.fieldvariant.csrf_token;

            $.post(create_action, data,
                function(json) {
                    $(li).css('opacity', '1');
                }, "json");
        }
        else {
            var variant_id = $(this).data('id');
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

pistol88.fieldvariant.init();
