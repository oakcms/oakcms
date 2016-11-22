<?php

// Id
$settings['id'] = substr(uniqid(), -3);

// Grid
$grid  = 'uk-grid-width-1-'.$settings['columns'];
$grid .= $settings['columns_small'] ? ' uk-grid-width-small-1-'.$settings['columns_small'] : '';
$grid .= $settings['columns_medium'] ? ' uk-grid-width-medium-1-'.$settings['columns_medium'] : '';
$grid .= $settings['columns_large'] ? ' uk-grid-width-large-1-'.$settings['columns_large'] : '';
$grid .= $settings['columns_xlarge'] ? ' uk-grid-width-xlarge-1-'.$settings['columns_xlarge'] : '';

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

        $filter  = 'uk-tab';
        $filter .= ($settings['filter_align'] == 'right') ? ' uk-tab-flip' : '';
        $filter .= ($settings['filter_align'] != 'center') ? ' uk-margin' : '';
        $tabs_center  = ($settings['filter_align'] == 'center') ? 'uk-tab-center uk-margin' : '';

    } elseif ($settings['filter'] != 'none') {

        switch ($settings['filter']) {
            case 'text':
                $filter = 'uk-subnav';
                break;
            case 'lines':
                $filter = 'uk-subnav uk-subnav-line';
                break;
            case 'nav':
                $filter = 'uk-subnav uk-subnav-pill';
                break;
        }

        $filter .= ' uk-flex-' . $settings['filter_align'];
    }

    // JS Options
    $options   = array();
    $options[] = ($settings['gutter_dynamic']) ? 'gutter: \'' . $settings['gutter_v_dynamic'] . ' ' . $settings['gutter_dynamic'] . '\'' : '';
    $options[] = ($settings['filter'] != 'none') ? 'controls: \'#wk-' . $settings['id'] . '\'' : '';
    $options[] = (count($tags) && $settings['filter'] != 'none' && !$settings['filter_all']) ? 'filter: \'' . $tags[0] . '\'': '';
    $options   = implode(',', array_filter($options));

    $grid_js   = $options ? 'data-uk-grid="{' . $options . '}"' : 'data-uk-grid';

} else {
    $grid .= ' uk-grid uk-grid-match';
    $grid .= in_array($settings['gutter'], array('collapse','large','medium','small')) ? ' uk-grid-'.$settings['gutter'] : '' ;
    $grid_js = 'data-uk-grid-match="{target:\'> div > .uk-panel\', row:true}" data-uk-grid-margin';

    if ($settings['parallax']) {
        $grid_js .= ' data-uk-grid-parallax' . ($settings['parallax_translate'] ? '="translate: ' . intval($settings['parallax_translate']) . '"' : '');
    }
}

// Title Size
switch ($settings['title_size']) {
    case 'panel':
        $title_size = 'uk-panel-title';
        break;
    case 'large':
        $title_size = 'uk-heading-large';
        break;
    default:
        $title_size = 'uk-' . $settings['title_size'];
}

// Lightbox Title Size
switch ($settings['lightbox_title_size']) {
    case 'panel':
        $lightbox_title_size = 'uk-panel-title';
        break;
    case 'large':
        $lightbox_title_size = 'uk-heading-large';
        break;
    default:
        $lightbox_title_size = 'uk-' . $settings['lightbox_title_size'];
}

// Content Size
switch ($settings['lightbox_content_size']) {
    case 'large':
        $lightbox_content_size = 'uk-text-large';
        break;
    case 'h1':
    case 'h2':
    case 'h3':
    case 'h4':
    case 'h5':
    case 'h6':
        $lightbox_content_size = 'uk-' . $settings['lightbox_content_size'];
        break;
    default:
        $lightbox_content_size = '';
}

