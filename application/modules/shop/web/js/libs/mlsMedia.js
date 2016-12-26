/**
 * Created by Volodymyr Hryvinskyi on 24.12.2016.
 */
;(function ($) {

    $.mlsMedia = {
        zoomImage: function () {
            var selector = $('[data-zoom-image]');

            //Destroy previous zoom to prevent images duplication
            selector.trigger('zoom.destroy');

            //Init zoom to each element in list
            selector.each(function () {
                var zoomImage = $(this);
                var zoomedImageUrl = zoomImage.attr('data-zoom-image');
                var zoomedImageWrapper = zoomImage.siblings('[data-zoom-wrapper]');

                zoomImage.zoom({
                    url: zoomedImageUrl,
                    target: zoomedImageWrapper,
                    touch: false,

                    onZoomIn: function () {
                        zoomedImageWrapper.removeClass('hidden');
                    },
                    onZoomOut: function () {
                        zoomedImageWrapper.addClass('hidden');
                    },
                    callback: function () {
                        var zoomedImage = $(this);

                        if ((zoomImage.width() >= zoomedImage.width()) && (zoomImage.height() >= zoomedImage.height())) {
                            selector.trigger('zoom.destroy');
                        }
                    }
                });
            });
        },
        magnificGalley: function (startIndex, outerGallery) {
            startIndex = startIndex || 0;
            outerGallery = outerGallery || $('[data-magnific-galley]');

            outerGallery.each(function () {

                var gallery = $(this);
                var mainImage = gallery.find('[data-magnific-galley-main]');
                var thumbList = gallery.find('[data-magnific-galley-thumb]');
                var imgStartArr = [];
                var imgPreArr;
                var imgShiftArr;

                if (thumbList.size() > 0) {
                    thumbList.each(function () {
                        var imgSrc = {
                            src: $(this).attr('href')
                        };
                        imgStartArr.push(imgSrc);
                    });

                    imgPreArr = imgStartArr.splice(0, startIndex);
                    imgShiftArr = imgStartArr.concat(imgPreArr);
                }

                mainImage.magnificPopup({
                    items: imgShiftArr,
                    type: "image",
                    gallery: {
                        enabled: true,
                        tCounter: '%curr% of %total%'
                    },
                    overflowY: "hidden",
                    image: {
                        titleSrc: 'data-magnific-galley-title'
                    }
                });

            });
        }
    };

})(jQuery);
