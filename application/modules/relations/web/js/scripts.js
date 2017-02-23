if (typeof oakcms == "undefined" || !oakcms) {
    var oakcms = {};
}

oakcms.relations = {
    init: function () {
        $('.oakcms-relations-variant').on('click', this.addRelation);
        $(document).on('click', '.oakcms-relation-delete', this.deleteRelation)
    },
    deleteRelation: function() {
        $(this).parent('.oakcms-delete-column').parent('.row').remove();
        return false;
    },
    addRelation: function() {
        $(this).addClass('selected');

        setTimeout(function() { $('.selected').removeClass('selected') }, 200);

        oakcms.relations.renderRow($(this).data('model'), $(this).data('id'), $(this).data('name'));

        return false;
    },
    renderRow: function(model, id, name) {
        var idColumn = '<div class="col-lg-1 col-xs-1">'+id+'<input type="hidden" name="relations_models[]" value="'+model+'" /><input type="hidden" name="relations_ids[]" value="'+id+'" /></div>';
        var nameColumn = '<div class="col-lg-6 col-xs-10">'+name+'</div>';
        var deleteColumn = '<div class="col-lg-1 col-xs-1 oakcms-delete-column"><a href="#" class="oakcms-relation-delete glyphicon glyphicon-trash"></a></div>';

        $('.oakcms-relations', window.parent.document).append('<div class="row oakcms-relation-row">'+idColumn+nameColumn+deleteColumn+'</div>');

        return true;
    }
}

oakcms.relations.init();
