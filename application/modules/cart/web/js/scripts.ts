declare var jQuery: JQueryStatic;
declare var $: JQueryStatic;

module OakCMS {

    export class Cart {
        elementsListWidgetParams: any = [];
        //jsonResult: string | null = null;
        csrf: string | null = null;
        csrf_param: string | null = null;

        constructor(csrf:string = $('meta[name=csrf-token]').prop('content'), csrf_param:string = $('meta[name=csrf-param]').prop('content')) {
            $(document).on('change', '.oakcms-cart-element-count', (event) => this.changeElementCount(event));
            $(document).on('click', '.oakcms-cart-buy-button', (event) => this.addElement(event));
            $(document).on('click', '.oakcms-cart-truncate-button', (event) => this.truncate(event));
            $(document).on('click', '.oakcms-cart-delete-button', (event) => this.deleteElement(event));
            $(document).on('click', '.oakcms-arr', (event) => this.changeInputValue(event));
            $(document).on('change', '.oakcms-cart-element-before-count', (event) => this.changeBeforeElementCount(event));
            $(document).on('change', '.oakcms-option-values-before', (event) => this.changeBeforeElementOptions(event));
            $(document).on('change', '.oakcms-option-values', (event) => this.changeElementOptions(event));
        }

        public addElement(e) {
            let data: object = {
                CartElement: {
                    model:   $(e.target).data('model'),
                    item_id: $(e.target).data('id'),
                    count:   $(e.target).data('count'),
                    price:   $(e.target).data('price'),
                    options: $(e.target).data('options'),
                    name:    $(e.target).data('name')
                }
            };

            $(document).trigger("addCartElement", data);

            $(e.target).addClass('loading');

            if($(e.target).prop("tagName") === 'BUTTON') {
                $(e.target).prop('disabled', true);
            }

            this.sendData(data, $(e.target).attr('href'), function () {
                setTimeout(function () {
                    $(e.target).removeClass('loading');
                    if($(e.target).prop("tagName") === 'BUTTON') {
                        $(e.target).prop('disabled', false);
                    }
                }, 1000);
            });

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

        public changeInputValue(e) {
            let val: number = parseInt($(e.target).closest('.oakcms-change-count').find('input').val()),
                input = $(e.target).closest('.oakcms-change-count').find('input');

            if ($(e.target).hasClass('oakcms-downArr')) {
                if (val <= 0) {
                    return false;
                }
                $(input).val(val - 1);
            } else if($(e.target).hasClass('oakcms-upArr')) {
                $(input).val(val + 1);
            }

            $(input).change();

            return false;
        }

        public changeBeforeElementCount(e) {
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

            let data: object = {
                CartElement: {
                    id: $(e.target).data('id'),
                    count: $(e.target).val(),
                }
            };

            $(document).trigger("changeCartElementOptions", data);

            this.sendData(data, $(e.target).data('href'));
            return false;
        }

        public changeBeforeElementOptions(e) {
            let $target: any = $(e.target),
                filter_id: number = $target.data('filter-id'),
                id: number = $target.data('id'),
                buyButton: object = $('.oakcms-cart-buy-button' + id),
                options: object = $(buyButton).data('options');

            if (!options) {
                options = {};
            }

            options[filter_id] = $target.val();

            $(buyButton).data('options', options);
            $(buyButton).attr('data-options', JSON.stringify(options));

            options['element'] = $target;

            $(document).trigger("beforeChangeCartElementOptions", options);

            return true;
        }

        public changeElementOptions(e) {
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

            //console.log(data);

            $(document).trigger("changeCartElementOptions", data);

            this.sendData(data, $(e.target).data('href'));

            return false;
        }

        public sendData(data: any, link: string = '/cart/element/create', callback:(...args: any[]) => any = null) {
            $(document).trigger("sendDataToCart", data);

            data.elementsListWidgetParams = this.elementsListWidgetParams;
            data[this.csrf_param] = this.csrf;

            $.post(link, data, function (json) {
                if (json.result == 'error') {
                    console.log(json.error);
                } else {
                    Cart.renderCart();
                    if(typeof callback === "function") {
                        callback.call(json);
                    }
                }
            }, "json");

            return false;
        }

        public static renderCart() {
            $.post('/cart/default/info', {}, function (json) {
                $('.oakcms-cart-count').html(json.count);
                $('.oakcms-cart-price').html(json.price);
                $(document).trigger("renderCart", json);
            }, "json");
        }

        public truncate(e) {
            this.sendData({}, $(e.target).attr('href'));
            return false;
        }
    }
}

new OakCMS.Cart();
