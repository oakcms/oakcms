if (typeof oakcms == "undefined" || !oakcms) {
    var oakcms = {};
}

oakcms.filterAjax = {
    rangeDelay: false,
    init: function() {
        $(document).on('change', '.oakcms-filter select, .oakcms-filter input[type=checkbox], .oakcms-filter input[type=radio]', this.renderResults);
        $(document).on('change', '.oakcms-filter input[type=text]', function() {
            if(oakcms.filterAjax.rangeDelay) {
                clearTimeout(oakcms.filterAjax.rangeDelay);
            }

            oakcms.filterAjax.rangeDelay = setTimeout(function() {
                oakcms.filterAjax.renderResults();
            }, 800);
        });
    },
    renderResults: function() {
        var data = $('.oakcms-filter').serialize();
        var resultHtmlSelector = $('.oakcms-filter').data('resulthtmlselector');

        $(resultHtmlSelector).css('opacity', 0.3);
        $(resultHtmlSelector).load(location.protocol + '//' + location.host + location.pathname+'?'+data+' '+resultHtmlSelector, function() {
            $(resultHtmlSelector).css('opacity', 1);
        });

        return false;
    }
};

oakcms.filterAjax.init();
