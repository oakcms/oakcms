if (typeof oakcms == "undefined" || !oakcms) {
    var oakcms = {};
}

oakcms.relations = function() {
    var handleDeleteRelation = function() {
        $(document).on('click', '.oakcms-relation-delete', function () {
            $(this).closest('li.js-relation-row').remove();
            return false;
        });
    };

    var handleAddRelation = function() {
        $(document).on('click', '.oakcms-relations-variant', function() {
            $(this).addClass('selected');
            setTimeout(function() { $('.selected').removeClass('selected') }, 200);
            oakcms.relations.renderRow($(this).data('model'), $(this).data('id'), $(this).data('name'));

            return false;
        });
    };

    var handleRenderRow = function(model, id, name) {
        if($('.oakcms-relations', window.parent.document).find('[data-id="'+id+'"]').length === 0) {
            var idColumn = '<div class="cont-col1"><div class="label label-sm label-default">' + id + '<input type="hidden" name="relations_models[]" value="' + model + '" /><input type="hidden" name="relations_ids[]" value="' + id + '" /></div></div>';
            var nameColumn = '<div class="cont-col2"><div class="desc">' + name + '</div></div>';
            var deleteColumn = '<div class="oakcms-delete-column pull-right"><a href="javascript:void(0)" data-id="' + id + '" class="oakcms-relation-delete label label-sm label-danger"><i class="glyphicon glyphicon-trash"></i></a></div>';

            $('.oakcms-relations', window.parent.document).append('<li data-id="' + id + '" class="js-relation-row"><div class="col1"><div class="cont">' + idColumn + nameColumn + '</div></div><div class="col2">' + deleteColumn + '</div></li>');

            return true;
        }
        return false;
    };

    return {
        init: function () {
            handleAddRelation();
            handleDeleteRelation();
        },
        renderRow: function(model, id, name) {
            handleRenderRow(model, id, name);
        }
    }
}();
oakcms.relations.init();
