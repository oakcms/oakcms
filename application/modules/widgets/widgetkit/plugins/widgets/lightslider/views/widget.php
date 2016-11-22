<?php

\app\templates\frontend\base\assets\LightSliderAsset::register(Yii::$app->view);

$map_id  = uniqid('lightSlider');
$js = "
    gallery: true,
    item: 1,
    loop: true,
    slideMargin: 0,
    thumbItem: 3
});";

// JS Options
$options = array();
$options[] = 'speed: ' . $settings['duration'];
$options[] = 'item: 1';
$options[] = 'thumbItem:6';
$options[] = 'thumbMargin:0';
$options[] = 'loop: true';
$options[] = $settings['autoplay']?'auto:true':'auto:false';
$options[] = $settings['gallery']?'gallery:true':'gallery:false';
$options[] = 'pause: ' . $settings['interval'];

$options = '$(\'#'.$map_id.'\').lightSlider({'.implode(',', array_filter($options)).'});';

Yii::$app->view->registerJs($options, \yii\web\View::POS_END, 'lightslider');

// Overlay
$overlay = 'overlay-panel';
switch ($settings['overlay']) {
    case 'center':
        $overlay .= ' flex flex-center flex-middle text-center';
        break;
    case 'middle-left':
        $overlay .= ' flex flex-middle';
        break;
    default:
        $overlay .= ' overlay-' . $settings['overlay'];
}

$overlay .= $settings['overlay_background'] ? ' overlay-background' : '';

if ($settings['overlay_animation'] == 'slide' && !in_array($settings['overlay'], array('center', 'middle-left'))) {
    $overlay .= ' overlay-slide-' . $settings['overlay'];
} else {
    $overlay .= ' overlay-fade';
}

// Title Size
switch ($settings['title_size']) {
    case 'large':
        $title_size = 'heading-large';
        break;
    default:
        $title_size = '' . $settings['title_size'];
}

// Content Size
switch ($settings['content_size']) {
    case 'large':
        $content_size = 'text-large';
        break;
    case 'h1':
    case 'h2':
    case 'h3':
    case 'h4':
    case 'h5':
    case 'h6':
        $content_size = '' . $settings['content_size'];
        break;
    default:
        $content_size = '';
}

// Link Style
switch ($settings['link_style']) {
    case 'button':
        $link_style = 'btn btn-default';
        break;
    case 'primary':
        $link_style = 'btn btn-primary';
        break;
    case 'button-large':
        $link_style = 'btn btn-default btn-lg';
        break;
    case 'primary-large':
        $link_style = 'btn btn-primary btn-lg';
        break;
    case 'button-link':
        $link_style = 'btn btn-link';
        break;
    default:
        $link_style = '';
}

// Link Target
$link_target = ($settings['link_target']) ? ' target="_blank"' : '';

// Badge Style
switch ($settings['badge_style']) {
    case 'badge':
        $badge_style = 'badge';
        break;
    case 'success':
        $badge_style = 'badge badge-success';
        break;
    case 'warning':
        $badge_style = 'badge badge-warning';
        break;
    case 'danger':
        $badge_style = 'badge badge-danger';
        break;
    case 'text-muted':
        $badge_style  = 'text-muted';
        break;
    case 'text-primary':
        $badge_style  = 'text-primary';
        break;
}

// Position Relative
if ($settings['slidenav'] == 'default') {
    $position_relative = 'slidenav-position';
} else {
    $position_relative = 'position-relative';
}

// Custom Class
$class = $settings['class'] ? ' class="' . $settings['class'] . '"' : '';

?>

