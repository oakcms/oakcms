/**
 * Created by Volodymyr Hryvinskyi on 24.12.2016.
 */
$(document).on('ready', function () {
    $.mlsMedia.magnificGalley();
    $.mlsMedia.zoomImage();
});


/* JS linking to product page instead of <a> element by SEO demand */
$(document).on('click', '[data-product-photo-href]', function () {
    var href = $(this).attr('data-product-photo-href');
    location.assign(href);
});


/* Changing main photo after clicking on thumb image */
$(document).on('click', '[data-product-photo-thumb]', function (e) {
    e.preventDefault();

    var currThumb = $(this);
    var context = currThumb.closest('[data-product-photo-scope]');

    var allThumbs = '[data-product-photo-thumb]';
    var activeThumb = '[data-product-photo-thumb-active]';
    var activeThumbPosition;
    var currGallery = currThumb.closest('[data-magnific-galley]');

    var largePhotoUrl = currThumb.attr('href');
    var targetLink = context.find('[data-product-photo-link]');
    var targetImg = context.find('[data-product-photo]');
    var zoomedImageLink = context.find('[data-zoom-image]');

    /* Toggle thumbs activity */
    context.find(allThumbs).removeAttr('data-product-photo-thumb-active');
    currThumb.attr('data-product-photo-thumb-active', '');

    /* Setting link to large photo in the main photo */
    targetLink.attr('href', largePhotoUrl);
    targetImg.attr('src', largePhotoUrl);
    zoomedImageLink.attr('data-zoom-image', largePhotoUrl);
    $.mlsMedia.zoomImage();

    /* Calculate index of active thumb among all thumbs */
    activeThumbPosition = context.find(allThumbs).index(context.find(activeThumb));

    /* Call magnific gallery and set active image */
    $.mlsMedia.magnificGalley(activeThumbPosition, currGallery);

});
