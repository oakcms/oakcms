/*
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

/**
 * Created by Володимир on 24.11.2015.
 */
var UINestable = function () {

    var updateOutput = function (e) {
        var list = e.length ? e : $(e.target),
            url = list.data('url');

        $.post(url, {list: list.nestable('serialize')}, function(response){
            var type, message;
            if(response.result === 'success') {
                type = 'success';
                message = response.success;
            } else if(response.result === 'error') {
                message = response.error;
            }
            $.bootstrapGrowl(message, {
                ele: 'body',
                type: type,
                offset: {
                    from: 'bottom',
                    amount: 100
                },
                align: 'right',
                width: 'auto',
                delay: 15000,
                allow_dismiss: true,
                stackup_spacing: 10
            });
        }, "json");
    };

    return {
        //main function to initiate the module
        init: function () {
            $('.nestable_list').nestable().on('change', updateOutput);
        }
    };

}();

jQuery(document).ready(function() {
    UINestable.init();
});
