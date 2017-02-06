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
$overlay = '{wk}-overlay-panel';
switch ($settings['overlay']) {
    case 'center':
        $overlay .= ' {wk}-flex {wk}-flex-center {wk}-flex-middle {wk}-text-center';
        break;
    case 'middle-left':
        $overlay .= ' {wk}-flex {wk}-flex-middle';
        break;
    default:
        $overlay .= ' {wk}-overlay-' . $settings['overlay'];
}

$overlay .= $settings['overlay_background'] ? ' {wk}-overlay-background' : '';

if ($settings['overlay_animation'] == 'slide' && !in_array($settings['overlay'], array('center', 'middle-left'))) {
    $overlay .= ' {wk}-overlay-slide-' . $settings['overlay'];
} else {
    $overlay .= ' {wk}-overlay-fade';
}

// Title Size
switch ($settings['title_size']) {
    case 'large':
        $title_size = '{wk}-heading-large';
        break;
    default:
        $title_size = '{wk}-' . $settings['title_size'];
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

// Badge Style
switch ($settings['badge_style']) {
    case 'badge':
        $badge_style = '{wk}-badge';
        break;
    case 'success':
        $badge_style = '{wk}-badge {wk}-badge-success';
        break;
    case 'warning':
        $badge_style = '{wk}-badge {wk}-badge-warning';
        break;
    case 'danger':
        $badge_style = '{wk}-badge {wk}-badge-danger';
        break;
    case 'text-muted':
        $badge_style  = '{wk}-text-muted';
        break;
    case 'text-primary':
        $badge_style  = '{wk}-text-primary';
        break;
}

// Position Relative
if ($settings['slidenav'] == 'default') {
    $position_relative = '{wk}-slidenav-position';
} else {
    $position_relative = '{wk}-position-relative';
}

// Custom Class
$class = $settings['class'] ? ' class="' . $settings['class'] . '"' : '';

?>

<div<?php echo $class; ?> data-{wk}-slideshow="<?php echo $options; ?>">

    <div class="<?php echo $position_relative; ?>">

        <ul class="{wk}-slideshow<?php if ($settings['fullscreen']) echo ' {wk}-slideshow-fullscreen'; ?><?php if ($settings['overlay'] != 'none') echo ' {wk}-overlay-active'; ?>">
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
                    $attrs['class']   .= '{wk}-cover-object {wk}-position-absolute';
                    $attrs['class']   .= ($item['media.poster']) ? ' {wk}-hidden-touch' : '';
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
                    <div class="{wk}-cover-background {wk}-position-cover {wk}-hidden-notouch" style="background-image: url(<?php echo $item['media.poster'] ?>);"></div>
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
                            <span class="{wk}-margin-small-left <?php echo $badge_style; ?>"><?php echo $item['badge']; ?></span>
                            <?php endif; ?>

                        </h3>
                        <?php endif; ?>

                        <?php if ($item['content'] && $settings['content']) : ?>
                        <div class="<?php echo $content_size; ?> {wk}-margin"><?php echo $item['content']; ?></div>
                        <?php endif; ?>

                        <?php if ($item['link'] && $settings['link']) : ?>
                        <p><a<?php if($link_style) echo ' class="' . $link_style . '"'; ?> href="<?php echo $item->escape('link'); ?>"<?php echo $link_target; ?>><?php echo $app['translator']->trans($settings['link_text']); ?></a></p>
                        <?php endif; ?>

                        <?php if (in_array($settings['overlay'], array('center', 'middle-left'))) : ?>
                        </div>
                        <?php endif; ?>

                    </div>
                    <?php endif; ?>

                    <?php if ($item['link'] && $settings['link_media']) : ?>
                    <a href="<?php echo $item->escape('link'); ?>" class="{wk}-position-cover" <?php echo $link_target; ?>></a>
                    <?php endif; ?>

                <?php elseif(($item['title'] && $settings['title']) || ($item['content'] && $settings['content'])) : ?>

                    <?php if ($item['title'] && $settings['title']) : ?>
                    <h3 class="<?php echo $title_size; ?>">

                        <?php echo $item['title']; ?>

                        <?php if ($item['badge'] && $settings['badge']) : ?>
                        <span class="{wk}-margin-small-left <?php echo $badge_style; ?>"><?php echo $item['badge']; ?></span>
                        <?php endif; ?>

                    </h3>
                    <?php endif; ?>

                    <?php if ($item['content'] && $settings['content']) : ?>
                    <div class="{wk}-margin"><?php echo $item['content']; ?></div>
                    <?php endif; ?>

                    <?php if ($item['link'] && $settings['link']) : ?>
                    <p><a<?php if($link_style) echo ' class="' . $link_style . '"'; ?> href="<?php echo $item->escape('link'); ?>"<?php echo $link_target; ?>><?php echo $app['translator']->trans($settings['link_text']); ?></a></p>
                    <?php endif; ?>

                <?php endif; ?>

            </li>

        <?php endforeach; ?>
        </ul>

        <?php if (in_array($settings['slidenav'], array('top-left', 'top-right', 'bottom-left', 'bottom-right'))) : ?>
        <div class="{wk}-position-<?php echo $settings['slidenav']; ?> {wk}-margin {wk}-margin-left {wk}-margin-right">
            <div class="{wk}-grid {wk}-grid-small">
                <div><a href="#" class="{wk}-slidenav <?php if ($settings['nav_contrast']) echo '{wk}-slidenav-contrast'; ?> {wk}-slidenav-previous" data-{wk}-slideshow-item="previous"></a></div>
                <div><a href="#" class="{wk}-slidenav <?php if ($settings['nav_contrast']) echo '{wk}-slidenav-contrast'; ?> {wk}-slidenav-next" data-{wk}-slideshow-item="next"></a></div>
            </div>
        </div>
        <?php elseif ($settings['slidenav'] == 'default') : ?>
        <a href="#" class="{wk}-slidenav <?php if ($settings['nav_contrast']) echo '{wk}-slidenav-contrast'; ?> {wk}-slidenav-previous {wk}-hidden-touch" data-{wk}-slideshow-item="previous"></a>
        <a href="#" class="{wk}-slidenav <?php if ($settings['nav_contrast']) echo '{wk}-slidenav-contrast'; ?> {wk}-slidenav-next {wk}-hidden-touch" data-{wk}-slideshow-item="next"></a>
        <?php endif ?>

        <?php if ($settings['nav_overlay'] && ($settings['nav'] != 'none')) : ?>
        <div class="{wk}-overlay-panel {wk}-overlay-bottom">
            <?php echo $this->render('plugins/widgets/' . $widget->getConfig('name')  . '/views/_nav.php', compact('items', 'settings')); ?>
        </div>
        <?php endif ?>

    </div>

    <?php if (!$settings['nav_overlay'] && ($settings['nav'] != 'none')) : ?>
    <div class="{wk}-margin">
        <?php echo $this->render('plugins/widgets/' . $widget->getConfig('name')  . '/views/_nav.php', compact('items', 'settings')); ?>
    </div>
    <?php endif ?>

</div>
