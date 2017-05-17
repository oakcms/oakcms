var OakCMS;
(function (OakCMS) {
    var Cart = (function () {
        function Cart() {
            this.elementsListWidgetParams = [];
            //jsonResult: string | null = null;
            this.csrf = null;
            this.csrf_param = null;
        }
        Cart.prototype.init = function () {
            var _this = this;
            $(document).on('change', '.oakcms-cart-element-count', function (event) { return _this.changeElementCount(event); });
            $(document).on('click', '.oakcms-cart-buy-button', function (event) { return _this.addElement(event); });
            $(document).on('click', '.oakcms-cart-truncate-button', function (event) { return _this.truncate(event); });
            $(document).on('click', '.oakcms-cart-delete-button', function (event) { return _this.deleteElement(event); });
            $(document).on('click', '.oakcms-arr', function (event) { return Cart.changeInputValue(event); });
            $(document).on('change', '.oakcms-cart-element-before-count', function (event) { return Cart.changeBeforeElementCount(event); });
            $(document).on('change', '.oakcms-option-values-before', function (event) { return Cart.changeBeforeElementOptions(event); });
            $(document).on('change', '.oakcms-option-values', function (event) { return _this.changeElementOptions(event); });
            return true;
        };
        Cart.prototype.addElement = function (e) {
            var data = {};
            data.CartElement = {};
            data.CartElement.model = $(e.target).data('model');
            data.CartElement.item_id = $(e.target).data('id');
            data.CartElement.count = $(e.target).data('count');
            data.CartElement.price = $(e.target).data('price');
            data.CartElement.options = $(e.target).data('options');
            data.CartElement.name = $(e.target).data('name');
            $(document).trigger("addCartElement", data);
            this.sendData(data, $(e.target).attr('href'));
            return false;
        };
        Cart.prototype.deleteElement = function (e) {
            $(document).trigger("deleteCartElement", e.target);
            var link = e.target, elementId = $(link).data('id'), lineSelector;
            this.sendData({ elementId: elementId }, $(link).attr('data-url'));
            if (lineSelector = $(link).data('line-selector')) {
                $(link).closest(lineSelector).hide('slow', function () {
                    $(this).remove();
                    if ($(lineSelector).length <= 0) {
                        location.reload();
                    }
                });
            }
            return false;
        };
        Cart.changeInputValue = function (e) {
            var val = parseInt($(e.target).closest('.oakcms-change-count').find('input').val()), input = $(e.target).closest('.oakcms-change-count').find('input');
            if ($(e.target).hasClass('oakcms-downArr')) {
                if (val <= 0) {
                    return false;
                }
                $(input).val(val - 1);
            }
            else {
                $(input).val(val + 1);
            }
            $(input).change();
            return false;
        };
        Cart.changeBeforeElementCount = function (e) {
            if ($(e.target).val() <= 0) {
                $(e.target).val('0');
            }
            var id = $(e.target).data('id'), buyButton = $('.oakcms-cart-buy-button' + id);
            $(buyButton).data('count', $(e.target).val());
            $(buyButton).attr('data-count', $(e.target).val());
            return true;
        };
        Cart.prototype.changeElementCount = function (e) {
            $(document).trigger("changeCartElementOptions", this);
            var id = $(e.target).data('id'), options = {}, els;
            if ($(e.target).is('select')) {
                els = $('.oakcms-cart-option' + id);
            }
            else {
                els = $('.oakcms-cart-option' + id + ':checked');
            }
            $(els).each(function () {
                //let name:number = $(this).data('id');
                options[id] = $(e.target).val();
            });
            var data = {};
            data.CartElement = {};
            data.CartElement.id = jQuery(this).data('id');
            data.CartElement.name = jQuery(this).data('id');
            data.CartElement.count = jQuery(this).val();
            this.sendData(data, jQuery(this).data('href'));
            return false;
        };
        Cart.changeBeforeElementOptions = function (e) {
            var $target = $(e.target), filter_id = $target.data('filter-id'), id = $target.data('id'), buyButton = $('.oakcms-cart-buy-button' + id), options = $(buyButton).data('options');
            if (!options) {
                options = {};
            }
            options[filter_id] = $target.val();
            options['element'] = $target;
            $(buyButton).data('options', options);
            $(buyButton).attr('data-options', JSON.stringify(options));
            $(document).trigger("beforeChangeCartElementOptions", options);
            return true;
        };
        Cart.prototype.changeElementOptions = function (e) {
            $(document).trigger("changeCartElementOptions", this);
            var id = $(e.target).data('id'), options = {}, data = {}, els;
            if ($(e.target).is('select')) {
                els = $('.oakcms-cart-option' + id);
            }
            else {
                els = $('.oakcms-cart-option' + id + ':checked');
            }
            $(els).each(function () {
                //var name = $(e.target).data('id');
                options[id] = $(e.target).val();
            });
            data.CartElement = {};
            data.CartElement.id = id;
            data.CartElement.options = JSON.stringify(options);
            this.sendData(data, $(e.target).data('href'));
            return false;
        };
        Cart.prototype.sendData = function (data, link) {
            if (link === void 0) { link = '/cart/element/create'; }
            $(document).trigger("sendDataToCart", data);
            data.elementsListWidgetParams = this.elementsListWidgetParams;
            data[this.csrf_param] = this.csrf;
            $.post(link, data, function (json) {
                if (json.result == 'error') {
                    console.log(json.error);
                }
                else {
                    Cart.renderCart(json);
                }
            }, "json");
            return false;
        };
        Cart.renderCart = function (json) {
            if (json === void 0) {
                $.post('/cart/default/info', {}, function (answer) {
                    json = answer;
                }, "json");
            }
            $('.oakcms-cart-block').replaceWith(json.elementsHTML);
            $('.oakcms-cart-count').html(json.count);
            $('.oakcms-cart-price').html(json.price);
            $(document).trigger("renderCart", json);
        };
        Cart.prototype.truncate = function (e) {
            this.sendData({}, $(e.target).attr('href'));
            return false;
        };
        return Cart;
    }());
    OakCMS.Cart = Cart;
})(OakCMS || (OakCMS = {}));
new OakCMS.Cart().init();
