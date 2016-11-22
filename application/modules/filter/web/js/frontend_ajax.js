if (typeof pistol88 == "undefined" || !pistol88) {
    var pistol88 = {};
}

pistol88.filterAjax = {
    rangeDelay: false,
    init: function() {
        $(document).on('change', '.pistol88-filter select, .pistol88-filter input[type=checkbox], .pistol88-filter input[type=radio]', this.renderResults);
        $(document).on('change', '.pistol88-filter input[type=text]', function() {
            if(pistol88.filterAjax.rangeDelay) {
                clearTimeout(pistol88.filterAjax.rangeDelay);
            }
            
            pistol88.filterAjax.rangeDelay = setTimeout(function() {
                pistol88.filterAjax.renderResults();
            }, 800);
        });
    },
    renderResults: function() {
        var data = $('.pistol88-filter').serialize();
        var resultHtmlSelector = $('.pistol88-filter').data('resulthtmlselector');
        
        $(resultHtmlSelector).css('opacity', 0.3);
        $(resultHtmlSelector).load(location.protocol + '//' + location.host + location.pathname+'?'+data+' '+resultHtmlSelector, function() {
            $(resultHtmlSelector).css('opacity', 1);
        });
        
        return false;
    }
};

pistol88.filterAjax.init();
