if (typeof pistol88 == "undefined" || !pistol88) {
    var pistol88 = {};
}

pistol88.cart = {
    init: function() {
        pistol88.cart.csrf = jQuery('meta[name=csrf-token]').attr("content");
        pistol88.cart.csrf_param = jQuery('meta[name=csrf-param]').attr("content");
        
        $(document).on('change', '.pistol88-cart-element-count', this.changeElementCount);
        $(document).on('click', '.pistol88-cart-buy-button', this.addElement);
        $(document).on('click', '.pistol88-cart-truncate-button', this.truncate);
        $(document).on('click', '.pistol88-cart-delete-button', this.deleteElement);
        $(document).on('click', '.pistol88-arr', this.changeInputValue);
        $(document).on('change', '.pistol88-cart-element-before-count', this.changeBeforeElementCount);
        $(document).on('change', '.pistol88-option-values-before', this.changeBeforeElementOptions);
        $(document).on('change', '.pistol88-option-values', this.changeElementOptions);
        
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
            var els = $('.pistol88-cart-option'+id);
        }
        else {
            var els = $('.pistol88-cart-option'+id+':checked');
            console.log('radio');
        }
        
        $(els).each(function() {
            var name = $(this).data('id');

            options[id] = $(this).val();
        });
        
        var data = {};
        data.CartElement = {};
        data.CartElement.id = id;
        data.CartElement.options = JSON.stringify(options);

        pistol88.cart.sendData(data, jQuery(this).data('href'));

        return false;
    },
    changeBeforeElementOptions: function() {
        var id = $(this).data('id');
        var filter_id = $(this).data('filter-id');
        var buyButton = $('.pistol88-cart-buy-button'+id);

        var options = $(buyButton).data('options');
        if(!options) {
            options = {};
        }

        options[filter_id] = $(this).val();

        $(buyButton).data('options', options);
        $(buyButton).attr('data-options', options);

        $(document).trigger("beforeChangeCartElementOptions", options);

        return true;
    },
    deleteElement: function() {
        $(document).trigger("deleteCartElement", this);

        var link = this;
        var elementId = jQuery(this).data('id');

        pistol88.cart.sendData({elementId: elementId}, jQuery(this).attr('href'));

        if(lineSelector = jQuery(this).data('line-selector')) {
            jQuery(link).parents(lineSelector).last().hide('slow');
        }

        return false;
    },
    changeInputValue: function() {
        var val = parseInt(jQuery(this).siblings('input').val());
        var input = jQuery(this).siblings('input');
        
        if(jQuery(this).hasClass('pistol88-downArr')) {
            if(val <= 0) {
                return false;
            }
            jQuery(input).val(val-1);
        }
        else {
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
        var buyButton = $('.pistol88-cart-buy-button'+id);
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

        pistol88.cart.sendData(data, jQuery(this).data('href'));

        return false;
    },
    addElement: function() {
        jQuery(document).trigger("addCartElement", this);

        var data = {};
        data.CartElement = {};
        data.CartElement.model = jQuery(this).data('model');
        data.CartElement.item_id = jQuery(this).data('id');
        data.CartElement.count = jQuery(this).data('count');
        data.CartElement.price = jQuery(this).data('price');
        data.CartElement.options = jQuery(this).data('options');

        pistol88.cart.sendData(data, jQuery(this).attr('href'));
        
        return false;
    },
    truncate: function() {
        pistol88.cart.sendData({}, jQuery(this).attr('href'));
        return false;
    },
    sendData: function(data, link) {
        if(!link) {
            link = '/cart/element/create';
        }

        jQuery(document).trigger("sendDataToCart", data);

        data.elementsListWidgetParams = pistol88.cart.elementsListWidgetParams;
        data[pistol88.cart.csrf_param] = pistol88.cart.csrf;

        jQuery.post(link, data,
            function(json) {
                if(json.result == 'fail') {
                    console.log(json.error);
                }
                else {
                    pistol88.cart.renderCart(json);
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
        
        jQuery('.pistol88-cart-block').replaceWith(json.elementsHTML);
        jQuery('.pistol88-cart-count').html(json.count);
        jQuery('.pistol88-cart-price').html(json.price);

        jQuery(document).trigger("renderCart", json);
        
        return true;
    },
};

pistol88.cart.init();