// Button: Link
switch ($settings['link_style']) {
    case 'icon-small':
        $button_link = 'uk-icon-small';
        break;
    case 'icon-medium':
        $button_link = 'uk-icon-medium';
        break;
    case 'icon-large':
        $button_link = 'uk-icon-large';
        break;
    case 'icon-button':
        $button_link = 'uk-icon-button';
        break;
    case 'button':
        $button_link = 'uk-button';
        break;
    case 'primary':
        $button_link = 'uk-button uk-button-primary';
        break;
    case 'button-large':
        $button_link = 'uk-button uk-button-large';
        break;
    case 'primary-large':
        $button_link = 'uk-button uk-button-large uk-button-primary';
        break;
    case 'button-link':
        $link_style = 'uk-button uk-button-link';
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
        $button_link .= ' uk-icon-' . $settings['link_icon'];
        $settings['link_text'] = '';
        break;
}

// Button: Lightbox
switch ($settings['lightbox_style']) {
    case 'icon-small':
        $button_lightbox = 'uk-icon-small';
        break;
    case 'icon-medium':
        $button_lightbox = 'uk-icon-medium';
        break;
    case 'icon-large':
        $button_lightbox = 'uk-icon-large';
        break;
    case 'icon-button':
        $button_lightbox = 'uk-icon-button';
        break;
    case 'button':
        $button_lightbox = 'uk-button';
        break;
    case 'primary':
        $button_lightbox = 'uk-button uk-button-primary';
        break;
    case 'button-large':
        $button_lightbox = 'uk-button uk-button-large';
        break;
    case 'primary-large':
        $button_lightbox = 'uk-button uk-button-large uk-button-primary';
        break;
    case 'button-link':
        $link_style = 'uk-button uk-button-link';
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
        $button_lightbox .= ' uk-icon-' . $settings['lightbox_icon'];
        $settings['lightbox_text'] = '';
        break;
}

// Media Border
$border = ($settings['media_border'] != 'none') ? 'uk-border-' . $settings['media_border'] : '';

// Animation
$animation = ($settings['animation'] != 'none') ? ' data-uk-scrollspy="{cls:\'uk-animation-' . $settings['animation'] . ' uk-invisible\', target:\'> div > .uk-panel\', delay:300}"' : '';

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

    <ul id="wk-<?php echo $settings['id']; ?>" class="<?php echo $filter; ?>"<?php if ($settings['filter'] == 'tabs') echo ' data-uk-tab'?>>

        <?php if ($settings['filter_all']) : ?>
        <li class="uk-active" data-uk-filter=""><a href="#"><?php echo $app['translator']->trans('All'); ?></a></li>
        <?php endif ?>

        <?php foreach ($tags as $i => $tag) : ?>
        <li data-uk-filter="<?php echo $tag; ?>"><a href="#"><?php echo ucwords($tag); ?></a></li>
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

            $attrs['class'] .= ($settings['image_animation'] != 'none' && !$thumbnail_overlay) ? 'uk-overlay-' . $settings['image_animation'] : '';

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

            $attrs['class'] .= ' uk-overlay-panel uk-overlay-image';
            $attrs['class'] .= ($settings['image_animation'] != 'none') ? ' uk-overlay-' . $settings['image_animation'] : '';

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
            $filter = ' data-uk-filter="' . implode(',', $item['tags']) . '"';
        }

    ?>

    <div<?php echo $filter; ?>>
    <?php echo $this->render('plugins/widgets/' . $widget->getConfig('name')  . '/views/_' . $settings['overlay'] . '.php', compact('item', 'settings', 'title_size', 'border', 'thumbnail', 'thumbnail_overlay', 'lightbox', 'lightbox_caption', 'button_link', 'button_lightbox', 'link_target', 'index', 'width', 'height')); ?>
    </div>

    <?php endif; ?>
<?php endforeach; ?>

</div>

