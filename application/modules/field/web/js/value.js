if (typeof pistol88 == "undefined" || !pistol88) {
    var pistol88 = {};
}

pistol88.fieldvalue = {
    csrf_param: '_csrf',
    csrf_token: '',
    init: function() {
        $(document).on('change', '.pistol88-field input[type=text], .pistol88-field input[type=number], .pistol88-field input[type=date], .pistol88-field textarea', this.setValue);

        $(document).on('click', '.field-save-value', function() {
            $(this).parent('span').siblings('input').change();
            return false;
        });

        $(document).on('keypress', '.pistol88-field input[type=text]', function(e) {
            if(e.which == 13) {
                $(this).change();
            }
        });

        pistol88.fieldvalue.csrf_param = $('meta[name="csrf-param"]').attr('content');
        pistol88.fieldvalue.csrf_token = $('meta[name="csrf-token"]').attr('content');
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
        data[pistol88.fieldvalue.csrf_param] = pistol88.fieldvalue.csrf_token;

        $.post(update_action, data,
            function(json) {
                
            }, "json");
    },
};

pistol88.fieldvalue.init();
