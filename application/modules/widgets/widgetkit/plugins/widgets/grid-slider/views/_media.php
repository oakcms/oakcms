<?php

// Slideshow Options
$options = array();
$options[] = ($settings['slide_animation'] != 'fade') ? 'animation: \'' . $settings['slide_animation'] . '\'' : '';
$options[] = ($settings['duration'] != '500') ? 'duration: ' . $settings['duration']: '';
$options[] = ($settings['slices'] != '15') ? 'slices: ' . $settings['slices']: '';
$options[] = ($settings['autoplay']) ? 'autoplay: true ' : '';
$options[] = ($settings['interval'] != '3000') ? 'autoplayInterval: ' . $settings['interval'] : '';
$options[] = ($settings['kenburns']) ? 'kenburns: true' : '';
$options[] = ($settings['autoplay_pause']) ? '' : 'pauseOnHover: false';

$options = '{'.implode(',', array_filter($options)).'}';

// Count media fields
$fields = array();
foreach ($item as $field) {
    if (in_array($item->type($field), array('image', 'video', 'iframe'))) {
        $fields[] = $field;
    }
}

// Position Relative
if ($settings['slidenav'] == 'default') {
    $position_relative = 'uk-slidenav-position';
} else {
    $position_relative = 'uk-position-relative';
}

?>

<?php if (count($fields) > 1) : ?>
<div data-uk-slideshow="<?php echo $options; ?>">

    <div class="<?php echo $position_relative; ?>">

        <ul class="uk-slideshow">
        <?php foreach ($fields as $field) :

                // Media Type
                $attrs  = array();
                $width  = $item[$field . '.width'];
                $height = $item[$field . '.height'];

                if ($item->type($field) == 'image') {
                    $attrs['alt'] = strip_tags($item['title']);
                    $width  = ($settings['image_width'] != 'auto') ? $settings['image_width'] : '';
                    $height = ($settings['image_height'] != 'auto') ? $settings['image_height'] : '';
                }

                if ($item->type($field) == 'video') {
                    $attrs['autoplay'] = true;
                    $attrs['loop']     = true;
                    $attrs['muted']    = true;
                    $attrs['class']    = 'uk-cover-object uk-position-absolute';
                }

                $attrs['width']  = ($width) ? $width : '';
                $attrs['height'] = ($height) ? $height : '';

                if (($item->type($field) == 'image') && ($settings['image_width'] != 'auto' || $settings['image_height'] != 'auto')) {
                    $media = $item->thumbnail($field, $width, $height, $attrs);
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
                    $media = $item->media($field, $attrs);
                }

            ?>

            <li><?php echo $media; ?></li>

        <?php endforeach; ?>
        </ul>

        <?php if ($item['link'] && $settings['link']) : ?>
        <a class="uk-position-cover" href="<?php echo $item->escape('link'); ?>"<?php echo $link_target; ?>></a>
        <?php endif ?>

        <?php if (in_array($settings['slidenav'], array('top-left', 'top-right', 'bottom-left', 'bottom-right'))) : ?>
        <div class="uk-position-<?php echo $settings['slidenav']; ?> uk-margin uk-margin-left uk-margin-right">
            <div class="uk-grid uk-grid-small">
                <div><a href="#" class="uk-slidenav <?php if ($settings['nav_contrast']) echo 'uk-slidenav-contrast'; ?> uk-slidenav-previous" data-uk-slideshow-item="previous"></a></div>
                <div><a href="#" class="uk-slidenav <?php if ($settings['nav_contrast']) echo 'uk-slidenav-contrast'; ?> uk-slidenav-next" data-uk-slideshow-item="next"></a></div>
            </div>
        </div>
        <?php elseif ($settings['slidenav'] == 'default') : ?>
        <a href="#" class="uk-slidenav <?php if ($settings['nav_contrast']) echo 'uk-slidenav-contrast'; ?> uk-slidenav-previous uk-hidden-touch" data-uk-slideshow-item="previous"></a>
        <a href="#" class="uk-slidenav <?php if ($settings['nav_contrast']) echo 'uk-slidenav-contrast'; ?> uk-slidenav-next uk-hidden-touch" data-uk-slideshow-item="next"></a>
        <?php endif ?>

        <?php if ($settings['nav_overlay']) : ?>
        <div class="uk-overlay-panel uk-overlay-bottom">
            <?php echo $this->render('plugins/widgets/' . $widget->getConfig('name')  . '/views/_nav.php', compact('item', 'fields', 'settings')); ?>
        </div>
        <?php endif ?>

    </div>

    <?php if (!$settings['nav_overlay']) : ?>
    <div class="uk-margin">
        <?php echo $this->render('plugins/widgets/' . $widget->getConfig('name')  . '/views/_nav.php', compact('item', 'fields', 'settings')); ?>
    </div>
    <?php endif ?>

</div>

<?php elseif (count($fields) == 1) :

    $field = $fields[0];

    // Media Type
    $attrs  = array();
    $width  = $item[$field . '.width'];
    $height = $item[$field . '.height'];

    if ($item->type($field) == 'image') {
        $attrs['alt'] = strip_tags($item['title']);
        $width  = ($settings['image_width'] != 'auto') ? $settings['image_width'] : $width;
        $height = ($settings['image_height'] != 'auto') ? $settings['image_height'] : $height;
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

    if (($item->type($field) == 'image') && ($settings['image_width'] != 'auto' || $settings['image_height'] != 'auto')) {
        $media = $item->thumbnail($field, $width, $height, $attrs);
    } else {
        $media = $item->media($field, $attrs);
    }

    echo $media;

endif; ?>
