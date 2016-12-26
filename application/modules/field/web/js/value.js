if (typeof oakcms == "undefined" || !oakcms) {
    var oakcms = {};
}

oakcms.fieldvalue = {
    csrf_param: '_csrf',
    csrf_token: '',
    init: function() {
        $(document).on('change', '.oakcms-field input[type=text], .oakcms-field input[type=number], .oakcms-field input[type=date], .oakcms-field textarea', this.setValue);

        $(document).on('click', '.field-save-value', function() {
            $(this).parent('span').siblings('input').change();
            return false;
        });

        $(document).on('keypress', '.oakcms-field input[type=text]', function(e) {
            if(e.which == 13) {
                $(this).change();
            }
        });

        oakcms.fieldvalue.csrf_param = $('meta[name="csrf-param"]').attr('content');
        oakcms.fieldvalue.csrf_token = $('meta[name="csrf-token"]').attr('content');
    },
    setValue: function() {
        var value = $(this).val();
        var field_id = $(this).data('id');
        var item_id = $(this).data('item-id');
        var update_action = $(this).parents('.field-data-container').data('update-action');

        var data = {};
        data.FieldValue = {};
        data.FieldValue.field_id = field_id;
        data.FieldValue.item_id = item_id;
        data.FieldValue.value = value;
        data[oakcms.fieldvalue.csrf_param] = oakcms.fieldvalue.csrf_token;

        $.post(update_action, data,
            function(json) {

            }, "json");
    },
};

oakcms.fieldvalue.init();
