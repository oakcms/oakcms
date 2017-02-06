<?php

// Id
$settings['id'] = substr(uniqid(), -3);

// JS Options
$options = array();
$options[] = ($settings['duration'] != '500') ? 'duration: ' . $settings['duration'] : '';
$options[] = ($settings['autoplay']) ? 'autoplay: true ' : '';
$options[] = ($settings['interval'] != '3000') ? 'autoplayInterval: ' . $settings['interval'] : '';
$options[] = ($settings['autoplay_pause']) ? '' : 'pauseOnHover: false';

$options = '{'.implode(',', array_filter($options)).'}';

// Panel
$panel = '{wk}-panel';
switch ($settings['panel']) {
    case 'box' :
        $panel .= ' {wk}-panel-box';
        break;
    case 'primary' :
        $panel .= ' {wk}-panel-box {wk}-panel-box-primary';
        break;
    case 'secondary' :
        $panel .= ' {wk}-panel-box {wk}-panel-box-secondary';
        break;
}

// Media Width
if ($settings['media_align'] == 'top') {

    $media_width = '{wk}-width-1-1';
    $content_width = '{wk}-width-1-1';

} else {

    $media_width = '{wk}-width-' . $settings['media_breakpoint'] . '-' . $settings['media_width'];

    switch ($settings['media_width']) {
        case '1-5':
            $content_width = '4-5';
            break;
        case '1-4':
            $content_width = '3-4';
            break;
        case '3-10':
            $content_width = '7-10';
            break;
        case '1-3':
            $content_width = '2-3';
            break;
        case '2-5':
            $content_width = '3-5';
            break;
        case '1-2':
            $content_width = '1-2';
            break;
        case '3-5':
            $content_width = '2-5';
            break;
        case '2-3':
            $content_width = '1-3';
            break;
    }

    $content_width = '{wk}-width-' . $settings['media_breakpoint'] . '-' . $content_width;

    // Align Right
    $media_width .= ($settings['media_align'] == 'right') ? ' {wk}-float-right {wk}-flex-order-last-' . $settings['media_breakpoint'] : '';

}

// Content Align
$content_align  = ($settings['content_align'] && $settings['media_align'] != 'top') ? '{wk}-flex-middle' : '';

// Title Size
switch ($settings['title_size']) {
    case 'panel':
        $title_size = '{wk}-panel-title';
        break;
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

// Custom Class
$class = $settings['class'] ? ' class="' . $settings['class'] . '"' : '';

?>

<div id="wk-<?php echo $settings['id']; ?>"<?php echo $class; ?>>
    <div class="{wk}-panel <?php echo $panel; ?> {wk}-padding-remove {wk}-overflow-hidden">

        <?php if ($settings['media']) : ?>
        <div class="{wk}-grid {wk}-grid-collapse <?php echo $content_align; ?>" data-{wk}-grid-margin>
            <div class="<?php echo $media_width ?> {wk}-text-center">
                <?php echo $this->render('plugins/widgets/' . $widget->getConfig('name')  . '/views/_media.php', compact('items', 'settings', 'widget')); ?>
            </div>
            <div class="<?php echo $content_width ?>">
        <?php endif; ?>

        <div class="{wk}-panel-body {wk}-text-<?php echo $settings['text_align']; ?>" data-{wk}-slideshow="<?php echo $options; ?>">
            <ul class="{wk}-slideshow">
                <?php foreach ($items as $item) : ?>

                <li>

                    <?php if ($item['title'] && $settings['title']) : ?>
                    <h3 class="<?php echo $title_size; ?>">

                        <?php if ($item['link']) : ?>
                            <a class="{wk}-link-reset" href="<?php echo $item->escape('link'); ?>" <?php echo $link_target; ?>><?php echo $item['title']; ?></a>
                        <?php else : ?>
                            <?php echo $item['title']; ?>
                        <?php endif; ?>

                        <?php if ($item['badge'] && $settings['badge']) : ?>
                        <span class="{wk}-margin-small-left <?php echo $badge_style; ?>"><?php echo $item['badge']; ?></span>
                        <?php endif; ?>

                    </h3>
                    <?php endif; ?>

                    <?php if ($item['content'] && $settings['content']) : ?>
                    <div class="<?php echo $content_size; ?> {wk}-margin-top"><?php echo $item['content']; ?></div>
                    <?php endif; ?>

                    <?php if ($item['link'] && $settings['link']) : ?>
                    <p class="{wk}-margin-bottom-remove"><a<?php if($link_style) echo ' class="' . $link_style . '"'; ?> href="<?php echo $item->escape('link'); ?>"<?php echo $link_target; ?>><?php echo $app['translator']->trans($settings['link_text']); ?></a></p>
                    <?php endif; ?>

                </li>

            <?php endforeach; ?>
            </ul>

            <?php if (!$settings['nav_overlay'] && ($settings['nav'] != 'none')) : ?>
            <div class="{wk}-margin-top">
                <?php echo $this->render('plugins/widgets/' . $widget->getConfig('name')  . '/views/_nav.php', compact('items', 'settings')); ?>
            </div>
            <?php endif ?>

        </div>

        <?php if ($settings['media']) : ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<script>

    (function($){

        var settings   = <?php echo json_encode($settings); ?>,
            container  = $('#wk-<?php echo $settings["id"]; ?>'),
            slideshows = container.find('[data-{wk}-slideshow]');

        if (slideshows.length > 1) {
            container.on('beforeshow.uk.slideshow', function(e, next) {
                slideshows.not(next.closest('[data-{wk}-slideshow]')[0]).data('slideshow').show(next.index());
            });
        }

        if (settings.autoplay && settings.autoplay_pause) {

            container.on({
                mouseenter: function() {
                    slideshows.each(function(){
                        $(this).data('slideshow').stop();
                    });
                },
                mouseleave: function() {
                    slideshows.each(function(){
                        $(this).data('slideshow').start();
                    });
                }
            });
        }

    })(jQuery);

</script>
