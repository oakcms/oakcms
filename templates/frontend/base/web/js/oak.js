/**
 * Created by Володимир on 08.04.2016.
 */


var Oak = new function() {
    var self = this;

};

var BootstrapTooltip = function () {
    return {
        init: function () {

        }
    };
}();

jQuery(document).ready(function() {
    BootstrapTooltip.init();

    $('[data-toggle="tooltip"]').tooltip();

    var icb = $('#oak_admin_bar .simple-switch');
    icb.simpleSwitch();
    $('#oak_admin_bar').on('change', '.simple-switch', function(){
        var cb = $(this);
        cb.prop('disabled', true);
        location.href = cb.attr('data-url') + '/' + (cb.is(':checked') ? 1 : 0);
    });
});