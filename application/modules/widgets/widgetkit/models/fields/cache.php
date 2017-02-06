<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1
 */
?>
<button id='wk-clear-cache' class='btn'>Clear Cache</button>
<span class='wk-cache-size' style="padding-left: 15px;"></span>

<script>
jQuery(function($) {
    var getCache = function() {
        $.get('admin/widgets/widgetkit&p=/cache/get', function(data) {
            $('.wk-cache-size').text(JSON.parse(data));
        });
    };
    $('#wk-clear-cache').on('click', function(e) {

        e.preventDefault();

        $('.wk-cache-size').text('Clearing cache...');
        $.get('admin/widgets/widgetkit&p=/cache/clear', getCache);
    });
    getCache();
});
