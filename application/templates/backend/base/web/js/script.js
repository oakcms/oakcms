/**
 * Created by Володимир on 06.04.2016.
 */
// Grow message
function grow(message, type) {
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
}

$(document).ready(function () {
    $("input:checkbox:not(.switch, .make-switch), input:radio:not(.switch, .make-switch)").uniform();

    $(document).on('pjax:complete', function() {
        $("input:checkbox:not(.switch, .make-switch), input:radio:not(.switch, .make-switch)").uniform();
        $.uniform.update();
    });

    $(document).on('change', '.select-on-check-all, .grid-view tbody .checker input', function () {
        $.uniform.update();
    });

    $(".select2").select2();
    $('.switch').switcher({copy: {en: {yes: '', no: ''}}}).on('change', function () {
        var checkbox = $(this);
        checkbox.switcher('setDisabled', true);
        $.getJSON(checkbox.data('link') + '/' + (checkbox.is(':checked') ? 'on' : 'off') + '/' + checkbox.data('id'), function (response) {
            if (response.result === 'error') {
                alert(response.error);
            }
            if (checkbox.data('reload')) {
                location.reload();
            } else {
                checkbox.switcher('setDisabled', false);
            }
        });
    });

    // Зберігаєм активну вкладку
    $(".nav-tabs-custom .nav a").on('click', function () {
        sessionStorage.activeTab = $(this).attr('href');
        sessionStorage.page = window.location.href;
    });

    if (window.location.href == sessionStorage.page) {
        $('.nav-tabs-custom .nav a[href="' + sessionStorage.activeTab + '"]').tab('show');
    }
});

var OakCMS = function () {

    var handleBootstrapSwitch = function () {
        if (!$().bootstrapSwitch) {
            return;
        }
        $('.make-switch').bootstrapSwitch();
    };

    var handleFancybox = function () {
        if (!jQuery.fancybox) {
            return;
        }

        if ($(".fancybox-button").size() > 0) {
            $(".fancybox-button").fancybox({
                groupAttr: 'data-rel',
                prevEffect: 'none',
                nextEffect: 'none',
                closeBtn: true,
                helpers: {
                    title: {
                        type: 'inside'
                    }
                }
            });
        }
    };

    /**
     * Системні кнопки кешу
     */
    var handleSystemCacheButtons = function () {
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
                success: function (data) {
                    $.bootstrapGrowl(data.success, {
                        ele: 'body',
                        type: 'success',
                        offset: {
                            from: 'bottom',
                            amount: 10
                        },
                        align: 'right',
                        width: 'auto',
                        delay: 5000,
                        allow_dismiss: true,
                        stackup_spacing: 10
                    });
                },
                complete: function () {
                    $t.html(icon);
                }
            });
        });
    };

    return {
        init: function () {
            handleFancybox();
            handleBootstrapSwitch();
            handleSystemCacheButtons();
        },

        initFancybox: function () {
            handleFancybox();
        },

        initBootstrapSwitch: function () {
            handleBootstrapSwitch();
        },

        initSystemCacheButtons: function () {
            handleSystemCacheButtons();
        },

        initAjax: function () {
            handleFancybox();
            handleBootstrapSwitch();
        }
    }
}();

jQuery(document).ready(function () {
    OakCMS.init();
});
