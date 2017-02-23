/*-------------------------------

 IFRAME.JS

 Iframe manager plugin for Yii2

 @author gromver5@gmail.com
 @version 1.1.0

 -------------------------------*/
yii.gromverIframe = (function ($) {
    var iframeCounter = 0,
        relations = [];

    var EVENT_KEY = '.grom.iframe';

    var Default = {
        width: '100%',
        height: 'content',
        frameborder: '0'
    };

    var Event = {
        OPEN: 'open' + EVENT_KEY,                           // root window
        SEND: 'send' + EVENT_KEY,                           // root window
        RECEIVE: 'receive' + EVENT_KEY,                     // child window
        REFRESH: 'refresh' + EVENT_KEY,                     // child window
        REFRESH_PARENT: 'refreshParent' + EVENT_KEY,       // root window
        REDIRECT: 'redirect' + EVENT_KEY,                   // child window
        REDIRECT_PARENT: 'redirectParent' + EVENT_KEY,     // root window
        CLOSE: 'close' + EVENT_KEY,                         // root window
        CLOSE_POPUP: 'closePopup' + EVENT_KEY              // child window
    };

    var pub = {
        namePrefix: 'grom-iframe-',
        isActive: true,
        dataHandler: null,      // function (data) { ... }
        actionHandler: null,    // function (formAction) { return newFormAction }
        paramsHandler: null,    // function (formParams) { return newFormParams }
        init: function() {
            initDataMethods();
            initEvents();
        },
        // отослать данные в родительское окно
        postData: function(data) {
            postMessage(Event.SEND, data);
        },
        // обновить родительское окно
        refreshParent: function() {
            postMessage(Event.REFRESH_PARENT);
        },
        // закрывает активное модальное окно
        closePopup: function() {
            postMessage(Event.CLOSE);
        },
        handleAction: function ($e) {
            /*
             - action
             - iframeOptions
             - formOptions
             */
            var formOptions = $e.data('form'),
                popupOptions = $e.data('popup'),
                iframeOptions = $e.data('iframe'),
                action = $e.attr('href') || $e.data('href'),
                dataHandler = $e.data('dataHandler'),
                actionHandler = $e.data('actionHandler'),
                paramsHandler = $e.data('paramsHandler');

            if (dataHandler) {
                eval("this.dataHandler = " + dataHandler);
            } else {
                this.dataHandler = null;
            }

            if (actionHandler) {
                eval("this.actionHandler = " + actionHandler);
            } else {
                this.actionHandler = null;
            }

            if (paramsHandler) {
                eval("this.paramsHandler = " + paramsHandler);
            } else {
                this.paramsHandler = null;
            }

            if (iframeOptions && $.isPlainObject(iframeOptions)) {
                if ($.type(iframeOptions.src) === 'string') {
                    action = iframeOptions.src;
                }
            }

            if (formOptions && $.isPlainObject(formOptions)) {
                formOptions.method = formOptions.method || 'get';

                if ($.type(formOptions.action) === 'string') {
                    action = formOptions.action;
                }

                if (!action || !action.match(/(^\/|:\/\/)/)) {
                    action = window.location.href;
                }
            }

            postMessage(Event.OPEN, {
                action: action,
                popupOptions: popupOptions,
                formOptions: formOptions,
                iframeOptions: iframeOptions
            });
        }
    };

    function createIframeName() {
        return pub.namePrefix + (++iframeCounter);
    }

    // отправка сообщения с целевое окно, по умолчанию в родительское
    function postMessage(name, message, target) {
        var data = {
            name: name,
            message: message
        };

        (target || window.parent).postMessage(JSON.stringify(data), window.location.origin || window.location.href);
    }

    function initDataMethods() {
        var handler = function (event) {
            pub.handleAction($(this));
            event.stopImmediatePropagation();
            return false;
        };

        // handle data-confirm and data-method for clickable and changeable elements
        $(document).on('click.yii', '[data-behavior="iframe"]', handler);
    }

    function initEvents() {
        attachPostMessageHandler(function(e) {
            try {
                var data = JSON.parse(e.data);
            } catch(e) {
                return;
            }
            if(data.name) {
                $(pub).triggerHandler(data.name, [data.message, e.source]);
            }
        });

        $(pub).on(Event.OPEN, function(e, data, source) {
            var action = data.action,
                formOptions = data.formOptions,
                popupOptions = data.popupOptions,
                target = createIframeName(),
                $iframe = $('<iframe id="' + target + '" name="' + target + '"></iframe>'),
                iframeOptions = $.extend(true, {}, Default, data.iframeOptions);

            // todo пробрасывать события нажатия в родительское окно (для обработки escape)
            $iframe.attr(iframeOptions);

            if (formOptions) {
                var method = formOptions.method,
                    params = formOptions.params,
                    $form = $('<form target="' + target + '" method="' + method + '"></form>');

                $form.prop('action', action);

                if (!method.match(/(get|post)/i)) {
                    $form.append('<input name="_method" value="' + method + '" type="hidden">');
                    method = 'POST';
                }
                if (!method.match(/(get|head|options)/i)) {
                    var csrfParam = yii.getCsrfParam();
                    if (csrfParam) {
                        $form.append('<input name="' + csrfParam + '" value="' + yii.getCsrfToken() + '" type="hidden">');
                    }
                }
                $form.hide().appendTo(document.body);

                if (this.paramsHandler instanceof Function) {
                    this.paramsHandler(params);
                }

                if (params && $.isPlainObject(params)) {
                    $.each(params, function (idx, obj) {
                        $('<input name="' + idx + '" type="hidden">').val(obj).appendTo($form);
                    });
                }

                popupOptions.content = $iframe;
                var $popup = yii.gromverPopup.open(popupOptions);

                // после интеграции айфрейма в дом, сабмитим в него форму
                $popup.one('loaded.grom.popup', function() {
                    $form.trigger('submit');
                    $form.remove();
                });
            } else {
                if (this.actionHandler instanceof Function) {
                    action = this.actionHandler(action);
                }

                if (window.location.pathname == action) {
                    //баг с отображением тойже страницы что отображена в родительском окне, добавим мусор в урл)
                    action += "?" + Math.floor(Math.random() * 10000);
                }

                $iframe.prop('src', action);

                popupOptions.content = $iframe;
                $popup = yii.gromverPopup.open(popupOptions);
            }

            // onload обработчик вешать после встраивания айфрейма в дом, иначе в safari/webkit событие сработает дважды
            $iframe.load(function() {
                pushRelation(source, this.contentWindow);
                if (iframeOptions.height == 'content') {
                    delete iframeOptions.height;
                    $iframe.iFrameResize({
                        checkOrigin: false,
                        heightCalculationMethod: (navigator.userAgent.indexOf("MSIE") !== -1) ? 'max' : 'grow'//'lowestElement'
                    });
                }
            });

            // задерживаем отрисовку контента до того момента как загрузится iframe
            $popup.one('show.grom.popup', function(e, popup) {
                var def = $.Deferred();

                $iframe.load(function() {
                    setTimeout(function() {
                        def.resolve($iframe);
                    }, 100);
                });

                return def.promise();
            });
            $popup.one('close.grom.popup', function(e, popup) {
                // при закрытии попапа постим месседж close.popup.iframe.gromver в айфрейм попапа
                postMessage(Event.CLOSE_POPUP, {}, childRelation(source));
                // ждем когда ивент пройдет
                var def = $.Deferred();
                setTimeout(function() {
                    def.resolve();
                }, 0);

                return def.promise();
            });
            $popup.one('closed.grom.popup', popRelation);
        });
        // событие отправки данных (попадает в окно топ уровня, и оттуда пересылается нужному окну событием receive)
        $(pub).on(Event.SEND, function(e, data, source) {
            postMessage(Event.RECEIVE, data, parentRelation(source));
        });
        // событие для получателя данных
        $(pub).on(Event.RECEIVE, function(e, data, source) {
            if ($.isFunction(this.dataHandler)) {
                this.dataHandler(data);
            }
        });
        // событие перезагрузки страницы (попадает в окно топ уровня, и оттуда пересылается нужному окну событием reload)
        $(pub).on(Event.REFRESH_PARENT, function(e, data, source) {
            // окно source посылает сообщение обновить его предка
            postMessage(Event.REFRESH, data, parentRelation(source));
            postMessage(Event.CLOSE);
        });
        $(pub).on(Event.REFRESH, function (e, data, source) {
            window.location.reload(true);
        });
        // событие перезагрузки страницы (попадает в окно топ уровня, и оттуда пересылается нужному окну событием reload)
        $(pub).on(Event.REDIRECT_PARENT, function(e, data, source) {
            postMessage(Event.REDIRECT, data, parentRelation(source));
            postMessage(Event.CLOSE);
        });
        $(pub).on(Event.REDIRECT, function (e, data, source) {
            window.location.replace(data);
        });
        // событие закрытия модального окна
        $(pub).on(Event.CLOSE, function(e, data, source) {
            yii.gromverPopup.close();
        });
    }

    function attachPostMessageHandler(handler) {
        if (window.addEventListener) {
            window.addEventListener("message", handler, false);
        } else {
            window.attachEvent("onmessage", handler);
        }
    }

    function pushRelation(parent, child) {
        relations.push({
            parent: parent,
            child: child
        });
    }

    function popRelation() {
        relations.pop();
    }

    function parentRelation(child) {
        var parent;

        $.each(relations, function(i, rel) {
            if (rel.child === child) {
                parent = rel.parent;
                return false;
            }
        });

        return parent || window;
    }

    function childRelation(parent) {
        var child;

        $.each(relations, function(i, rel) {
            if (rel.parent === parent) {
                child = rel.child;
                return false;
            }
        });

        return child;
    }

    return pub;
})(jQuery);
