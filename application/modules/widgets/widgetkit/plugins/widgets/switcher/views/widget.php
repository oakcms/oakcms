<?php

// Id
$settings['id'] = substr(uniqid(), -3);

// Width
$nav_width = 'uk-width-medium-' . $settings['width'];

switch ($settings['width']) {
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
}

$content_width = 'uk-width-medium-' . $content_width;

?>

<?php if ($settings['position'] == 'top' || $settings['position'] == 'bottom') : ?>

<div<?php if ($settings['class']) echo ' class="' . $settings['class'] . '"'; ?>>

    <?php if ($settings['position'] == 'top') : ?>
    <?php echo $this->render('plugins/widgets/' . $widget->getConfig('name') . '/views/_nav.php', compact('items', 'settings')); ?>
    <?php endif ?>

    <?php echo $this->render('plugins/widgets/' . $widget->getConfig('name')  . '/views/_content.php', compact('items', 'settings')); ?>

    <?php if ($settings['position'] == 'bottom') : ?>
    <?php echo $this->render('plugins/widgets/' . $widget->getConfig('name')  . '/views/_nav.php', compact('items', 'settings')); ?>
    <?php endif ?>

</div>

<?php else : ?>

<div class="uk-grid uk-grid-match <?php echo $settings['class']; ?>" data-uk-grid-match="{target:'> div > ul'}" data-uk-grid-margin>
    <div class="<?php echo $nav_width ?><?php if ($settings['position'] == 'right') echo ' uk-float-right uk-flex-order-last-medium' ?>">
        <?php echo $this->render('plugins/widgets/' . $widget->getConfig('name')  . '/views/_nav.php', compact('items', 'settings')); ?>
    </div>
    <div class="<?php echo $content_width ?>">
        <?php echo $this->render('plugins/widgets/' . $widget->getConfig('name')  . '/views/_content.php', compact('items', 'settings')); ?>
    </div>
</div>

<?php endif ?>