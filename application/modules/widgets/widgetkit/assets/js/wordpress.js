!function (e, t) {
    angular.module("widgetkit").service("mediaPicker", ["$location", "$q", "Application", function (e, t, n) {
        function i(e) {
            var t = document.createElement("a");
            return t.href = e, t
        }

        var r = new RegExp("^" + n.baseUrl());
        return {
            select: function (n) {
                n = angular.extend({title: "Pick media", multiple: !1, button: {text: "Select"}}, n);
                var a = t.defer(), o = wp.media(n).on("select", function () {
                    var t = o.state().get("selection").map(function (t) {
                        var n = t.toJSON(), a = i(n.url);
                        return a.host == e.host() && (n.url = a.pathname.replace(r, "").replace(/^\//, "")), n
                    });
                    a.resolve(n.multiple ? t : t[0])
                }).open();
                return a.promise
            }
        }
    }]), e(function () {
        e("body").on("click", ".widgetkit-editor", function (n) {
            n.preventDefault(), t.widgetkit.env.editor(e("textarea#content"))
        }).on("click", ".widgetkit-widget button", function (n) {
            n.preventDefault();
            var i = e(this).nextAll("input"), r = e(this).closest("form").find(".widget-control-save");
            t.widgetkit.env.init(JSON.parse(i.val()), function (e) {
                i.val(JSON.stringify(e)), r.trigger("click")
            })
        }).find("[data-app]").addClass("wrap")
    })
}(jQuery, window);
