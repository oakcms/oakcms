<?php

// JS Options
$options = array();
$options[] = ($settings['animation'] != 'fade') ? 'animation: \'' . $settings['animation'] . '\'' : '';
$options[] = ($settings['duration'] != '500') ? 'duration: ' . $settings['duration'] : '';
$options[] = ($settings['slices'] != '15') ? 'slices: ' . $settings['slices'] : '';
$options[] = ($settings['autoplay']) ? 'autoplay: true ' : '';
$options[] = ($settings['interval'] != '3000') ? 'autoplayInterval: ' . $settings['interval'] : '';
$options[] = ($settings['autoplay_pause']) ? '' : 'pauseOnHover: false';
if ($settings['kenburns'] && $settings['kenburns_duration']) {
    $kenburns_animation = ($settings['kenburns_animation']) ? ', kenburnsanimations: \'' . $settings['kenburns_animation'] . '\'' : '';
    $options[] = 'kenburns: \'' . $settings['kenburns_duration'] . 's\'' . $kenburns_animation;
}

$options = '{'.implode(',', array_filter($options)).'}';

// Overlay
$overlay = 'uk-overlay-panel';
switch ($settings['overlay']) {
    case 'center':
        $overlay .= ' uk-flex uk-flex-center uk-flex-middle uk-text-center';
        break;
    case 'middle-left':
        $overlay .= ' uk-flex uk-flex-middle';
        break;
    default:
        $overlay .= ' uk-overlay-' . $settings['overlay'];
}

$overlay .= $settings['overlay_background'] ? ' uk-overlay-background' : '';

if ($settings['overlay_animation'] == 'slide' && !in_array($settings['overlay'], array('center', 'middle-left'))) {
    $overlay .= ' uk-overlay-slide-' . $settings['overlay'];
} else {
    $overlay .= ' uk-overlay-fade';
}

