!function (t, n) {
    angular.module("widgetkit").run(["$rootScope", "$rootElement", "$timeout", "$filter", function (e, o, a, i) {
        function s() {
            r.find("#toolbar-apply button, #toolbar-save button").prop("disabled", o.find("form.ng-invalid").length)
        }

        n.parent.document.updateUploader = n.parent.document.updateUploader || function () {};
        var c = t("body.com_widgetkit"),
            l = c.find(".header .container-title").append('<h1 class="page-title"><span class="icon-widgetkit"></span>Widgetkit: <span></span></h1>').find(".page-title span").eq(1), r = c.find(".subhead .btn-toolbar"), p = '<div class="btn-wrapper" id="toolbar-create"><button class="btn btn-small btn-success"></span>' + i("trans")("New") + "</button></div>", d = '<div class="btn-wrapper" id="toolbar-apply"><button class="btn btn-small btn-success"><span class="icon-apply icon-white"></span> ' + i("trans")("Save") + '</button></div>                           <div class="btn-wrapper" id="toolbar-save"><button class="btn btn-small"><span class="icon-save"></span> ' + i("trans")("Save & Close") + '</button></div>                           <div class="btn-wrapper" id="toolbar-cancel"><button class="btn btn-small"><span class="icon-cancel"></span> ' + i("trans")("Close") + "</button></div>", u = '<div class="btn-wrapper" id="toolbar-options"><button class="btn btn-small"><span class="icon-options"></span> ' + i("trans")("Options") + "</button></div>";
        r.on("click", "#toolbar-create button", function () {
            o.scope().vm.createContent()
        }).on("click", "#toolbar-apply", function () {
            o.scope().vm.saveContent()
        }).on("click", "#toolbar-save", function () {
            var t = o.scope();
            t.vm.d().$promise.then(function () {
                t.vm.setView("content")
            })
        }).on("click", "#toolbar-cancel", function () {
            var t = o.scope();
            t.vm.setView("content"), t.$apply()
        }).on("click", "#toolbar-options", function () {
            location.href = "index.php?option=com_config&view=component&component=com_widgetkit"
        }), c.on("keyup", '[ng-model="content.name"]', s), e.$on("wk.change.view", function (t, n) {
            a(function () {
                l.text(o.find("h2.js-header").text()), r.empty().html("content" == n ? p : "contentEdit" == n ? d : "").append(u), s()
            })
        })
    }]), t(function () {
        t(document).on("click", '[rel="widgetkit"], [aria-label="Widgetkit"]', function (e) {
            e.preventDefault(), e.stopPropagation();
            for (var o = t(this); o.length && !o.has("textarea").length;)o = o.parent();
            n.widgetkit.env.editor(o.find("textarea:first"))
        });
        var e = t(".view-module .widgetkit-widget, #modules-form .widgetkit-widget"), o = e.nextAll("input"), a = {
            value: function () {
                try {
                    return JSON.parse(o.val())
                } catch (t) {
                    return {}
                }
            }, update: function () {
                var t = this.value().name;
                e.text(t ? Translator.trans("Widget: %widget%", {widget: t}) : Translator.trans("Select Widget"))
            }
        };
        e.on("click", function (t) {
            t.preventDefault(), n.widgetkit.env.init("widget", a.value(), function (t) {
                o.val(JSON.stringify(t)), a.update()
            })
        }), a.update()
    }), t(function () {
        if (n.MooTools) {
            var e = Element.prototype.hide;
            Element.prototype.hide = function () {
                return t(this).is('[class*="uk-"]') ? this : e.apply(this, [])
            }
        }
    })
}(jQuery, window);
