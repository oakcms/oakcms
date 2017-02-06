<?php

// JS Options
$options = array();
$options[] = (!$settings['infinite']) ? 'infinite: false' : '';
$options[] = ($settings['center']) ? 'center: true' : '';
$options[] = ($settings['autoplay']) ? 'autoplay: true ' : '';
$options[] = ($settings['interval'] != '3000') ? 'autoplayInterval: ' . $settings['interval'] : '';
$options[] = ($settings['autoplay_pause']) ? '' : 'pauseOnHover: false';

$options = '{'.implode(',', array_filter($options)).'}';

// Grid
$grid  = '{wk}-grid {wk}-grid-match {wk}-flex-center {wk}-grid-width-1-'.$settings['columns'];
$grid .= $settings['columns_small'] ? ' {wk}-grid-width-small-1-'.$settings['columns_small'] : '';
$grid .= $settings['columns_medium'] ? ' {wk}-grid-width-medium-1-'.$settings['columns_medium'] : '';
$grid .= $settings['columns_large'] ? ' {wk}-grid-width-large-1-'.$settings['columns_large'] : '';
$grid .= $settings['columns_xlarge'] ? ' {wk}-grid-width-xlarge-1-'.$settings['columns_xlarge'] : '';

$grid .= in_array($settings['gutter'], array('collapse','large','medium','small')) ? ' {wk}-grid-'.$settings['gutter'] : '' ;

// Title Size
switch ($settings['title_size']) {
    case 'large':
        $title_size = '{wk}-heading-large {wk}-margin-top-remove';
        break;
    default:
        $title_size = '{wk}-' . $settings['title_size'] . ' {wk}-margin-top-remove';
}

// Content Size
switch ($settings['content_size']) {
    case 'large':
        $content_size = '{wk}-text-large';
        break;
    case 'h1':
    case 'h2':
    case 'h3':
    case 'h4':
    case 'h5':
    case 'h6':
        $content_size = '{wk}-' . $settings['content_size'];
        break;
    default:
        $content_size = '';
}

// Link Style
switch ($settings['link_style']) {
    case 'button':
        $link_style = '{wk}-button';
        break;
    case 'primary':
        $link_style = '{wk}-button {wk}-button-primary';
        break;
    case 'button-large':
        $link_style = '{wk}-button {wk}-button-large';
        break;
    case 'primary-large':
        $link_style = '{wk}-button {wk}-button-large {wk}-button-primary';
        break;
    case 'button-link':
        $link_style = '{wk}-button {wk}-button-link';
        break;
    default:
        $link_style = '';
}

// Link Target
$link_target = ($settings['link_target']) ? ' target="_blank"' : '';

// Position Relative
if ($settings['slidenav'] == 'default') {
    $position_relative = '{wk}-slidenav-position';
} else {
    $position_relative = '{wk}-position-relative';
}

// Overlays
$overlay_hover = ($settings['overlay_hover']) ? '{wk}-overlay-' . $settings['overlay_animation'] : '{wk}-ignore';
$background = ($settings['overlay_background'] == 'hover') ? '{wk}-overlay-' . $settings['overlay_animation'] : '{wk}-ignore';

?>

