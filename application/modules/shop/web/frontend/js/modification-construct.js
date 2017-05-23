if (typeof oakcms == "undefined" || !oakcms) {
    var oakcms = {};
}
Array.prototype.diff = function (a) {
    return this.filter(function (i) {
        return a.indexOf(i) < 0;
    });
};

String.prototype.strtr = function (replacePairs) {
    "use strict";

    var str = this.toString(), key, re;
    for (key in replacePairs) {
        if (replacePairs.hasOwnProperty(key)) {
            re = new RegExp(key, "g");
            str = str.replace(re, replacePairs[key]);
        }
    }
    return str;
};

oakcms.modificationconstruct = {
    optionsChange: null,
    modifications: null,
    init: function () {
        $(document).on('change', '.product-add-modification-form .filters select', this.generateName);

        $(document).on("beforeChangeCartElementOptions", function (e, options) {
            oakcms.modificationconstruct.setModification(options);
        });
    },
    setModification: function (options) {
        var optionsChange           = oakcms.modificationconstruct.optionsChange,
            $id                     = options.element.closest('.oakcms-change-options').attr('id'),
            $return                 = '';


        if (typeof $id !== 'undefined') {
            delete options.element;

            var m                       = this.getData(options),
                parts                   = {},
                currency                = optionsChange[$id].currency,
                $template               = optionsChange[$id].template,
                $pricesParts            = optionsChange[$id].pricesParts,
                $pricesTypes            = optionsChange[$id].pricesTypes,
                notAvailable            = optionsChange[$id].notAvailable,
                id                      = optionsChange[$id].id;

            if(m && m.available == 'yes' && parseInt(m.price) > 0) {

                parts['{currency}'] = currency;
                parts['{main_price}'] = m.price;

                $.each($pricesTypes, function (key, value) {
                    var price = m.prices[value];

                    if(typeof price !== 'undefined' && parseInt(price) > 0) {
                        parts['{price id="'+value+'"}'] = price;
                    } else {
                        parts['{price id="'+value+'"}'] = '';
                    }

                });

                $return = $template.strtr($pricesParts);
                $return = $return.strtr(parts);
            } else {
                $return = notAvailable;
            }

            $('#' + id).html($return);

        }

        $(document).trigger("shopSetModification", m);
    },
    getData: function (options) {

        if (oakcms.modificationconstruct.modifications) {
            var cartOptions = options,
                ret = false;

            $.each(oakcms.modificationconstruct.modifications, function (i, m) {

                var options = [];
                $.each(cartOptions, function (i, co) {
                    options.push(parseInt(co));
                });
                var filter_value = $.makeArray(m.filter_value);

                if (options.length == filter_value.length) {
                    var result = options.diff(filter_value);
                    if (result.length == 0) {
                        ret = m;
                    }
                }
            });
            return ret;
        } else {
            return false;
        }
    },
    generateName: function () {
        var name = '';
        $('.product-add-modification-form .filters select').each(function (i, el) {
            var val = $(this).find('option:selected').text();
            if (val) {
                name = name + ' ' + val;
            }
        });

        if (name != '') {
            $('#modification-name').val(name);
        }
    }
};

oakcms.modificationconstruct.init();
