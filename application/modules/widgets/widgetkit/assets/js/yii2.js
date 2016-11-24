/*
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

!function (t, n) {
    angular.module("widgetkit").run(["$rootScope", "$rootElement", "$timeout", "$filter", function (e, o, a, i) {
        function s() {
            r.find("#toolbar-apply button, #toolbar-save button").prop("disabled", o.find("form.ng-invalid").length)
        }

        n.parent.document.updateUploader = n.parent.document.updateUploader || function () {};
        var c = t("body.com_widgetkit"),
            l = c.find(".content-wrapper .content .box .box-title")
                .html('Widgetkit:')
                .find(".page-title span").eq(1),

            r = c.find(".content .box .actions"),
            p = '<span id="toolbar-create">' +
                '<button class="btn btn-circle btn-sm btn-success"></span>' + i("trans")("New") + "</button> " +
                "</span>",

            d = ' <span id="toolbar-apply">' +
                '<button class="btn btn-circle btn-success btn-sm"><span class="fa fa-check"></span> ' + i("trans")("Save") + '</button>' +
                '</span>' +
                ' <span id="toolbar-save">' +
                '<button class="btn btn-circle btn-success btn-sm"><span class="fa fa-floppy-o"></span> ' + i("trans")("Save & Close") + '</button>' +
                '</span>' +
                ' <span id="toolbar-cancel">' +
                '<button class="btn btn-circle btn-default btn-sm"><span class="fa fa-ban"></span> ' + i("trans")("Close") + "</button>" +
                "</span>",

            u = ' <span id="toolbar-options"><button class="btn btn-circle btn-default btn-sm"><span class="fa fa-cogs"></span></button></span>';

        r.on("click", "#toolbar-create button", function () {
            o.scope().vm.createContent()
        }).on("click", "#toolbar-apply", function () {
            o.scope().vm.saveContent()
        }).on("click", "#toolbar-save", function () {
            var t = o.scope();
            console.log(t.vm);
            t.vm.saveContent().$promise.then(function () {
                t.vm.setView("content")
            })
        }).on("click", "#toolbar-cancel", function () {
            var t = o.scope();
            t.vm.setView("content"), t.$apply()
        }).on("click", "#toolbar-options", function () {
            location.href = "/admin/modules/setting?name=widgets"
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
    });
}(jQuery, window);
