(function (window) {

    function install() {

        var Vue = window.Vue, Translator = window.Translator, _ = Vue.util, config = window.$config;

        Vue.url.options.root = config.url;
        Vue.http.headers.custom['X-XSRF-TOKEN'] = config.csrf;
        Vue.http.options.emulateHTTP = true;
        Vue.http.options.beforeSend = function (request, options) {
            if (typeof options.url === 'string' && !options.url.match(/^(https?:)?\//)) {
                options.params.p = options.url;
                options.url = config.route;
            }
        };

        Vue.url.base = function (url, params) {
            var options = url;

            if (!_.isPlainObject(options)) {
                options = {url: url, params: params};
            }

            if (!options.root) {
                options.root = config['base'];
            }

            return Vue.url(options);
        };

        Vue.url.route = function (url, params) {
            var options = url;

            if (!_.isPlainObject(params)) {
                params = {};
            }

            if (!_.isPlainObject(options)) {
                options = {url: url, params: params};
            }

            options = {
                url: config.route,
                params: _.extend(options.params, {
                    p: options.url
                })
            };

            return Vue.url(options);
        };

        Translator.locale = config.locale;

        for (var locale in config.locales) {
            for (var id in config.locales[locale]) {
                Translator.add(id, config.locales[locale][id]);
            }
        }
    }

    if (window.Vue) {
        jQuery(install);
    }

})(this);