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
});
