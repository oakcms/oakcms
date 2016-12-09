/*
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

/**
 * Created by Volodymyr Hryvinskyi on 01.12.2016.
 */

(function ($) {
    $(document).ready(function () {
        $('.js-oak-flush-cache, .js-oak-clear-assets').on('click', function (e) {
            e.preventDefault();
            var $t = $(this),
                icon = $t.html();
            $.ajax({
                url: $t.attr('href'),
                dataType: 'json',
                beforeSend: function () {
                    $t.html('<i class="fa fa-spinner fa-spin"></i>');
                },
                success: function (data) {},
                complete: function () {
                    $t.html(icon);
                }
            });
        });
    });
})(jQuery);
