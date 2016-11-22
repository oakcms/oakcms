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
$grid  = 'uk-grid uk-grid-match uk-flex-center uk-grid-width-1-'.$settings['columns'];
$grid .= $settings['columns_small'] ? ' uk-grid-width-small-1-'.$settings['columns_small'] : '';
$grid .= $settings['columns_medium'] ? ' uk-grid-width-medium-1-'.$settings['columns_medium'] : '';
$grid .= $settings['columns_large'] ? ' uk-grid-width-large-1-'.$settings['columns_large'] : '';
$grid .= $settings['columns_xlarge'] ? ' uk-grid-width-xlarge-1-'.$settings['columns_xlarge'] : '';

$grid .= in_array($settings['gutter'], array('collapse','large','medium','small')) ? ' uk-grid-'.$settings['gutter'] : '' ;

// Title Size
switch ($settings['title_size']) {
    case 'large':
        $title_size = 'uk-heading-large uk-margin-top-remove';
        break;
    default:
        $title_size = 'uk-' . $settings['title_size'] . ' uk-margin-top-remove';
}

// Content Size
switch ($settings['content_size']) {
    case 'large':
        $content_size = 'uk-text-large';
        break;
    case 'h1':
    case 'h2':
    case 'h3':
    case 'h4':
    case 'h5':
    case 'h6':
        $content_size = 'uk-' . $settings['content_size'];
        break;
    default:
        $content_size = '';
}

// Link Style
switch ($settings['link_style']) {
    case 'button':
        $link_style = 'uk-button';
        break;
    case 'primary':
        $link_style = 'uk-button uk-button-primary';
        break;
    case 'button-large':
        $link_style = 'uk-button uk-button-large';
        break;
    case 'primary-large':
        $link_style = 'uk-button uk-button-large uk-button-primary';
        break;
    case 'button-link':
        $link_style = 'uk-button uk-button-link';
        break;
    default:
        $link_style = '';
}

// Link Target
$link_target = ($settings['link_target']) ? ' target="_blank"' : '';

// Position Relative
if ($settings['slidenav'] == 'default') {
    $position_relative = 'uk-slidenav-position';
} else {
    $position_relative = 'uk-position-relative';
}

// Overlays
$overlay_hover = ($settings['overlay_hover']) ? 'uk-overlay-' . $settings['overlay_animation'] : 'uk-ignore';
$background = ($settings['overlay_background'] == 'hover') ? 'uk-overlay-' . $settings['overlay_animation'] : 'uk-ignore';

?>

<div class="<?php echo $position_relative; ?> <?php echo $settings['class']; ?>" data-uk-slider="<?php echo $options; ?>">

    <div class="uk-slider-container">
        <ul class="uk-slider<?php if ($settings['fullscreen']) echo ' uk-slider-fullscreen'; ?> <?php echo $grid; ?>">
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

                <div class="uk-panel uk-overlay uk-overlay-hover uk-cover-background" <?php echo $media; ?>>

                    <?php if ($media2) : ?>
                    <div class="uk-overlay-panel uk-cover-background <?php if ($settings['image_animation'] != 'none') echo 'uk-overlay-' . $settings['image_animation']; ?>" <?php echo $media2; ?>></div>
                    <?php endif; ?>

                    <?php if ($settings['overlay_background'] != 'none') : ?>
                    <div class="uk-overlay-panel uk-overlay-background <?php echo $background; ?>"></div>
                    <?php endif; ?>

                    <div class="uk-overlay-panel <?php echo $overlay_hover; ?> uk-flex uk-flex-center uk-flex-middle uk-text-<?php echo $settings['text_align']; ?>">
                        <div>

                            <?php if ($item['title'] && $settings['title']) : ?>
                            <h3 class="<?php echo $title_size; ?> uk-margin-top-remove">

                                <?php if ($item['link']) : ?>
                                    <a class="uk-link-reset" href="<?php echo $item->escape('link') ?>"<?php echo $link_target; ?>><?php echo $item['title']; ?></a>
                                <?php else : ?>
                                    <?php echo $item['title']; ?>
                                <?php endif; ?>

                            </h3>
                            <?php endif; ?>

                            <?php if ($item['content'] && $settings['content']) : ?>
                            <div class="<?php echo $content_size; ?> uk-margin"><?php echo $item['content']; ?></div>
                            <?php endif; ?>

                            <?php if ($item['link'] && $settings['link']) : ?>
                            <p><a<?php if($link_style) echo ' class="' . $link_style . '"'; ?> href="<?php echo $item->escape('link'); ?>"<?php echo $link_target; ?>><?php echo $app['translator']->trans($settings['link_text']); ?></a></p>
                            <?php endif; ?>

                        </div>
                    </div>

                    <?php if ($item['link'] && $settings['overlay_link']) : ?>
                    <a class="uk-position-cover" href="<?php echo $item->escape('link'); ?>"<?php echo $link_target; ?>></a>
                    <?php endif; ?>

                </div>

            </li>

        <?php endforeach; ?>
        </ul>
    </div>

    <?php if (in_array($settings['slidenav'], array('top-left', 'top-right', 'bottom-left', 'bottom-right'))) : ?>
    <div class="uk-position-<?php echo $settings['slidenav']; ?> uk-margin uk-margin-left uk-margin-right">
        <div class="uk-grid uk-grid-small">
            <div><a href="#" class="uk-slidenav <?php if ($settings['slidenav_contrast']) echo 'uk-slidenav-contrast'; ?> uk-slidenav-previous" data-uk-slider-item="previous"></a></div>
            <div><a href="#" class="uk-slidenav <?php if ($settings['slidenav_contrast']) echo 'uk-slidenav-contrast'; ?> uk-slidenav-next" data-uk-slider-item="next"></a></div>
        </div>
    </div>
    <?php elseif ($settings['slidenav'] == 'default') : ?>
    <a href="#" class="uk-slidenav <?php if ($settings['slidenav_contrast']) echo 'uk-slidenav-contrast'; ?> uk-slidenav-previous uk-hidden-touch" data-uk-slider-item="previous"></a>
    <a href="#" class="uk-slidenav <?php if ($settings['slidenav_contrast']) echo 'uk-slidenav-contrast'; ?> uk-slidenav-next uk-hidden-touch" data-uk-slider-item="next"></a>
    <?php endif ?>

</div>