<div id="<?= $map_id ?>" <?php echo $class; ?>>
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
        $attrs['class']   .= 'cover-object position-absolute';
        $attrs['class']   .= ($item['media.poster']) ? ' hidden-touch' : '';
    }

    $attrs['width']  = ($width) ? $width : '';
    $attrs['height'] = ($height) ? $height : '';

    if (($item->type('media') == 'image') && ($settings['image_width'] != 'auto' || $settings['image_height'] != 'auto')) {
        $media = $item->thumbnail('media', $width, $height, $attrs);
    } else {
        $media = $item->media('media', $attrs);
    }
    ?>

        <?php if ($item['media'] && $settings['media']):?>
            <div class="lightSlider__item" data-thumb="<?= $item['media'] ?>" style="background-image: url(<?= $item['media'] ?>);">
                <div class="content__item">
                    <?php if ($item['title'] && $settings['title']) : ?>
                        <h3 class="<?php echo $title_size; ?>">

                            <?php echo $item['title']; ?>

                            <?php if ($item['badge'] && $settings['badge']) : ?>
                                <span class="margin-small-left <?php echo $badge_style; ?>"><?php echo $item['badge']; ?></span>
                            <?php endif; ?>
                        </h3>
                    <?php endif; ?>
                    <?php if ($item['content'] && $settings['content']) : ?>
                        <div class="margin description <?php echo $content_size; ?>"><?php echo $item['content']; ?></div>
                    <?php endif; ?>

                    <?php if ($item['link'] && $settings['link']) : ?>
                        <p><a<?php if($link_style) echo ' class="' . $link_style . '"'; ?> href="<?php echo $item->escape('link'); ?>"<?php echo $link_target; ?>><?php echo $app['translator']->trans($settings['link_text']); ?></a></p>
                    <?php endif; ?>
                </div>
            </div>

        <?elseif(($item['title'] && $settings['title']) || ($item['content'] && $settings['content'])):?>

            <?php if ($item['title'] && $settings['title']) : ?>
                <h3 class="<?php echo $title_size; ?>">
                    <?php echo $item['title']; ?>
                </h3>
            <?php endif; ?>

            <?php if ($item['content'] && $settings['content']) : ?>
            <div class="margin"><?php echo $item['content']; ?></div>
            <?php endif; ?>

            <?php if ($item['link'] && $settings['link']) : ?>
            <p><a<?php if($link_style) echo ' class="' . $link_style . '"'; ?> href="<?php echo $item->escape('link'); ?>"<?php echo $link_target; ?>><?php echo $app['translator']->trans($settings['link_text']); ?></a></p>
            <?php endif; ?>

        <?endif;?>
    <?endforeach;?>
