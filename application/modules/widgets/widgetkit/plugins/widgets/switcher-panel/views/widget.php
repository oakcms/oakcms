<?php

// Id
$settings['id'] = substr(uniqid(), -3);

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
    <div class="{wk}-panel-teaser {wk}-cover-background {wk}-position-relative" style="background-image: url('<?php echo $image; ?>');">

        <img class="{wk}-invisible" style="min-height: <?php echo $settings['image_min_height']; ?>px;" src="<?php echo $image; ?>" alt="">

        <div class="{wk}-position-bottom {wk}-margin-left {wk}-margin-right <?php if ($settings['contrast']) echo '{wk}-contrast'; ?>">
        <?php echo $this->render('plugins/widgets/' . $widget->getConfig('name') . '/views/_nav.php', compact('items', 'settings')); ?>
        </div>

    </div>
    <?php else : ?>
        <?php echo $this->render('plugins/widgets/' . $widget->getConfig('name') . '/views/_nav.php', compact('items', 'settings')); ?>
    <?php endif; ?>

    <?php echo $this->render('plugins/widgets/' . $widget->getConfig('name')  . '/views/_content.php', compact('items', 'settings')); ?>

</div>
