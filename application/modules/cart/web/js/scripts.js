if (typeof oakcms == "undefined" || !oakcms) {
    var oakcms = {};
}

oakcms.cart = {
    init: function() {
        oakcms.cart.csrf = jQuery('meta[name=csrf-token]').attr("content");
        oakcms.cart.csrf_param = jQuery('meta[name=csrf-param]').attr("content");

        $(document).on('change', '.oakcms-cart-element-count', this.changeElementCount);
        $(document).on('click', '.oakcms-cart-buy-button', this.addElement);
        $(document).on('click', '.oakcms-cart-truncate-button', this.truncate);
        $(document).on('click', '.oakcms-cart-delete-button', this.deleteElement);
        $(document).on('click', '.oakcms-arr', this.changeInputValue);
        $(document).on('change', '.oakcms-cart-element-before-count', this.changeBeforeElementCount);
        $(document).on('change', '.oakcms-option-values-before', this.changeBeforeElementOptions);
        $(document).on('change', '.oakcms-option-values', this.changeElementOptions);

        return true;
    },
    elementsListWidgetParams: [],
    jsonResult: null,
    csrf: null,
    csrf_param: null,
    changeElementOptions: function() {
        jQuery(document).trigger("changeCartElementOptions", this);

        var id = $(this).data('id');

        var options = {};

        if($(this).is('select')) {
            var els = $('.oakcms-cart-option'+id);
        }
        else {
            var els = $('.oakcms-cart-option'+id+':checked');
        }

        $(els).each(function() {
            var name = $(this).data('id');

            options[id] = $(this).val();
        });

        var data = {};
        data.CartElement = {};
        data.CartElement.id = id;
        data.CartElement.options = JSON.stringify(options);

        oakcms.cart.sendData(data, jQuery(this).data('href'));

        return false;
    },
    changeBeforeElementOptions: function() {
        var id = $(this).data('id');
        var filter_id = $(this).data('filter-id');
        var buyButton = $('.oakcms-cart-buy-button'+id);

        var options = $(buyButton).data('options');
        if(!options) {
            options = {};
        }

        options[filter_id] = $(this).val();
        options['element'] = $(this);

        $(buyButton).data('options', options);
        $(buyButton).attr('data-options', JSON.stringify(options));

        $(document).trigger("beforeChangeCartElementOptions", options);

        return true;
    },
    deleteElement: function() {
        $(document).trigger("deleteCartElement", this);

        var link = this;
        var elementId = jQuery(this).data('id');

        oakcms.cart.sendData({elementId: elementId}, jQuery(this).attr('href'));

        if(lineSelector = jQuery(this).data('line-selector')) {
            jQuery(link).parents(lineSelector).last().hide('slow');
        }

        return false;
    },
    changeInputValue: function() {
        var val = parseInt(jQuery(this).closest('.oakcms-change-count').find('input').val()),
            input = jQuery(this).closest('.oakcms-change-count').find('input');

        if(jQuery(this).hasClass('oakcms-downArr')) {
            if(val <= 0) {
                return false;
            }
            jQuery(input).val(val-1);
        } else {
            jQuery(input).val(val+1);
        }

        jQuery(input).change();

        return false;
    },
    changeBeforeElementCount: function() {
        if($(this).val() <= 0) {
            $(this).val('0');
        }

        var id = $(this).data('id');
        var buyButton = $('.oakcms-cart-buy-button'+id);
        $(buyButton).data('count', $(this).val());
        $(buyButton).attr('data-count', $(this).val());

        return true;
    },
    changeElementCount: function() {
        jQuery(document).trigger("changeCartElementCount", this);

        if($(this).val() <= 0) {
            $(this).val('0');
        }

        var input = jQuery(this);

        var data = {};
        data.CartElement = {};
        data.CartElement.id = jQuery(this).data('id');
        data.CartElement.count = jQuery(this).val();

        oakcms.cart.sendData(data, jQuery(this).data('href'));

        return false;
    },
    addElement: function() {
        var data = {};
        data.CartElement = {};
        data.CartElement.model = jQuery(this).data('model');
        data.CartElement.item_id = jQuery(this).data('id');
        data.CartElement.count = jQuery(this).data('count');
        data.CartElement.price = jQuery(this).data('price');
        data.CartElement.options = jQuery(this).data('options');
        data.CartElement.name = jQuery(this).data('name');


        jQuery(document).trigger("addCartElement", data);

        oakcms.cart.sendData(data, jQuery(this).attr('href'));

        return false;
    },
    truncate: function() {
        oakcms.cart.sendData({}, jQuery(this).attr('href'));
        return false;
    },
    sendData: function(data, link) {
        if(!link) {
            link = '/cart/element/create';
        }

        jQuery(document).trigger("sendDataToCart", data);

        data.elementsListWidgetParams = oakcms.cart.elementsListWidgetParams;
        data[oakcms.cart.csrf_param] = oakcms.cart.csrf;

        jQuery.post(link, data,
            function(json) {
                if(json.result == 'fail') {
                    console.log(json.error);
                }
                else {
                    oakcms.cart.renderCart(json);
                }

            }, "json");

        return false;
    },
    renderCart: function(json) {
        if(!json) {
            var json = {};
            jQuery.post('/cart/default/info', {},
                function(answer) {
                    json = answer;
                }, "json");
        }

        jQuery('.oakcms-cart-block').replaceWith(json.elementsHTML);
        jQuery('.oakcms-cart-count').html(json.count);
        jQuery('.oakcms-cart-price').html(json.price);

        jQuery(document).trigger("renderCart", json);

        return true;
    },
};

oakcms.cart.init();
