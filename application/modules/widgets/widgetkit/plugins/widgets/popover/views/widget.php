<?php

// JS Options
$options = array();
$options[] = '\'pos\': \'' . $settings['position'] . '\'';
$options[] = '\'mode\': \'' . $settings['mode'] . '\'';

$options = '{'.implode(',', array_filter($options)).'}';


// Toggle
$toggle  = 'wk-popover-toggle';
$toggle .= $settings['toggle'] ? ' uk-icon-' . $settings['toggle'] . ' uk-icon-button' : '';

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

// Title Size
switch ($settings['title_size']) {
    case 'panel':
        $title_size = 'uk-panel-title';
        break;
    case 'large':
        $title_size = 'uk-heading-large uk-margin-top-remove';
        break;
    default:
        $title_size = 'uk-' . $settings['title_size'] . ' uk-margin-top-remove';
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

// Image
$image = $settings['image'];

if ($settings['image_hero_width'] != 'auto' || $settings['image_hero_height'] != 'auto') {

    $width  = ($settings['image_hero_width'] != 'auto') ? $settings['image_hero_width'] : '';
    $height = ($settings['image_hero_height'] != 'auto') ? $settings['image_hero_height'] : '';

    $image = $app['image']->thumbnailUrl($settings['image'], $width, $height);

}

?>

<?php if ($settings['image']) : ?>
<div class="<?php echo $settings['class']; ?>">
    <div class="uk-position-relative uk-display-inline-block">

        <img src="<?php echo $image; ?>" alt="">

        <?php foreach ($items as $i => $item) :

            // Position
            $left  = isset($item['left']) && $item['left'] ? (float) $item['left'] : '';
            $top   = isset($item['top']) && $item['top'] ? (float) $item['top'] : '';

            if ($left !== 0 && !$left || $top !== 0 && !$top) continue;

            $left .= '%';
            $top  .= '%';

        ?>

        <div class="uk-position-absolute uk-hidden-small" style="left:<?php echo $left; ?>; top:<?php echo $top; ?>;" data-uk-dropdown="<?php echo $options; ?>">

            <?php if ($settings['contrast']) echo '<div class="uk-contrast">'; ?>

            <a class="<?php echo $toggle; ?>"></a>

            <?php if ($settings['contrast']) echo '</div>'; ?>

            <div class="uk-dropdown-blank" <?php echo ($settings['width']) ? 'style="width:' . $settings['width'] . 'px;"': ''; ?>>

               <?php echo $this->render('plugins/widgets/' . $widget->getConfig('name')  . '/views/_content.php', compact('item', 'settings', 'panel', 'title_size', 'link_style', 'link_target')); ?>

            </div>

        </div>

    <?php endforeach; ?>
    </div>

    <div class="uk-margin uk-visible-small" data-uk-slideset="{default: 1}">
        <div class="uk-margin">
            <ul class="uk-slideset uk-grid uk-flex-center">
                <?php foreach ($items as $i => $item) : ?>
                <li><?php echo $this->render('plugins/widgets/' . $widget->getConfig('name')  . '/views/_content.php', compact('item', 'settings', 'panel', 'title_size', 'link_style', 'link_target')); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <ul class="uk-slideset-nav uk-dotnav uk-flex-center"></ul>
    </div>

</div>
<?php endif; ?>
