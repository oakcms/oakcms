var OakCMS;
(function (OakCMS) {
    var Cart = (function () {
        function Cart(csrf, csrf_param) {
            if (csrf === void 0) { csrf = $('meta[name=csrf-token]').prop('content'); }
            if (csrf_param === void 0) { csrf_param = $('meta[name=csrf-param]').prop('content'); }
            var _this = this;
            this.elementsListWidgetParams = [];
            //jsonResult: string | null = null;
            this.csrf = null;
            this.csrf_param = null;
            $(document).on('change', '.oakcms-cart-element-count', function (event) { return _this.changeElementCount(event); });
            $(document).on('click', '.oakcms-cart-buy-button', function (event) { return _this.addElement(event); });
            $(document).on('click', '.oakcms-cart-truncate-button', function (event) { return _this.truncate(event); });
            $(document).on('click', '.oakcms-cart-delete-button', function (event) { return _this.deleteElement(event); });
            $(document).on('click', '.oakcms-arr', function (event) { return _this.changeInputValue(event); });
            $(document).on('change', '.oakcms-cart-element-before-count', function (event) { return _this.changeBeforeElementCount(event); });
            $(document).on('change', '.oakcms-option-values-before', function (event) { return _this.changeBeforeElementOptions(event); });
            $(document).on('change', '.oakcms-option-values', function (event) { return _this.changeElementOptions(event); });
        }
        Cart.prototype.addElement = function (e) {
            var data = {
                CartElement: {
                    model: $(e.target).data('model'),
                    item_id: $(e.target).data('id'),
                    count: $(e.target).data('count'),
                    price: $(e.target).data('price'),
                    options: $(e.target).data('options'),
                    name: $(e.target).data('name')
                }
            };
            $(document).trigger("addCartElement", data);
            $(e.target).button('loading');
            this.sendData(data, $(e.target).attr('href'), function () {
                $(e.target).button('reset');
            });
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
        Cart.prototype.changeInputValue = function (e) {
            var val = parseInt($(e.target).closest('.oakcms-change-count').find('input').val()), input = $(e.target).closest('.oakcms-change-count').find('input');
            if ($(e.target).hasClass('oakcms-downArr')) {
                if (val <= 0) {
                    return false;
                }
                $(input).val(val - 1);
            }
            else if ($(e.target).hasClass('oakcms-upArr')) {
                $(input).val(val + 1);
            }
            $(input).change();
            return false;
        };
        Cart.prototype.changeBeforeElementCount = function (e) {
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
            var data = {
                CartElement: {
                    id: $(e.target).data('id'),
                    count: $(e.target).val()
                }
            };
            this.sendData(data, $(e.target).data('href'));
            return false;
        };
        Cart.prototype.changeBeforeElementOptions = function (e) {
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
        Cart.prototype.sendData = function (data, link, callback) {
            if (link === void 0) { link = '/cart/element/create'; }
            if (callback === void 0) { callback = null; }
            $(document).trigger("sendDataToCart", data);
            data.elementsListWidgetParams = this.elementsListWidgetParams;
            data[this.csrf_param] = this.csrf;
            $.post(link, data, function (json) {
                if (json.result == 'error') {
                    console.log(json.error);
                }
                else {
                    Cart.renderCart();
                    if (typeof callback === "function") {
                        callback.call(json);
                    }
                }
            }, "json");
            return false;
        };
        Cart.renderCart = function () {
            $.post('/cart/default/info', {}, function (json) {
                $('.oakcms-cart-count').html(json.count);
                $('.oakcms-cart-price').html(json.price);
                $(document).trigger("renderCart", json);
            }, "json");
        };
        Cart.prototype.truncate = function (e) {
            this.sendData({}, $(e.target).attr('href'));
            return false;
        };
        return Cart;
    }());
    OakCMS.Cart = Cart;
})(OakCMS || (OakCMS = {}));
new OakCMS.Cart();
