<?php

// Id
$settings['id'] = substr(uniqid(), -3);

// Grid
$grid  = '{wk}-grid-width-1-'.$settings['columns'];
$grid .= $settings['columns_small'] ? ' {wk}-grid-width-small-1-'.$settings['columns_small'] : '';
$grid .= $settings['columns_medium'] ? ' {wk}-grid-width-medium-1-'.$settings['columns_medium'] : '';
$grid .= $settings['columns_large'] ? ' {wk}-grid-width-large-1-'.$settings['columns_large'] : '';
$grid .= $settings['columns_xlarge'] ? ' {wk}-grid-width-xlarge-1-'.$settings['columns_xlarge'] : '';

if ($settings['grid'] == 'dynamic') {

    // Filter Tags
    $tags = array();

    if (isset($settings['filter_tags']) && is_array($settings['filter_tags'])) {
        $tags = $settings['filter_tags'];
    }

    if(!count($tags)){
        foreach ($items as $i => $item) {
            if ($item['tags']) {
                $tags = array_merge($tags, $item['tags']);
            }
        }
        $tags = array_unique($tags);

        natsort($tags);
        $tags = array_values($tags);
    }

    // Filter Nav
    $tabs_center = '';
    if ($settings['filter'] == 'tabs') {

        $filter  = '{wk}-tab';
        $filter .= ($settings['filter_align'] == 'right') ? ' {wk}-tab-flip' : '';
        $filter .= ($settings['filter_align'] != 'center') ? ' {wk}-margin' : '';
        $tabs_center  = ($settings['filter_align'] == 'center') ? '{wk}-tab-center {wk}-margin' : '';

    } elseif ($settings['filter'] != 'none') {

        switch ($settings['filter']) {
            case 'text':
                $filter = '{wk}-subnav';
                break;
            case 'lines':
                $filter = '{wk}-subnav {wk}-subnav-line';
                break;
            case 'nav':
                $filter = '{wk}-subnav {wk}-subnav-pill';
                break;
        }

        $filter .= ' {wk}-flex-' . $settings['filter_align'];
    }

    // JS Options
    $options   = array();
    $options[] = ($settings['gutter_dynamic']) ? 'gutter: \'' . $settings['gutter_v_dynamic'] . ' ' . $settings['gutter_dynamic'] . '\'' : '';
    $options[] = ($settings['filter'] != 'none') ? 'controls: \'#wk-' . $settings['id'] . '\'' : '';
    $options[] = (count($tags) && $settings['filter'] != 'none' && !$settings['filter_all']) ? 'filter: \'' . $tags[0] . '\'': '';
    $options   = implode(',', array_filter($options));

    $grid_js   = $options ? 'data-{wk}-grid="{' . $options . '}"' : 'data-{wk}-grid';

} else {
    $grid .= ' {wk}-grid {wk}-grid-match';
    $grid .= in_array($settings['gutter'], array('collapse','large','medium','small')) ? ' {wk}-grid-'.$settings['gutter'] : '' ;
    $grid_js = 'data-{wk}-grid-match="{target:\'> div > .{wk}-panel\', row:true}" data-{wk}-grid-margin';

    if ($settings['parallax']) {
        $grid_js .= ' data-{wk}-grid-parallax' . ($settings['parallax_translate'] ? '="translate: ' . intval($settings['parallax_translate']) . '"' : '');
    }
}

// Title Size
switch ($settings['title_size']) {
    case 'panel':
        $title_size = '{wk}-panel-title';
        break;
    case 'large':
        $title_size = '{wk}-heading-large';
        break;
    default:
        $title_size = '{wk}-' . $settings['title_size'];
}

// Lightbox Title Size
switch ($settings['lightbox_title_size']) {
    case 'panel':
        $lightbox_title_size = '{wk}-panel-title';
        break;
    case 'large':
        $lightbox_title_size = '{wk}-heading-large';
        break;
    default:
        $lightbox_title_size = '{wk}-' . $settings['lightbox_title_size'];
}