<div class="<?php echo $position_relative; ?> <?php echo $settings['class']; ?>" data-{wk}-slider="<?php echo $options; ?>">

    <div class="{wk}-slider-container">
        <ul class="{wk}-slider<?php if ($settings['fullscreen']) echo ' {wk}-slider-fullscreen'; ?> <?php echo $grid; ?>">
        <?php foreach ($items as $item) :

                // Media Type
                $width = $item['media.width'];
                $height = $item['media.height'];

                $media = '';

                if ($item->type('media') == 'image' && $settings['media']) {
                    if ($settings['image_width'] != 'auto' || $settings['image_height'] != 'auto') {
                        $width  = ($settings['image_width'] != 'auto') ? $settings['image_width'] : '';
                        $height = ($settings['image_height'] != 'auto') ? $settings['image_height'] : '';

                        $media = 'background-image: url(' . $item->thumbnail('media', $width, $height, array(), true) . ');';
                    }
                    elseif ($media = $item->get('media')) {

                        if ($img = $app['image']->create($media)) {
                            $media = 'background-image: url(' . $img->getURL() . ');';
                        }
                        else {
                            $media = 'background-image: url(' . $media . ');';
                        }

                    }
                }

                // `min-height` doesn't work in IE11 and IE10 if flex items are centered vertically
                // can't set `height` when fullscreen
                $min_height = (!$settings['fullscreen']) ? 'height: ' . $settings['min_height'] . 'px;' : '';

                if ($settings['overlay_image'] != 'hover') {
                    $media = 'style="' . $min_height . $media . '"';
                }

                // Second Image as Overlay
                $media2 = '';
                if ($settings['overlay_image'] == 'second') {
                    foreach ($item as $field) {
                        if ($field != 'media' && $item->type($field) == 'image') {
                            $media2 = 'style="background-image: url(' . $item->thumbnail($field, $width, $height, array(), true) . ');"';
                            break;
                        }
                    }
                }

                if ($settings['overlay_image'] == 'hover') {
                    $media2 = 'style="' . $media . '"';
                    $media  = 'style="' . $min_height . '"';
                }

            ?>

            <li>

                <div class="{wk}-panel {wk}-overlay {wk}-overlay-hover {wk}-cover-background" <?php echo $media; ?>>

                    <?php if ($media2) : ?>
                    <div class="{wk}-overlay-panel {wk}-cover-background <?php if ($settings['image_animation'] != 'none') echo '{wk}-overlay-' . $settings['image_animation']; ?>" <?php echo $media2; ?>></div>
                    <?php endif; ?>

                    <?php if ($settings['overlay_background'] != 'none') : ?>
                    <div class="{wk}-overlay-panel {wk}-overlay-background <?php echo $background; ?>"></div>
                    <?php endif; ?>

                    <div class="{wk}-overlay-panel <?php echo $overlay_hover; ?> {wk}-flex {wk}-flex-center {wk}-flex-middle {wk}-text-<?php echo $settings['text_align']; ?>">
                        <div>

                            <?php if ($item['title'] && $settings['title']) : ?>
                            <h3 class="<?php echo $title_size; ?> {wk}-margin-top-remove">

                                <?php if ($item['link']) : ?>
                                    <a class="{wk}-link-reset" href="<?php echo $item->escape('link') ?>"<?php echo $link_target; ?>><?php echo $item['title']; ?></a>
                                <?php else : ?>
                                    <?php echo $item['title']; ?>
                                <?php endif; ?>

                            </h3>
                            <?php endif; ?>

                            <?php if ($item['content'] && $settings['content']) : ?>
                            <div class="<?php echo $content_size; ?> {wk}-margin"><?php echo $item['content']; ?></div>
                            <?php endif; ?>

                            <?php if ($item['link'] && $settings['link']) : ?>
                            <p><a<?php if($link_style) echo ' class="' . $link_style . '"'; ?> href="<?php echo $item->escape('link'); ?>"<?php echo $link_target; ?>><?php echo $app['translator']->trans($settings['link_text']); ?></a></p>
                            <?php endif; ?>

                        </div>
                    </div>

                    <?php if ($item['link'] && $settings['overlay_link']) : ?>
                    <a class="{wk}-position-cover" href="<?php echo $item->escape('link'); ?>"<?php echo $link_target; ?>></a>
                    <?php endif; ?>

                </div>

            </li>

        <?php endforeach; ?>
        </ul>
    </div>

    <?php if (in_array($settings['slidenav'], array('top-left', 'top-right', 'bottom-left', 'bottom-right'))) : ?>
    <div class="{wk}-position-<?php echo $settings['slidenav']; ?> {wk}-margin {wk}-margin-left {wk}-margin-right">
        <div class="{wk}-grid {wk}-grid-small">
            <div><a href="#" class="{wk}-slidenav <?php if ($settings['slidenav_contrast']) echo '{wk}-slidenav-contrast'; ?> {wk}-slidenav-previous" data-{wk}-slider-item="previous"></a></div>
            <div><a href="#" class="{wk}-slidenav <?php if ($settings['slidenav_contrast']) echo '{wk}-slidenav-contrast'; ?> {wk}-slidenav-next" data-{wk}-slider-item="next"></a></div>
        </div>
    </div>
    <?php elseif ($settings['slidenav'] == 'default') : ?>
    <a href="#" class="{wk}-slidenav <?php if ($settings['slidenav_contrast']) echo '{wk}-slidenav-contrast'; ?> {wk}-slidenav-previous {wk}-hidden-touch" data-{wk}-slider-item="previous"></a>
    <a href="#" class="{wk}-slidenav <?php if ($settings['slidenav_contrast']) echo '{wk}-slidenav-contrast'; ?> {wk}-slidenav-next {wk}-hidden-touch" data-{wk}-slider-item="next"></a>
    <?php endif ?>

</div>
