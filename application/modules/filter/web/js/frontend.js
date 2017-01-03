if (typeof oakcms == "undefined" || !oakcms) {
    var oakcms = {};
}

oakcms.filter = {
    init: function() {
        jQuery.fn.bstooltip = jQuery.fn.tooltip;
        $('body').bstooltip({
            selector: '[data-toggle="tooltip"]',
            container: 'body'
        });
    },
};

oakcms.filter.init();
