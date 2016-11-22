angular.module("widgetkit", ["Application", "ngResource", "ngTouch", "Fields"]).value("name", "widgetkit").value("UIkit", jQuery.UIkit).factory("Content", ["$resource", "Application", function (e, t) {
    return e(t.url("/content/:id"), {}, {
        query: {method: "GET", responseType: "json"},
        save: {method: "POST", responseType: "json"}
    })
}]).filter("supported", ["Application", function (e) {
    return function (t, i) {
        return i ? t.filter(function (t) {
            var r = e.config.types[t.type], o = i.item.filter(function (e) {
                return -1 !== r.item.indexOf(e) ? !0 : void 0
            }).length;
            return o == i.item.length
        }) : t
    }
}]).filter("ucwords", ["Application", function (e) {
    return function (e) {
        return e.replace(/(-|_)/g, " ").replace(/^([a-z\u00E0-\u00FC])|\s+([a-z\u00E0-\u00FC])/g, function (e) {
            return e.toUpperCase()
        })
    }
}]).service("mediaInfo", ["Application", "$q", function (e, t) {
    return function (i, r) {
        i = i || "";
        var o, n, a = {
            url: i,
            type: "",
            src: i && !i.match(/^(https?:)?\//) ? e.baseUrl() + "/" + i : i,
            provider: null,
            image: e.config.images.placeholder,
            width: null,
            height: null
        };
        if (i.match(/\.(jpe?g|png|gif|svg)$/i) ? (a.type = "image", a.image = a.src) : i.match(/\.(mp3|ogg|wav)$/) ? (a.type = "audio", a.image = e.config.images.audio) : i.match(/\.(mp4|ogv|webm)$/) ? (a.type = "video", a.image = e.config.images.video) : (o = /(\/\/.*?)vimeo\.[a-z]+\/(?:\w*\/)*(\d+)/i.exec(i)) ? (a.provider = "vimeo", a.type = "iframe", a.src = "//player.vimeo.com/video/" + o[2], a.image = e.config.images.iframe) : ((o = /(\/\/.*?youtube\.[a-z]+)\/watch\?v=([^&]+)(.*)/i.exec(i)) || (o = /(\/\/.*?youtu\.be)\/([^\?]+)(.*)/i.exec(i))) && (a.provider = "youtube", a.type = "iframe", a.src = "//www.youtube.com/embed/" + o[2] + o[3].replace(/^&/, "?"), a.image = "//img.youtube.com/vi/" + o[2] + "/hqdefault.jpg"), r)switch (n = t.defer(), a.type) {
            case"image":
                var u = new Image;
                u.onerror = function () {
                    n.resolve(a)
                }, u.onload = function () {
                    a.width = u.width, a.height = u.height, n.resolve(a)
                }, u.src = a.src;
                break;
            case"video":
                var c = angular.element('<video style="position:fixed;visibility:hidden;top:-10000px;"></video>').attr("src", a.src).appendTo("body"), s = setInterval(function () {
                    c[0].videoWidth && (clearInterval(s), a.width = c[0].videoWidth, a.height = c[0].videoHeight, c.remove(), n.resolve(a))
                }, 20);
                break;
            case"iframe":
                "vimeo" == a.provider && jQuery.ajax({
                    type: "GET",
                    url: "http://vimeo.com/api/oembed.json?url=" + encodeURI(a.url),
                    jsonp: "callback",
                    dataType: "jsonp",
                    success: function (e) {
                        a.width = e.width, a.height = e.height, n.resolve(a)
                    }
                }).fail(function () {
                    n.resolve(a)
                }), "youtube" == a.provider && jQuery.ajax({
                    type: "GET",
                    url: "http://query.yahooapis.com/v1/public/yql",
                    data: {
                        q: "select * from json where url ='http://www.youtube.com/oembed?url=" + encodeURI(a.url) + "'",
                        format: "json"
                    },
                    dataType: "jsonp",
                    success: function (e) {
                        if (e && e.query && e.query.results && e.query.results.json) {
                            var t = jQuery(e.query.results.json.html);
                            a.width = t.attr("width"), a.height = t.attr("height")
                        }
                        n.resolve(a)
                    }
                }).fail(function () {
                    n.resolve(a)
                });
                break;
            default:
                n.resolve(a)
        }
        return r ? n.promise : a
    }
}]).factory("httpInterceptor", ["$q", "UIkit", function (e, t) {
    return {
        responseError: function (i) {
            var r;
            return r = i.data && i.data.message ? i.data.message : i.statusText, t.notify(r, "danger"), e.reject(i)
        }
    }
}]).config(["$httpProvider", "$sceProvider", function (e, t) {
    e.interceptors.push("httpInterceptor"), t.enabled(!1)
}]);
