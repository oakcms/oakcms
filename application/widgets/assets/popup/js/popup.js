/*-------------------------------

 popup.js

 Simple Popup plugin Yii2

 @author gromver5@gmail.com
 @version 1.1.0

 -------------------------------*/
yii.gromverPopup = (function ($) {
    var popupStack = [],
        popupCounter = 0;

    var EVENT_KEY = '.grom.popup';

    var Default = {
        backdrop: true, // true, false, 'static'
        keyboard: true,
        content : null,
        width : '80%',
        height : null
    };

    var Event = {
        HIDE: 'hide' + EVENT_KEY,
        HIDDEN: 'hidden' + EVENT_KEY,
        SHOW: 'show' + EVENT_KEY,
        SHOWN: 'shown' + EVENT_KEY,
        CLOSE: 'close' + EVENT_KEY,
        CLOSED: 'closed' + EVENT_KEY,
        LOAD: 'load' + EVENT_KEY,
        LOADED: 'loaded' + EVENT_KEY,
        KEYDOWN_DISMISS: 'keydown.dismiss' + EVENT_KEY//,
    };

    var ClassName = {
        BACKDROP: 'grom-backdrop',
        CONTAINER: 'grom-popup',
        CONTENT: 'grom-popup__content',
        CLOSE: 'grom-popup__close',
        OPEN: 'grom-popup-open'
    };

    function Popup(config) {
        var _this = this;

        this._id = ++popupCounter;
        this._config = config;
        this.$container = $('<div/>').addClass(ClassName.CONTAINER).appendTo(document.body);
        if (config.backdrop) {
            this.$container.addClass(ClassName.CONTAINER + '_backdrop');
            if (config.backdrop != 'static') {
                this.$container.on('click', function (e) {
                    if (e.target === this) {
                        _this.close();
                    }
                });
            }
        }
        this.$popup = $('<div/>').addClass(ClassName.CONTENT).appendTo(this.$container);
        this.$close = $('<div>&times;</div>').addClass(ClassName.CLOSE).on('click', function() {
            _this.close();
        }).appendTo(this.$popup);

        // customize popup
        if (config.width) {
            this.$popup.css('width', config.width, 10);
        } else {
            this.$popup.css('width', '');
        }

        if (config.height) {
            this.$popup.css('height', config.height, 10);
        } else {
            this.$popup.css('height', '');
        }

        if (config.class) {
            this.$popup.addClass(config.class);
        }

        if (config.style) {
            this.$popup.attr('style', config.style);
        }

        setTimeout(function () {
            _this.show();
        }, 0);
    }

    Popup.prototype = {
        show: function () {
            var _this = this,
                complete = $.Deferred(),
                showEvent = $.Event(Event.SHOW);

            if (this._config.keyboard) {
                $(document).on(Event.KEYDOWN_DISMISS + this._id, function (event) {
                    if (event.which === 27) {
                        _this.close();
                    }
                });
            }

            this.$container.trigger(showEvent, [this]);

            this.$container.addClass(ClassName.CONTAINER + '_visible');

            if (isPromise(showEvent.result)) {
                $.when(this.load(), showEvent.result).done(showPopup);
            } else {
                $.when(this.load()).done(showPopup);
            }

            function showPopup () {
                $(document.body).addClass(ClassName.OPEN);
                _this.$container.removeClass(ClassName.CONTAINER + '_loading').trigger(Event.SHOWN, [_this]);
                _this.$popup.addClass(ClassName.CONTENT + '_visible');

                complete.resolve(_this);
            }

            return complete.promise();
        },
        hide: function () {
            var _this = this,
                complete = $.Deferred(),
                hideEvent = $.Event(Event.HIDE);

            if (this._config.keyboard) {
                $(document).off(Event.KEYDOWN_DISMISS + this._id);
            }

            this.$container.trigger(hideEvent, [this]);

            if (isPromise(hideEvent.result)) {
                hideEvent.result.done(hidePopup);
            } else {
                hidePopup();
            }

            function hidePopup () {
                _this.$container.removeClass(ClassName.CONTAINER + '_visible');
                $(document.body).removeClass(ClassName.OPEN);

                _this.$container.trigger(Event.HIDDEN, [_this]);
                complete.resolve(_this);
            }

            return complete.promise();
        },
        close: function () {
            var _this = this,
                complete = $.Deferred(),
                closeEvent = $.Event(Event.CLOSE);

            togglePopup();

            this.hide();

            this.$container.trigger(closeEvent, [this]);

            if (isPromise(closeEvent.result)) {
                closeEvent.result.done(closePopup);
            } else {
                closePopup();
            }


            function closePopup () {
                _this.$container.trigger(Event.CLOSED, [_this]).detach();
                delete _this.$container;
                delete _this.$popup;
                delete _this.$close;
                delete _this.$content;

                complete.resolve(_this);
            }

            return complete.promise();
        },
        load: function() {
            if (typeof this.$content !== "undefined") return this.$content;

            var _this = this,
                complete = $.Deferred(),
                loadEvent = $.Event(Event.LOAD);

            // определяем контент
            this.$container.addClass(ClassName.CONTAINER + '_loading').trigger(loadEvent, [this]);

            if (isPromise(loadEvent.result)) {
                loadEvent.result.done(process);
            } else {
                process(this._config.content);
            }

            function process (content) {
                if (content) {
                    if (isPromise(content)) {
                        // promise
                        content.done(function (content) {
                            _this.$content = $(content).appendTo(_this.$popup);
                            _this.$container.trigger(Event.LOADED, [_this]);

                            complete.resolve(_this);
                        });
                    } else {
                        _this.$content = $(content).appendTo(_this.$popup);
                        _this.$container.trigger(Event.LOADED, [_this]);

                        complete.resolve(_this);
                    }
                } else {
                    _this.$content = $('<p>Content is not defined!</p>').appendTo(_this.$popup);
                    _this.$container.trigger(Event.LOADED, [_this]);

                    complete.resolve(_this);
                }
            }

            return complete.promise();
        },
        prop: function (name) {
            return this[name];
        },
        instance: function () {
            return this;
        }
    };

    function isPromise(obj) {
        return obj && obj.then && obj.done && obj.fail;
    }

    var pub = {
        open: function(config) {
            $.each(popupStack, function(index, popup) {
                popup.hide();
            });

            var instance = new Popup($.extend({}, Default, config));
            popupStack.push(instance);

            return instance.$container;
        },
        close: function() {
            var popup;
            if (popupStack.length && (popup = popupStack[popupStack.length-1])) {
                popup.close();
            }
        },
        one: function () {
            dispatcher.one.apply(dispatcher, arguments)
        },
        bind: function () {
            dispatcher.bind.apply(dispatcher, arguments)
        },
        unbind: function () {
            dispatcher.unbind.apply(dispatcher, arguments)
        }
    };

    var dispatcher = $(pub);

    function togglePopup() {
        var popup;

        popupStack.pop();

        if (popupStack.length && (popup = popupStack[popupStack.length-1])) {
            popup.show();
        }
    }

    $(document).on('click.yii', '[data-behavior="grom-popup"]', function(event) {
        var $this = $(this),
            config = {
                backdrop: $this.data('backdrop'),
                keyboard: $this.data('keyboard')
            };

        config.content = ($this.attr('href') ? $.get($this.attr('href')) : null) || $this.data('popupContent') || ($this.data('popupSource') ? $($this.data('popupSource')).clone().show() : null);
        pub.open(config);

        event.stopImmediatePropagation();
        event.preventDefault();
    });

    return pub;

    //$.fn.gromPopup = function (config, relatedTarget) {
    //    return this.each(function () {
    //        var data = $(this).data(DATA_KEY);
    //        var _config = $.extend({}, Default, /*Modal.Default, */$(this).data(), typeof config === 'object' && config);
    //
    //        if (!data) {
    //            data = new Popup(this, _config);
    //            $(this).data(DATA_KEY, data);
    //        }
    //
    //        if (typeof config === 'string') {
    //            if (data[config] === undefined) {
    //                throw new Error('No method named "' + config + '"');
    //            }
    //            data[config](relatedTarget);
    //        } else if (_config.show) {
    //            data.show(relatedTarget);
    //        }
    //    });
    //}
})(jQuery);