// Content Size
switch ($settings['lightbox_content_size']) {
    case 'large':
        $lightbox_content_size = '{wk}-text-large';
        break;
    case 'h1':
    case 'h2':
    case 'h3':
    case 'h4':
    case 'h5':
    case 'h6':
        $lightbox_content_size = '{wk}-' . $settings['lightbox_content_size'];
        break;
    default:
        $lightbox_content_size = '';
}

// Button: Link
switch ($settings['link_style']) {
    case 'icon-small':
        $button_link = '{wk}-icon-small';
        break;
    case 'icon-medium':
        $button_link = '{wk}-icon-medium';
        break;
    case 'icon-large':
        $button_link = '{wk}-icon-large';
        break;
    case 'icon-button':
        $button_link = '{wk}-icon-button';
        break;
    case 'button':
        $button_link = '{wk}-button';
        break;
    case 'primary':
        $button_link = '{wk}-button {wk}-button-primary';
        break;
    case 'button-large':
        $button_link = '{wk}-button {wk}-button-large';
        break;
    case 'primary-large':
        $button_link = '{wk}-button {wk}-button-large {wk}-button-primary';
        break;
    case 'button-link':
        $link_style = '{wk}-button {wk}-button-link';
        break;
    default:
        $button_link = '';
}

switch ($settings['link_style']) {
    case 'icon':
    case 'icon-small':
    case 'icon-medium':
    case 'icon-large':
    case 'icon-button':
        $button_link .= ' {wk}-icon-' . $settings['link_icon'];
        $settings['link_text'] = '';
        break;
}

// Button: Lightbox
switch ($settings['lightbox_style']) {
    case 'icon-small':
        $button_lightbox = '{wk}-icon-small';
        break;
    case 'icon-medium':
        $button_lightbox = '{wk}-icon-medium';
        break;
    case 'icon-large':
        $button_lightbox = '{wk}-icon-large';
        break;
    case 'icon-button':
        $button_lightbox = '{wk}-icon-button';
        break;
    case 'button':
        $button_lightbox = '{wk}-button';
        break;
    case 'primary':
        $button_lightbox = '{wk}-button {wk}-button-primary';
        break;
    case 'button-large':
        $button_lightbox = '{wk}-button {wk}-button-large';
        break;
    case 'primary-large':
        $button_lightbox = '{wk}-button {wk}-button-large {wk}-button-primary';
        break;
    case 'button-link':
        $link_style = '{wk}-button {wk}-button-link';
        break;
    default:
        $button_lightbox = '';
}

switch ($settings['lightbox_style']) {
    case 'icon':
    case 'icon-small':
    case 'icon-medium':
    case 'icon-large':
    case 'icon-button':
        $button_lightbox .= ' {wk}-icon-' . $settings['lightbox_icon'];
        $settings['lightbox_text'] = '';
        break;
}

// Media Border
$border = ($settings['media_border'] != 'none') ? '{wk}-border-' . $settings['media_border'] : '';

// Animation
$animation = ($settings['animation'] != 'none') ? ' data-{wk}-scrollspy="{cls:\'{wk}-animation-' . $settings['animation'] . ' {wk}-invisible\', target:\'> div > .{wk}-panel\', delay:300}"' : '';

// Link Target
$link_target = ($settings['link_target']) ? ' target="_blank"' : '';

// Force overlay style
if (!in_array($settings['overlay'], array('default', 'center', 'bottom'))) {
    $settings['overlay'] = 'default';
}

?>

<?php if (isset($tags) && $tags && $settings['filter'] != 'none') : ?>

    <?php if ($tabs_center) : ?>
    <div class="<?php echo $tabs_center; ?>">
    <?php endif ?>

    <ul id="wk-<?php echo $settings['id']; ?>" class="<?php echo $filter; ?>"<?php if ($settings['filter'] == 'tabs') echo ' data-{wk}-tab'?>>

        <?php if ($settings['filter_all']) : ?>
        <li class="{wk}-active" data-{wk}-filter=""><a href="#"><?php echo $app['translator']->trans('All'); ?></a></li>
        <?php endif ?>

        <?php foreach ($tags as $i => $tag) : ?>
        <li data-{wk}-filter="<?php echo $tag; ?>"><a href="#"><?php echo ucwords($tag); ?></a></li>
        <?php endforeach; ?>

    </ul>

    <?php if ($tabs_center) : ?>
    </div>
    <?php endif ?>

