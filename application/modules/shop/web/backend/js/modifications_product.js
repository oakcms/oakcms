/*
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

$.exists = function (selector) {
    return ($(selector).length > 0);
};

$(function() {
    $("#addVariant").on("click", function(){
        var variantsTable = $('#modifications-table tbody');
        var mainImageName = variantsTable.find('tr').last().find('.mainImageName').val();
        var countVarRows = variantsTable.children('tr').length;
        var clonedVarTr = $(
            variantTemplate
                .replace(/\{\{id\}\}/g, countVarRows)
                .replace(/\{\{variantsId\}\}/g, '')
                .replace(/\{\{price\}\}/g, '')
                .replace(/\{\{code\}\}/g, '')
                .replace(/\{\{amount\}\}/g, '')
                .replace(/\{\{name\}\}/g, '')
        );
        clonedVarTr.find('.mainImageName').val(mainImageName);
        var imageFile = variantsTable.find('tr').last().find('input[name*="image"]');

        mainImageName = imageFile[0].files.length ? imageFile[0].files[0]['name'] : mainImageName;
        mainImageName = mainImageName ? mainImageName : 'https://placeholdit.imgix.net/~text?txtsize=20&txt=120%C3%97120&w=120&h=120';

        var src = $('#modifications-table tbody').find('tr').last().find('.photo-block > img').attr('src');

        clonedVarTr.find('.photo-block > img').attr('src', src);

        clonedVarTr.attr('id', 'ProductModificationRow_' + countVarRows);
        clonedVarTr.find('.add-image-color')
            .attr('data-path', $('#modifications-table tbody').data('path'))
            .attr('data-id', countVarRows);

        variantsTable.append(clonedVarTr);
        $(window).scrollTop($(window).scrollTop() + 59);
        $('#ProductModificationRow_' + countVarRows).children('td.number:first').find('input').attr('id', 'price-product-' + countVarRows);
        $('#ProductModificationRow_' + countVarRows).children('td.number:first').find('input').attr('onkeyup', "checkLenghtStr('price-product-" + countVarRows + "', 11, 5, event.keyCode);");

        $('#ProductModificationRow_' + countVarRows).children('td.number:last').find('input').attr('id', 'stock-len-' + countVarRows);
        $('#ProductModificationRow_' + countVarRows).children('td.number:last').find('input').attr('onkeyup', "checkLenghtStr('stock-len-" + countVarRows + "', 9, 0, event.keyCode);");

        $('.name-var-def').attr('disabled', false);

        $(".tooltips").tooltip();
        $('.select2me').select2();
    });

    $("body").on("click", ".change_image", function () {
        $(this).closest('td').find('[type="file"]').attr('accept', "image/gif, image/jpeg, image/png").click();
    });

    $('body').off('change', '[data-url="file"] input[type="file"]').on('change', '[data-url="file"] input[type="file"]', function (e) {

        var $this = $(this),
            $type_file = $this.val(),
            file = this.files[0],
            img = document.createElement("img"),
            reader = new FileReader();
        reader.onloadend = function () {
            img.src = reader.result;
        };
        reader.readAsDataURL(file);

        img.onerror = function () {
            img.src = 'https://placeholdit.imgix.net/~text?txtsize=20&txt=120%C3%97120&w=120&h=120';
            alert('Not supported file format');
            return;
        };

        $(this).closest('.control-group').find('.photo-block').html(img);
        $this.parent().next().val($type_file).attr('data-rel', 'tooltip');
        $(this).closest('td').find('.changeImage').val('1');
    });

    $('body').on('click', '.delete_image', function () {
        var container = $(this).closest('td');


        container.find(".deleteImage").attr("value", 1);
        container.find('[name="variants[mainPhoto][]"]').attr('value', '');
        container.find('img').attr('src', "https://placeholdit.imgix.net/~text?txtsize=20&txt=120%C3%97120&w=120&h=120");
        container.find('img').css('width', '50px');

        $(this).closest('.variantImage').find('.delete_image').hide();

    });

    $(document).on('click', '.add-image-color', function(){
        var id = $(this).data('id'), path = $(this).data('path');
        mihaildev.elFinder.register("image-color-w"+id, function(file, id){
            $('#' + id).val(file.url).trigger('change');
            return true;
        });
        mihaildev.elFinder.openManager({
            "url"       : "/file-manager-elfinder/manager?filter=image&callback=image-color-w"+id+"&lang=ru&path="+path,
            "width"     : "auto",
            "height"    : "auto",
            "id"        : "image-color-w"+id
        });
    });

    $("body").on("click", ".remove_variant", function(){
        var tr = $(this).closest("tr"),
            id = tr.find('[name="variants[id][]"]').attr('value'),
            url = $(this).closest(".save_positions").data('delete');

        tr.fadeOut(function(){
            $(this).remove();
            if(id != '') {
                $.post(url + '/' + id, function( data ) {
                    if(data.result == 'success') {
                        grow(data.message, 'success');
                    } else {
                        grow(data.error, 'danger');
                    }
                });
            }
        });
    });

    function save_positions(url, productId) {
        var arr = new Array();
        $('input[name="variants[id][]"]').each(function () {
            if($(this).val() != '')
            arr.push($(this).val());
        });
        if(arr.length > 0) {
            $.post(url, {positions: arr, productId: productId}, function (data) {
                if(data.result == 'success') {
                    grow(data.message, 'success');
                } else {
                    grow(data.error, 'danger');
                }
            });
        }
    }

    if ($.exists(".sortable")) {
        $('.sortable tr').css('cursor', 'move');
        $(".sortable").sortable({
            axis: "y",
            cursor: "move",
            scroll: false,
            cancel: ".head_body, .btn, .frame_label, td p, td span, td a, td input, input, td select, td textarea",
            sort: function () {
                $(".tooltip").remove();
            }
        });
    }
    $('body').on("sortstop", ".save_positions", function (event, ui) {
        var elProdID = $("#Product_Id");
        if(elProdID.val() != '') {
            var productId = elProdID.val();
            var url = elProdID.closest('form').data('sort-url');
            save_positions(url, productId);
        }
    });
});
