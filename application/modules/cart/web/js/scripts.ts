import * as $ from 'jquery';

module OakCMS {

    export class Cart {
        elementsListWidgetParams: any = [];
        //jsonResult: string | null = null;
        csrf: string | null = null;
        csrf_param: string | null = null;


        public init() {

            $(document).on('change', '.oakcms-cart-element-count', (event) => this.changeElementCount(event));
            $(document).on('click', '.oakcms-cart-buy-button', (event) => this.addElement(event));
            $(document).on('click', '.oakcms-cart-truncate-button', (event) => this.truncate(event));
            $(document).on('click', '.oakcms-cart-delete-button', (event) => this.deleteElement(event));
            $(document).on('click', '.oakcms-arr', (event) => Cart.changeInputValue(event));
            $(document).on('change', '.oakcms-cart-element-before-count', (event) => Cart.changeBeforeElementCount(event));
            $(document).on('change', '.oakcms-option-values-before', (event) => Cart.changeBeforeElementOptions(event));
            $(document).on('change', '.oakcms-option-values', (event) => this.changeElementOptions(event));

            return true;
        }

        public addElement(e) {
            let data: any = {};
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
        }

        public deleteElement(e) {
            $(document).trigger("deleteCartElement", e.target);

            let link: object = e.target,
                elementId: number = $(link).data('id'),
                lineSelector: string;

            this.sendData({elementId: elementId}, $(link).attr('data-url'));

            if (lineSelector = $(link).data('line-selector')) {
                $(link).closest(lineSelector).hide('slow', function () {
                    $(this).remove();

                    if ($(lineSelector).length <= 0) {
                        location.reload();
                    }
                });
            }

            return false;
        }

        public static changeInputValue(e) {
            let val: number = parseInt($(e.target).closest('.oakcms-change-count').find('input').val()),
                input = $(e.target).closest('.oakcms-change-count').find('input');

            if ($(e.target).hasClass('oakcms-downArr')) {
                if (val <= 0) {
                    return false;
                }
                $(input).val(val - 1);
            } else {
                $(input).val(val + 1);
            }

            $(input).change();

            return false;
        }

        public static changeBeforeElementCount(e) {
            if ($(e.target).val() <= 0) {
                $(e.target).val('0');
            }

            let id: number = $(e.target).data('id'),
                buyButton: object = $('.oakcms-cart-buy-button' + id);

            $(buyButton).data('count', $(e.target).val());
            $(buyButton).attr('data-count', $(e.target).val());

            return true;
        }

        public changeElementCount(e) {
            $(document).trigger("changeCartElementOptions", this);

            let id: number = $(e.target).data('id'),
                options: object = {},
                els: object;

            if ($(e.target).is('select')) {
                els = $('.oakcms-cart-option' + id);
            } else {
                els = $('.oakcms-cart-option' + id + ':checked');
            }

            $(els).each(function () {
                //let name:number = $(this).data('id');
                options[id] = $(e.target).val();
            });

            let data: any = {};

            data.CartElement = {};
            data.CartElement.id = jQuery(this).data('id');
            data.CartElement.name = jQuery(this).data('id');
            data.CartElement.count = jQuery(this).val();

            this.sendData(data, jQuery(this).data('href'));
            return false;
        }

        public static changeBeforeElementOptions(e) {
            let $target: any = $(e.target),
                filter_id: number = $target.data('filter-id'),
                id: number = $target.data('id'),
                buyButton: object = $('.oakcms-cart-buy-button' + id),
                options: object = $(buyButton).data('options');

            if (!options) {
                options = {};
            }

            options[filter_id] = $target.val();
            options['element'] = $target;

            $(buyButton).data('options', options);
            $(buyButton).attr('data-options', JSON.stringify(options));

            $(document).trigger("beforeChangeCartElementOptions", options);

            return true;
        }

        public changeElementOptions(e) {
            $(document).trigger("changeCartElementOptions", this);

            let id: number = $(e.target).data('id'),
                options: any = {},
                data: any = {},
                els: any;

            if ($(e.target).is('select')) {
                els = $('.oakcms-cart-option' + id);
            } else {
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
        }

        public sendData(data: any, link: string = '/cart/element/create') {
            $(document).trigger("sendDataToCart", data);

            data.elementsListWidgetParams = this.elementsListWidgetParams;
            data[this.csrf_param] = this.csrf;

            $.post(link, data, function (json) {
                if (json.result == 'error') {
                    console.log(json.error);
                } else {
                    Cart.renderCart(json);
                }
            }, "json");

            return false;
        }

        public static renderCart(json: any) {
            if (json === void 0) {
                $.post('/cart/default/info', {}, function (answer) {
                    json = answer
                }, "json");
            }

            $('.oakcms-cart-block').replaceWith(json.elementsHTML);
            $('.oakcms-cart-count').html(json.count);
            $('.oakcms-cart-price').html(json.price);

            $(document).trigger("renderCart", json);
        }

        public truncate(e) {
            this.sendData({}, $(e.target).attr('href'));
            return false;
        }
    }
}

new OakCMS.Cart().init();