<?php endif; ?>

<div id="wk-grid<?php echo $settings['id']; ?>" class="<?php echo $grid; ?> <?php echo $settings['class']; ?>" <?php echo $grid_js; ?> <?php echo $animation; ?>>

<?php foreach ($items as $index => $item) : ?>
    <?php if ($item['media']) :

        // Second Image as Thumbnail Overlay
        $thumbnail_overlay = '';
        $lightbox_alt      = '';
        foreach ($item as $field) {
            if ($field != 'media' && $item->type($field) == 'image') {
                $thumbnail_overlay = ($settings['overlay'] == 'default' && $settings['overlay_image']) ? $field : '';
                $lightbox_alt = $settings['lightbox_alt'] ? $field : '';
                break;
            }
        }

        // Thumbnails
        $thumbnail = '';

        if (($item->type('media') == 'image' || $item['media.poster'])) {

            $attrs           = array('class' => '');
            $width           = ($settings['image_width'] != 'auto') ? $settings['image_width'] : '';
            $height          = ($settings['image_height'] != 'auto') ? $settings['image_height'] : '';

            $attrs['alt']    = strip_tags($item['title']);
            $attrs['width']  = $width;
            $attrs['height'] = $height;

            $attrs['class'] .= ($settings['image_animation'] != 'none' && !$thumbnail_overlay) ? '{wk}-overlay-' . $settings['image_animation'] : '';

            if ($settings['image_width'] != 'auto' || $settings['image_height'] != 'auto') {
                $thumbnail = $item->thumbnail($item->type('media') == 'image' ? 'media' : 'media.poster', $width, $height, $attrs);
            } else {

                if(($item->type('media') == 'image') && $settings['gutter_dynamic']){
                    // adding the size of the original image to the attributes, so that on load the canvas can be created ( see script at the end of the file ).
                    if ($img  = $app['image']->create($item->get('media'))) {
                        $size = getimagesize($img->getPathName());
                        $width  = $size[0];
                        $height = $size[1];
                        $attrs['width'] = $width;
                        $attrs['height'] = $height;
                    }
                }

                $thumbnail = $item->media($item->type('media') == 'image' ? 'media' : 'media.poster', $attrs);
            }
        }

        // Lightbox
        $lightbox = '';
        $field = $lightbox_alt ? $lightbox_alt : 'media';
        if ($settings['lightbox']) {
            if ($item->type($field) == 'image') {
                if ($settings['lightbox_width'] != 'auto' || $settings['lightbox_height'] != 'auto') {

                    $width  = ($settings['lightbox_width'] != 'auto') ? $settings['lightbox_width'] : '';
                    $height = ($settings['lightbox_height'] != 'auto') ? $settings['lightbox_height'] : '';

                    $lightbox = 'href="' . htmlspecialchars($item->thumbnail($field, $width, $height, $attrs, true), null, null, false) . '" data-lightbox-type="image"';
                } else {
                    $lightbox = 'href="' . $item[$field] . '" data-lightbox-type="image"';
                }
            } else {
                $lightbox = 'href="' . $item[$field] . '"';
            }
        }

        // Second Image as Overlay
        if ($thumbnail_overlay) {

            $attrs['class'] .= ' {wk}-overlay-panel {wk}-overlay-image';
            $attrs['class'] .= ($settings['image_animation'] != 'none') ? ' {wk}-overlay-' . $settings['image_animation'] : '';

            $thumbnail_overlay = $item->thumbnail($thumbnail_overlay, $width, $height, $attrs);
        }

        // Lightbox Caption
        $lightbox_caption = '';
        switch ($settings['lightbox_caption']) {
            case 'title':
                $lightbox_caption = $item['title'];
                break;
            case 'content':
                $lightbox_caption = $item['lightbox_content'] ? $item['lightbox_content'] : $item['content'];
                break;
        }
        $lightbox_caption = $lightbox_caption ? 'title="' . strip_tags($lightbox_caption) .'"' : '';

        // Filter
        $filter = '';
        if ($item['tags'] && $settings['grid'] == 'dynamic' && $settings['filter'] != 'none') {
            $filter = ' data-{wk}-filter="' . implode(',', $item['tags']) . '"';
        }

    ?>

    <div<?php echo $filter; ?>>
    <?php echo $this->render('plugins/widgets/' . $widget->getConfig('name')  . '/views/_' . $settings['overlay'] . '.php', compact('item', 'settings', 'title_size', 'border', 'thumbnail', 'thumbnail_overlay', 'lightbox', 'lightbox_caption', 'button_link', 'button_lightbox', 'link_target', 'index', 'width', 'height')); ?>
    </div>

    <?php endif; ?>