// Title Size
switch ($settings['title_size']) {
    case 'large':
        $title_size = 'uk-heading-large';
        break;
    default:
        $title_size = 'uk-' . $settings['title_size'];
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

// Badge Style
switch ($settings['badge_style']) {
    case 'badge':
        $badge_style = 'uk-badge';
        break;
    case 'success':
        $badge_style = 'uk-badge uk-badge-success';
        break;
    case 'warning':
        $badge_style = 'uk-badge uk-badge-warning';
        break;
    case 'danger':
        $badge_style = 'uk-badge uk-badge-danger';
        break;
    case 'text-muted':
        $badge_style  = 'uk-text-muted';
        break;
    case 'text-primary':
        $badge_style  = 'uk-text-primary';
        break;
}

// Position Relative
if ($settings['slidenav'] == 'default') {
    $position_relative = 'uk-slidenav-position';
} else {
    $position_relative = 'uk-position-relative';
}

// Custom Class
$class = $settings['class'] ? ' class="' . $settings['class'] . '"' : '';

?>

<div<?php echo $class; ?> data-uk-slideshow="<?php echo $options; ?>">

    <div class="<?php echo $position_relative; ?>">

        <ul class="uk-slideshow<?php if ($settings['fullscreen']) echo ' uk-slideshow-fullscreen'; ?><?php if ($settings['overlay'] != 'none') echo ' uk-overlay-active'; ?>">
        <?php foreach ($items as $item) :

                // Media Type
                $attrs  = array('class' => '');
                $width  = $item['media.width'];
                $height = $item['media.height'];

                if ($item->type('media') == 'image') {
                    $attrs['alt'] = strip_tags($item['title']);
                    $width  = ($settings['image_width'] != 'auto') ? $settings['image_width'] : '';
                    $height = ($settings['image_height'] != 'auto') ? $settings['image_height'] : '';
                }

                if ($item->type('media') == 'video') {
                    $attrs['autoplay'] = true;
                    $attrs['loop']     = true;
                    $attrs['muted']    = true;
                    $attrs['class']   .= 'uk-cover-object uk-position-absolute';
                    $attrs['class']   .= ($item['media.poster']) ? ' uk-hidden-touch' : '';
                }

                $attrs['width']  = ($width) ? $width : '';
                $attrs['height'] = ($height) ? $height : '';

                if (($item->type('media') == 'image') && ($settings['image_width'] != 'auto' || $settings['image_height'] != 'auto')) {
                    $media = $item->thumbnail('media', $width, $height, $attrs);
                } else {
                    $media = $item->media('media', $attrs);
                }

            ?>

            <li style="min-height: <?php echo $settings['min_height']; ?>px;">

                <?php if ($item['media'] && $settings['media']) : ?>

                    <?php echo $media; ?>

                    <?php if ($item['media.poster']) : ?>
                    <div class="uk-cover-background uk-position-cover uk-hidden-notouch" style="background-image: url(<?php echo $item['media.poster'] ?>);"></div>
                    <?php endif ?>

                    <?php if ($settings['overlay'] != 'none' && (($item['title'] && $settings['title']) || ($item['content'] && $settings['content']) || ($item['link'] && $settings['link']))) : ?>
                    <div class="<?php echo $overlay; ?>">

                        <?php if (in_array($settings['overlay'], array('center', 'middle-left'))) : ?>
                        <div>
                        <?php endif; ?>

                        <?php if ($item['title'] && $settings['title']) : ?>
                        <h3 class="<?php echo $title_size; ?>">

                            <?php echo $item['title']; ?>

                            <?php if ($item['badge'] && $settings['badge']) : ?>
                            <span class="uk-margin-small-left <?php echo $badge_style; ?>"><?php echo $item['badge']; ?></span>
                            <?php endif; ?>

                        </h3>
                        <?php endif; ?>

                        <?php if ($item['content'] && $settings['content']) : ?>
                        <div class="<?php echo $content_size; ?> uk-margin"><?php echo $item['content']; ?></div>
                        <?php endif; ?>

                        <?php if ($item['link'] && $settings['link']) : ?>
                        <p><a<?php if($link_style) echo ' class="' . $link_style . '"'; ?> href="<?php echo $item->escape('link'); ?>"<?php echo $link_target; ?>><?php echo $app['translator']->trans($settings['link_text']); ?></a></p>
                        <?php endif; ?>

                        <?php if (in_array($settings['overlay'], array('center', 'middle-left'))) : ?>
                        </div>
                        <?php endif; ?>

                    </div>
                    <?php endif; ?>

                <?php elseif(($item['title'] && $settings['title']) || ($item['content'] && $settings['content'])) : ?>

                    <?php if ($item['title'] && $settings['title']) : ?>
                    <h3 class="<?php echo $title_size; ?>">

                        <?php echo $item['title']; ?>

                        <?php if ($item['badge'] && $settings['badge']) : ?>
                        <span class="uk-margin-small-left <?php echo $badge_style; ?>"><?php echo $item['badge']; ?></span>
                        <?php endif; ?>

                    </h3>
                    <?php endif; ?>

                    <?php if ($item['content'] && $settings['content']) : ?>
                    <div class="uk-margin"><?php echo $item['content']; ?></div>
                    <?php endif; ?>

                    <?php if ($item['link'] && $settings['link']) : ?>
                    <p><a<?php if($link_style) echo ' class="' . $link_style . '"'; ?> href="<?php echo $item->escape('link'); ?>"<?php echo $link_target; ?>><?php echo $app['translator']->trans($settings['link_text']); ?></a></p>
                    <?php endif; ?>

                <?php endif; ?>

            </li>

        <?php endforeach; ?>
        </ul>

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

        <?php if ($settings['nav_overlay'] && ($settings['nav'] != 'none')) : ?>
        <div class="uk-overlay-panel uk-overlay-bottom">
            <?php echo $this->render('plugins/widgets/' . $widget->getConfig('name')  . '/views/_nav.php', compact('items', 'settings')); ?>
        </div>
        <?php endif ?>

    </div>

    <?php if (!$settings['nav_overlay'] && ($settings['nav'] != 'none')) : ?>
    <div class="uk-margin">
        <?php echo $this->render('plugins/widgets/' . $widget->getConfig('name')  . '/views/_nav.php', compact('items', 'settings')); ?>
    </div>
    <?php endif ?>

</div>
