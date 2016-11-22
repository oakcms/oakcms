if (typeof pistol88 == "undefined" || !pistol88) {
    var pistol88 = {};
}

pistol88.relations = {
    init: function () {
        $('.pistol88-relations-variant').on('click', this.addRelation);
        $(document).on('click', '.pistol88-relation-delete', this.deleteRelation)
    },
    deleteRelation: function() {
        $(this).parent('.pistol88-delete-column').parent('.row').remove();
        return false;
    },
    addRelation: function() {
        $(this).addClass('selected');
        
        setTimeout(function() { $('.selected').removeClass('selected') }, 200);

        pistol88.relations.renderRow($(this).data('model'), $(this).data('id'), $(this).data('name'));

        return false;
    },
    renderRow: function(model, id, name) {
        var idColumn = '<div class="col-lg-1 col-xs-1">'+id+'<input type="hidden" name="relations_models[]" value="'+model+'" /><input type="hidden" name="relations_ids[]" value="'+id+'" /></div>';
        var nameColumn = '<div class="col-lg-6 col-xs-10">'+name+'</div>';
        var deleteColumn = '<div class="col-lg-1 col-xs-1 pistol88-delete-column"><a href="#" class="pistol88-relation-delete glyphicon glyphicon-trash"></a></div>';

        $('.pistol88-relations', window.parent.document).append('<div class="row pistol88-relation-row">'+idColumn+nameColumn+deleteColumn+'</div>');
        
        return true;
    }
}

pistol88.relations.init();