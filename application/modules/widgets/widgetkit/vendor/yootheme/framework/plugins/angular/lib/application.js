(function(angular, global) {

    angular.module('Application', [])

        .factory('Application', ['name', function(name) {

            return angular.extend({

                url: function(pattern, params) {

                    var query = [], url = this.config.route;

                    angular.forEach(angular.extend({ p: pattern }, params), function(val, key) {
                        query.push(key + '=' + val);
                    });

                    if (query.length) {
                        url += (url.indexOf('?') != -1 ? '&' : '?') + query.join('&');
                    }

                    return url;
                },

                baseUrl: function() {
                    return this.config.url;
                },

                templateUrl: function(name) {
                    return this.url('template', {'name': name});
                }

            }, global[name]);

        }])

        .filter('first', ['$filter', function($filter) {
            return function(collection) {
                return $filter('toArray')(collection)[0];
            };
        }])

        .filter('length', ['$filter', function($filter) {
            return function(collection) {
                return $filter('toArray')(collection).length;
            };
        }])

        .filter('toArray', function() {
            return function(collection) {

                if (angular.isObject(collection)) {
                    return Object.keys(collection)

                        .filter(function(key) {
                            return key.charAt(0) !== '$';
                        })

                        .map(function(key) {
                            return collection[key];
                        });
                }

                return angular.isArray(collection) ? collection : [];
            };
        })

        .config(['$provide', '$httpProvider', function($provide, $httpProvider) {

            $provide.decorator('$templateCache', ['$delegate', 'Application', function($delegate, App) {

                angular.forEach(App.templates, function(tpl, name) {
                    $delegate.put(name, tpl);
                });

                return $delegate;
            }]);

            $provide.decorator('$templateRequest', ['$delegate', 'Application', function($delegate, App) {

                return function(tpl, ignoreRequestError) {

                    if (!App.templates[tpl]) {
                        tpl = App.templateUrl(tpl);
                    }

                    return $delegate(tpl, ignoreRequestError);
                };
            }]);

            $httpProvider.interceptors.push(['Application', function(App) {

                return {

                    request: function(config) {

                        if (config.method == 'PUT' || config.method == 'DELETE') {
                            config.headers['X-HTTP-Method-Override'] = config.method;
                            config.method = 'POST';
                        }

                        if (config.method == 'POST') {
                            config.headers['X-XSRF-TOKEN'] = App.config.csrf;
                        }

                        return config;
                    }
                };
            }]);

        }]);

    angular.element(global.document).ready(function() {

        var apps = angular.element(this).find('[data-app]');

        angular.forEach(apps, function(app) {

            var name = angular.element(app).data('app');

            if (global[name]) {
                angular.bootstrap(app, [name]);
            }
        });

    });

})(angular, window);
