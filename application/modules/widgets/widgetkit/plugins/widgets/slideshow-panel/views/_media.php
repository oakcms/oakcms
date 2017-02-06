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

// Slidenav Position Relative
if ($settings['slidenav'] == 'default') {
    $position_relative = '{wk}-slidenav-position';
} else {
    $position_relative = '{wk}-position-relative';
}

// Nav
$nav = '{wk}-position-bottom {wk}-margin-bottom';

switch ($settings['nav_align']) {
    case 'left':
        $nav .= ' {wk}-margin-left';
        break;
    case 'right':
        $nav .= ' {wk}-margin-right';
        break;
    case 'center':
    case 'justify':
        $nav .= ' {wk}-margin-left {wk}-margin-right';
        break;
}

?>

<div class="<?php echo $position_relative; ?>" data-{wk}-slideshow="<?php echo $options; ?>">

    <ul class="{wk}-slideshow<?php if ($settings['fullscreen']) echo ' {wk}-slideshow-fullscreen'; ?>">
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
                    $attrs['class'] = '{wk}-responsive-width';
                    $attrs['controls'] = true;
                }

                if ($item->type('media') == 'iframe') {
                    $attrs['class'] = '{wk}-responsive-width';
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
                <?php echo $media; ?>
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
    <div class="<?php echo $nav; ?>">
        <?php echo $this->render('plugins/widgets/' . $widget->getConfig('name')  . '/views/_nav.php', compact('items', 'settings')); ?>
    </div>
    <?php endif ?>

</div>
