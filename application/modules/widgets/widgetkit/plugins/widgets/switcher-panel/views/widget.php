<?php

// Id
$settings['id'] = substr(uniqid(), -3);

// Panel
$panel = 'uk-panel';
switch ($settings['panel']) {
    case 'box' :
        $panel .= ' uk-panel-box';
        break;
    case 'primary' :
        $panel .= ' uk-panel-box uk-panel-box-primary';
        break;
    case 'secondary' :
        $panel .= ' uk-panel-box uk-panel-box-secondary';
        break;
}

// Image
$image = $settings['image'];

if ($settings['image_hero_width'] != 'auto' || $settings['image_hero_height'] != 'auto') {

    $width  = ($settings['image_hero_width'] != 'auto') ? $settings['image_hero_width'] : '';
    $height = ($settings['image_hero_height'] != 'auto') ? $settings['image_hero_height'] : '';

    $image = $app['image']->thumbnailUrl($settings['image'], $width, $height);

}

?>

<div class="<?php echo $panel; ?> <?php echo $settings['class']; ?>">

    <?php if ($image) : ?>
    <div class="uk-panel-teaser uk-cover-background uk-position-relative" style="background-image: url('<?php echo $image; ?>');">

        <img class="uk-invisible" style="min-height: <?php echo $settings['image_min_height']; ?>px;" src="<?php echo $image; ?>" alt="">

        <div class="uk-position-bottom uk-margin-left uk-margin-right <?php if ($settings['contrast']) echo 'uk-contrast'; ?>">
        <?php echo $this->render('plugins/widgets/' . $widget->getConfig('name') . '/views/_nav.php', compact('items', 'settings')); ?>
        </div>

    </div>
    <?php else : ?>
        <?php echo $this->render('plugins/widgets/' . $widget->getConfig('name') . '/views/_nav.php', compact('items', 'settings')); ?>
    <?php endif; ?>

    <?php echo $this->render('plugins/widgets/' . $widget->getConfig('name')  . '/views/_content.php', compact('items', 'settings')); ?>

</div>