</div>
<!--
<div<?php /*echo $class; */?> data-slideshow="<?php /*echo $options; */?>">

    <div class="<?php /*echo $position_relative; */?>">

        <ul class="slideshow<?php /*if ($settings['fullscreen']) echo ' slideshow-fullscreen'; */?><?php /*if ($settings['overlay'] != 'none') echo ' overlay-active'; */?>">
        <?php /*foreach ($items as $item) :

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
                    $attrs['class']   .= 'cover-object position-absolute';
                    $attrs['class']   .= ($item['media.poster']) ? ' hidden-touch' : '';
                }

                $attrs['width']  = ($width) ? $width : '';
                $attrs['height'] = ($height) ? $height : '';

                if (($item->type('media') == 'image') && ($settings['image_width'] != 'auto' || $settings['image_height'] != 'auto')) {
                    $media = $item->thumbnail('media', $width, $height, $attrs);
                } else {
                    $media = $item->media('media', $attrs);
                }

            */?>

            <li style="min-height: <?php /*echo $settings['min_height']; */?>px;">

                <?php /*if ($item['media'] && $settings['media']) : */?>

                    <?php /*echo $media; */?>

                    <?php /*if ($item['media.poster']) : */?>
                    <div class="cover-background position-cover hidden-notouch" style="background-image: url(<?php /*echo $item['media.poster'] */?>);"></div>
                    <?php /*endif */?>

                    <?php /*if ($settings['overlay'] != 'none' && (($item['title'] && $settings['title']) || ($item['content'] && $settings['content']) || ($item['link'] && $settings['link']))) : */?>
                    <div class="<?php /*echo $overlay; */?>">

                        <?php /*if (in_array($settings['overlay'], array('center', 'middle-left'))) : */?>
                        <div>
                        <?php /*endif; */?>

                        <?php /*if ($item['title'] && $settings['title']) : */?>
                        <h3 class="<?php /*echo $title_size; */?>">

                            <?php /*echo $item['title']; */?>

                            <?php /*if ($item['badge'] && $settings['badge']) : */?>
                            <span class="margin-small-left <?php /*echo $badge_style; */?>"><?php /*echo $item['badge']; */?></span>
                            <?php /*endif; */?>

                        </h3>
                        <?php /*endif; */?>

                        <?php /*if ($item['content'] && $settings['content']) : */?>
                        <div class="<?php /*echo $content_size; */?> margin"><?php /*echo $item['content']; */?></div>
                        <?php /*endif; */?>

                        <?php /*if ($item['link'] && $settings['link']) : */?>
                        <p><a<?php /*if($link_style) echo ' class="' . $link_style . '"'; */?> href="<?php /*echo $item->escape('link'); */?>"<?php /*echo $link_target; */?>><?php /*echo $app['translator']->trans($settings['link_text']); */?></a></p>
                        <?php /*endif; */?>

                        <?php /*if (in_array($settings['overlay'], array('center', 'middle-left'))) : */?>
                        </div>
                        <?php /*endif; */?>

                    </div>
                    <?php /*endif; */?>

                <?php /*elseif(($item['title'] && $settings['title']) || ($item['content'] && $settings['content'])) : */?>

                    <?php /*if ($item['title'] && $settings['title']) : */?>
                    <h3 class="<?php /*echo $title_size; */?>">

                        <?php /*echo $item['title']; */?>

                        <?php /*if ($item['badge'] && $settings['badge']) : */?>
                        <span class="margin-small-left <?php /*echo $badge_style; */?>"><?php /*echo $item['badge']; */?></span>
                        <?php /*endif; */?>

                    </h3>
                    <?php /*endif; */?>

                    <?php /*if ($item['content'] && $settings['content']) : */?>
                    <div class="margin"><?php /*echo $item['content']; */?></div>
                    <?php /*endif; */?>

                    <?php /*if ($item['link'] && $settings['link']) : */?>
                    <p><a<?php /*if($link_style) echo ' class="' . $link_style . '"'; */?> href="<?php /*echo $item->escape('link'); */?>"<?php /*echo $link_target; */?>><?php /*echo $app['translator']->trans($settings['link_text']); */?></a></p>
                    <?php /*endif; */?>

                <?php /*endif; */?>

            </li>

        <?php /*endforeach; */?>
        </ul>

        <?php /*if (in_array($settings['slidenav'], array('top-left', 'top-right', 'bottom-left', 'bottom-right'))) : */?>
        <div class="position-<?php /*echo $settings['slidenav']; */?> margin margin-left margin-right">
            <div class="grid grid-small">
                <div><a href="#" class="slidenav <?php /*if ($settings['nav_contrast']) echo 'slidenav-contrast'; */?> slidenav-previous" data-slideshow-item="previous"></a></div>
                <div><a href="#" class="slidenav <?php /*if ($settings['nav_contrast']) echo 'slidenav-contrast'; */?> slidenav-next" data-slideshow-item="next"></a></div>
            </div>
        </div>
        <?php /*elseif ($settings['slidenav'] == 'default') : */?>
        <a href="#" class="slidenav <?php /*if ($settings['nav_contrast']) echo 'slidenav-contrast'; */?> slidenav-previous hidden-touch" data-slideshow-item="previous"></a>
        <a href="#" class="slidenav <?php /*if ($settings['nav_contrast']) echo 'slidenav-contrast'; */?> slidenav-next hidden-touch" data-slideshow-item="next"></a>
        <?php /*endif */?>

        <?php /*if ($settings['nav_overlay'] && ($settings['nav'] != 'none')) : */?>
        <div class="overlay-panel overlay-bottom">
            <?php /*echo $this->render('plugins/widgets/' . $widget->getConfig('name')  . '/views/_nav.php', compact('items', 'settings')); */?>
        </div>
        <?php /*endif */?>

    </div>

    <?php /*if (!$settings['nav_overlay'] && ($settings['nav'] != 'none')) : */?>
    <div class="margin">
        <?php /*echo $this->render('plugins/widgets/' . $widget->getConfig('name')  . '/views/_nav.php', compact('items', 'settings')); */?>
    </div>
    <?php /*endif */?>

</div>-->