<?php endforeach; ?>

</div>

<?php if ($settings['lightbox'] === 'slideshow') : ?>
<div id="wk-3<?php echo $settings['id']; ?>" class="{wk}-modal">
    <div class="{wk}-modal-dialog {wk}-modal-dialog-blank">

        <button class="{wk}-modal-close {wk}-close" type="button"></button>

        <div class="{wk}-grid" data-{wk}-grid-margin>
            <div class="{wk}-width-medium-1-2 {wk}-text-center">

                <div class="{wk}-slidenav-position" data-{wk}-slideshow data-{wk}-check-display>
                    <ul class="{wk}-slideshow {wk}-slideshow-fullscreen">
                        <?php foreach ($items as $item) :

                            // Alternative Media Field
                            $field = 'media';
                            if ($settings['lightbox_alt']) {
                                foreach ($item as $media_field) {
                                    if (($item[$media_field] != $item['media']) && ($item->type($media_field) == 'image')) {
                                        $field = $media_field;
                                        break;
                                    }
                                }
                            }

                            // Media Type
                            $attrs  = array('class' => '');
                            $width  = $item[$field . '.width'];
                            $height = $item[$field . '.height'];

                            if ($item->type($field) == 'image') {
                                $attrs['alt'] = strip_tags($item['title']);
                                $width  = ($settings['lightbox_width'] != 'auto') ? $settings['lightbox_width'] : $width;
                                $height = ($settings['lightbox_height'] != 'auto') ? $settings['lightbox_height'] : $height;
                            }

                            if ($item->type($field) == 'video') {
                                $attrs['class'] = '{wk}-responsive-width';
                                $attrs['controls'] = true;
                            }

                            if ($item->type($field) == 'iframe') {
                                $attrs['class'] = '{wk}-responsive-width';
                            }

                            $attrs['width']  = ($width) ? $width : '';
                            $attrs['height'] = ($height) ? $height : '';

                            if (($item->type($field) == 'image') && ($settings['lightbox_width'] != 'auto' || $settings['lightbox_height'] != 'auto')) {
                                $media = $item->thumbnail($field, $width, $height, $attrs);
                            } else {
                                $media = $item->media($field, $attrs);
                            }

                        ?>

                            <li>
                                <?php echo $media; ?>
                            </li>

                        <?php endforeach; ?>
                    </ul>

                    <a href="#" class="{wk}-slidenav <?php if ($settings['lightbox_nav_contrast']) echo '{wk}-slidenav-contrast'; ?> {wk}-slidenav-previous {wk}-hidden-touch" data-{wk}-slideshow-item="previous"></a>
                    <a href="#" class="{wk}-slidenav <?php if ($settings['lightbox_nav_contrast']) echo '{wk}-slidenav-contrast'; ?> {wk}-slidenav-next {wk}-hidden-touch" data-{wk}-slideshow-item="next"></a>

                </div>
            </div>
            <div class="{wk}-width-medium-1-2 {wk}-flex {wk}-flex-middle {wk}-flex-center">

                <div class="{wk}-panel-body {wk}-width-1-1 <?php echo $settings['lightbox_content_width'] ? '{wk}-width-xlarge-' . $settings['lightbox_content_width'] : ''; ?>" data-{wk}-slideshow data-{wk}-check-display>
                    <ul class="{wk}-slideshow">
                        <?php foreach ($items as $item) : ?>
                        <li>

                            <?php if ($item['title']) : ?>
                            <h3 class="<?php echo $lightbox_title_size; ?>"><?php echo $item['title']; ?></h3>
                            <?php endif; ?>

                            <?php if ($item['lightbox_content']) : ?>
                            <div class="{wk}-margin-top <?php echo $lightbox_content_size; ?>"><?php echo $item['lightbox_content']; ?></div>
                            <?php elseif ($item['content']) : ?>
                            <div class="{wk}-margin-top <?php echo $lightbox_content_size; ?>"><?php echo $item['content']; ?></div>
                            <?php endif; ?>

                            <?php if ($item['link'] && $settings['link']) : ?>
                            <p class="{wk}-margin-bottom-remove"><a href="<?php echo $item->escape('link'); ?>"<?php echo $link_target; ?>><?php echo $app['translator']->trans($settings['link_text']); ?></a></p>
                            <?php endif; ?>

                        </li>
                    <?php endforeach; ?>
                    </ul>

                    <div class="{wk}-margin-top">
                        <ul class="{wk}-thumbnav {wk}-margin-bottom-remove">
                        <?php foreach ($items as $i => $item) :

                                // Thumbnails
                                $thumbnail = '';
                                if (($item->type('media') == 'image' || $item['media.poster'])) {

                                    $attrs           = array();
                                    $width           = ($settings['lightbox_nav_width'] != 'auto') ? $settings['lightbox_nav_width'] : $item['media.width'];
                                    $height          = ($settings['lightbox_nav_height'] != 'auto') ? $settings['lightbox_nav_height'] : $item['media.height'];

                                    $attrs['alt']    = strip_tags($item['title']);
                                    $attrs['width']  = $width;
                                    $attrs['height'] = $height;

                                    if ($settings['lightbox_nav_width'] != 'auto' || $settings['lightbox_nav_height'] != 'auto') {
                                        $thumbnail = $item->thumbnail($item->type('media') == 'image' ? 'media' : 'media.poster', $width, $height, $attrs);
                                    } else {
                                        $thumbnail = $item->media($item->type('media') == 'image' ? 'media' : 'media.poster', $attrs);
                                    }
                                }

                            ?>
                            <li data-{wk}-slideshow-item="<?php echo $i; ?>"><a href="#"><?php echo ($thumbnail) ? $thumbnail : $item['title']; ?></a></li>
                        <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<script>
    (function($){

        var modal      = $('#wk-3<?php echo $settings['id']; ?>'),
            container  = modal.prev(),
            slideshows = modal.find('[data-{wk}-slideshow]'),
            slideshow;

        container.on('click', '[href^="#wk-"][data-{wk}-modal]', function(e) {
            slideshows.each(function(){

                slideshow = $(this).data('slideshow');
                slideshow.show(parseInt(e.target.getAttribute('data-index'), 10));
            });
        });

        modal.on('beforeshow.uk.slideshow', function(e, next) {
            slideshows.not(next.closest('[data-{wk}-slideshow]')[0]).data('slideshow').show(next.index());
        });

    })(jQuery);
</script>
<?php endif; ?>

<script>
(function($){

    // get the images of the gallery and replace it by a canvas of the same size to fix the problem with overlapping images on load.
    $('img[width][height]:not(.{wk}-overlay-panel)', $('#wk-grid<?php echo $settings['id']; ?>')).each(function() {

        var $img = $(this);

        if (this.width == 'auto' || this.height == 'auto' || !$img.is(':visible')) {
            return;
        }

        var $canvas = $('<canvas class="{wk}-responsive-width"></canvas>').attr({width:$img.attr('width'), height:$img.attr('height')}),
            img = new Image,
            release = function() {
                $canvas.remove();
                $img.css('display', '');
                release = function(){};
            };

        $img.css('display', 'none').after($canvas);

        $(img).on('load', function(){ release(); });
        setTimeout(function(){ release(); }, 1000);

        img.src = this.src;

    });

})(jQuery);
</script>
