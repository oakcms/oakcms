/**
 * Created by Володимир on 06.04.2016.
 */

$(document).ready(function () {
    $("input:checkbox:not(.switch, .make-switch), input:radio:not(.switch, .make-switch)").uniform();

    $(".select-on-check-all").change(function () {
        $.uniform.update()
    });

    $('.grid-view tbody .checker input').change(function () {
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

    $(".nav-tabs-custom .nav a").on('click', function(){
        sessionStorage.activeTab = $(this).attr('href');
        sessionStorage.page = window.location.href;
    });
    if(window.location.href == sessionStorage.page) {
        $('.nav-tabs-custom .nav a[href="' + sessionStorage.activeTab + '"]').tab('show');
    }
});
