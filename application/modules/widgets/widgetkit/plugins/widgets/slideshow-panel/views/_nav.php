<?php

// Nav
$nav = '';
$nav_item = '';

switch ($settings['nav']) {
    case 'dotnav':
        $nav  = 'uk-dotnav';
        $nav .= ($settings['nav_overlay'] && $settings['nav_contrast']) ? ' uk-dotnav-contrast' : '';
        break;
    case 'thumbnails':
        $nav = 'uk-thumbnav';
        $nav_item = ($settings['nav_align'] == 'justify') ? ' class="uk-width-1-' . count($items) . '"' : '';
        break;
}

// Alignment
$nav .= ($settings['nav_align'] != 'justify') ? ' uk-flex-' . $settings['nav_align'] : '';

?>

<ul class="<?php echo $nav; ?> uk-margin-bottom-remove">
<?php foreach ($items as $i => $item) :

        // Alternative Media Field
        $field = 'media';
        if ($settings['thumbnail_alt']) {
            foreach ($item as $media_field) {
                if (($item[$media_field] != $item['media']) && ($item->type($media_field) == 'image')) {
                    $field = $media_field;
                    break;
                }
            }
        }

        // Thumbnails
        $thumbnail = '';
        if ($settings['nav'] == 'thumbnails' &&  ($item->type($field) == 'image' || $item[$field . '.poster'])) {

            $attrs           = array();
            $width           = ($settings['thumbnail_width'] != 'auto') ? $settings['thumbnail_width'] : $item[$field . '.width'];
            $height          = ($settings['thumbnail_height'] != 'auto') ? $settings['thumbnail_height'] : $item[$field . '.height'];

            $attrs['alt']    = strip_tags($item['title']);
            $attrs['width']  = $width;
            $attrs['height'] = $height;

            if ($settings['thumbnail_width'] != 'auto' || $settings['thumbnail_height'] != 'auto') {
                $thumbnail = $item->thumbnail($item->type($field) == 'image' ? $field : $field . '.poster', $width, $height, $attrs);
            } else {
                $thumbnail = $item->media($item->type($field) == 'image' ? $field : $field . '.poster', $attrs);
            }
        }

    ?>
    <li<?php echo $nav_item; ?> data-uk-slideshow-item="<?php echo $i; ?>"><a href="#"><?php echo ($thumbnail) ? $thumbnail : $item['title']; ?></a></li>
<?php endforeach; ?>
</ul>