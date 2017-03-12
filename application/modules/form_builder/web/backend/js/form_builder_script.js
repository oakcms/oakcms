/*
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */
$(document).ready(function () {
    var fieldsButtons       = $("#grid_form_fields_buttons"),
        fieldsGrid          = $("#grid_form_fields"),
        fieldsButtonDel     = $("#grid_form_field_delete"),
        fieldsGridCheckbox  = fieldsGrid.find('input[name="selection[]"]'),
        renderFormBuilder   = $('#renderFormBuilder'),
        submissionVariables = $('.js-submission_variables'),
        templateVariables   = $('#template_variables'),
        emailFieldUser      = $('#email_field_user'),
        switcherEditHtml    = $('#switcher_edit_html');

    function getSelectedRows(fieldsGrid) {
        return fieldsGrid.yiiGridView('getSelectedRows');
    }

    window.fieldsUpdateTemplate = function () {
        if(!switcherEditHtml.is(':checked')) {
            $.get(renderFormBuilder.data('url'), function( data ) {
                ace.edit("design_html").setValue(data, -1);
            });
        }

        $.get(submissionVariables.data('url'), function(data) {
            submissionVariables.html(data);
        });

        $.get(templateVariables.data('url'), function(data) {
            templateVariables.html(data);
        });

        $.getJSON(emailFieldUser.data('url'), function(data) {
            var options = emailFieldUser.data('select2').options.options;
            emailFieldUser.html('');
            for (var i = 0; i < data.length; i++) {
                if(data[i]['id'] == emailFieldUser.data('selected')) {
                    emailFieldUser.append("<option value=\"" + data[i]['id'] + "\" selected='selected'>" + data[i]['text'] + "</option>");
                } else {
                    emailFieldUser.append("<option value=\"" + data[i]['id'] + "\">" + data[i]['text'] + "</option>");
                }
            }
            options.data = data;
            emailFieldUser.select2(options);
        });
    };

    fieldsUpdateTemplate();

    $(document).on('switchChange.bootstrapSwitch', switcherEditHtml, function(event, state){
        ace.edit("design_html").setReadOnly(!state);
        fieldsUpdateTemplate();
    });

    $(document).on('change', fieldsGridCheckbox, function () {
        var count = getSelectedRows(fieldsGrid);
        if(count.length > 0) {
            fieldsButtons.fadeIn();
            fieldsButtonDel.attr('href', fieldsButtonDel.data('url') + '?id=' + getSelectedRows(fieldsGrid));
        } else {
            fieldsButtons.fadeOut();
            fieldsButtonDel.attr('href', '#');
        }
    });
});

