if (typeof oak == "undefined" || !oak) {
    var oak = {};
}

oak.changestatus = {
    csrf: null,
    csrf_param: null,
    init: function() {
        oak.changestatus.csrf = jQuery('meta[name=csrf-token]').attr("content");
        oak.changestatus.csrf_param = jQuery('meta[name=csrf-param]').attr("content");
        $(document).on('change', ".oak-change-order-status", this.changeStatus);
    },
    changeStatus: function() {
        var link = $(this);
        $(link).css('opacity', '0.2');

        data = {};
        data['status'] = $(this).val();
        data['id'] = $(this).data('id');
        data[oak.changestatus.csrf_param] = oak.changestatus.csrf;

        jQuery.post($(this).data('link'), data,
            function(json) {
                if(json.result == 'success') {
                    $(link).css('opacity', '1');
                }
                else {
                    console.log(json.error);
                }

            }, "json");

        return false;
    },
};

oak.changestatus.init();