<?php if ($settings['lightbox'] === 'slideshow') : ?>
<div id="wk-3<?php echo $settings['id']; ?>" class="uk-modal">
    <div class="uk-modal-dialog uk-modal-dialog-blank">

        <button class="uk-modal-close uk-close" type="button"></button>

        <div class="uk-grid" data-uk-grid-margin>
            <div class="uk-width-medium-1-2 uk-text-center">

                <div class="uk-slidenav-position" data-uk-slideshow data-uk-check-display>
                    <ul class="uk-slideshow uk-slideshow-fullscreen">
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
                                $attrs['class'] = 'uk-responsive-width';
                                $attrs['controls'] = true;
                            }

                            if ($item->type($field) == 'iframe') {
                                $attrs['class'] = 'uk-responsive-width';
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

                    <a href="#" class="uk-slidenav <?php if ($settings['lightbox_nav_contrast']) echo 'uk-slidenav-contrast'; ?> uk-slidenav-previous uk-hidden-touch" data-uk-slideshow-item="previous"></a>
                    <a href="#" class="uk-slidenav <?php if ($settings['lightbox_nav_contrast']) echo 'uk-slidenav-contrast'; ?> uk-slidenav-next uk-hidden-touch" data-uk-slideshow-item="next"></a>

                </div>
            </div>
            <div class="uk-width-medium-1-2 uk-flex uk-flex-middle uk-flex-center">

                <div class="uk-panel-body uk-width-1-1 <?php echo $settings['lightbox_content_width'] ? 'uk-width-xlarge-' . $settings['lightbox_content_width'] : ''; ?>" data-uk-slideshow data-uk-check-display>
                    <ul class="uk-slideshow">
                        <?php foreach ($items as $item) : ?>
                        <li>

                            <?php if ($item['title']) : ?>
                            <h3 class="<?php echo $lightbox_title_size; ?>"><?php echo $item['title']; ?></h3>
                            <?php endif; ?>

                            <?php if ($item['lightbox_content']) : ?>
                            <div class="uk-margin-top <?php echo $lightbox_content_size; ?>"><?php echo $item['lightbox_content']; ?></div>
                            <?php elseif ($item['content']) : ?>
                            <div class="uk-margin-top <?php echo $lightbox_content_size; ?>"><?php echo $item['content']; ?></div>
                            <?php endif; ?>

                            <?php if ($item['link'] && $settings['link']) : ?>
                            <p class="uk-margin-bottom-remove"><a href="<?php echo $item->escape('link'); ?>"<?php echo $link_target; ?>><?php echo $app['translator']->trans($settings['link_text']); ?></a></p>
                            <?php endif; ?>

                        </li>
                    <?php endforeach; ?>
                    </ul>

                    <div class="uk-margin-top">
                        <ul class="uk-thumbnav uk-margin-bottom-remove">
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
                            <li data-uk-slideshow-item="<?php echo $i; ?>"><a href="#"><?php echo ($thumbnail) ? $thumbnail : $item['title']; ?></a></li>
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
            slideshows = modal.find('[data-uk-slideshow]'),
            slideshow;

        container.on('click', '[href^="#wk-"][data-uk-modal]', function(e) {
            slideshows.each(function(){

                slideshow = $(this).data('slideshow');
                slideshow.show(parseInt(e.target.getAttribute('data-index'), 10));
            });
        });

        modal.on('beforeshow.uk.slideshow', function(e, next) {
            slideshows.not(next.closest('[data-uk-slideshow]')[0]).data('slideshow').show(next.index());
        });

    })(jQuery);
</script>
<?php endif; ?>

<script>
(function($){

    // get the images of the gallery and replace it by a canvas of the same size to fix the problem with overlapping images on load.
    $('img[width][height]:not(.uk-overlay-panel)', $('#wk-grid<?php echo $settings['id']; ?>')).each(function() {

        var $img = $(this);

        if (this.width == 'auto' || this.height == 'auto' || !$img.is(':visible')) {
            return;
        }

        var $canvas = $('<canvas class="uk-responsive-width"></canvas>').attr({width:$img.attr('width'), height:$img.attr('height')}),
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
