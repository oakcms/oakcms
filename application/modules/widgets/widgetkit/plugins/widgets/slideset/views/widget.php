<?php

// JS Options
$options = array();
$options[] = ($settings['animation'] != 'fade') ? 'animation: \'' . $settings['animation'] . '\'' : '';
$options[] = ($settings['duration'] != '200') ? 'duration: ' . $settings['duration']: '';
$options[] = ($settings['autoplay']) ? 'autoplay: true ' : '';
$options[] = ($settings['interval'] != '3000') ? 'autoplayInterval: ' . $settings['interval'] : '';
$options[] = ($settings['autoplay_pause']) ? '' : 'pauseOnHover: false';
$options[] = ($settings['columns']) ? 'default: ' . $settings['columns']: '';
$options[] = ($settings['columns_small']) ? 'small: ' . $settings['columns_small']: '';
$options[] = ($settings['columns_medium']) ? 'medium: ' . $settings['columns_medium']: '';
$options[] = ($settings['columns_large']) ? 'large: ' . $settings['columns_large']: '';
$options[] = ($settings['columns_xlarge']) ? 'xlarge: ' . $settings['columns_xlarge']: '';

// Grid
$grid  = 'uk-grid uk-grid-match uk-flex-center uk-grid-width-1-'.$settings['columns'];
$grid .= $settings['columns_small'] ? ' uk-grid-width-small-1-'.$settings['columns_small'] : '';
$grid .= $settings['columns_medium'] ? ' uk-grid-width-medium-1-'.$settings['columns_medium'] : '';
$grid .= $settings['columns_large'] ? ' uk-grid-width-large-1-'.$settings['columns_large'] : '';
$grid .= $settings['columns_xlarge'] ? ' uk-grid-width-xlarge-1-'.$settings['columns_xlarge'] : '';

$grid .= in_array($settings['gutter'], array('collapse','large','medium','small')) ? ' uk-grid-'.$settings['gutter'] : '' ;

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
    case 'hover' :
        $panel .= ' uk-panel-hover';
        break;
    case 'header' :
        $panel .= ' uk-panel-header';
        break;
    case 'space' :
        $panel .= ' uk-panel-space';
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
        $badge_style .= ($settings['badge_position'] == 'panel') ? ' uk-panel-badge' : '';
        break;
    case 'text-primary':
        $badge_style  = 'uk-text-primary';
        $badge_style .= ($settings['badge_position'] == 'panel') ? ' uk-panel-badge' : '';
        break;
}

// Media Border
$border = ($settings['media_border'] != 'none') ? 'uk-border-' . $settings['media_border'] : '';

// Link Target
$link_target = ($settings['link_target']) ? ' target="_blank"' : '';

// Filter Tags
$tags = array();
if (isset($settings['filter_tags']) && is_array($settings['filter_tags'])) {
    $tags = $settings['filter_tags'];
}

if(!count($tags)){
    foreach ($items as $i => $item) {
        if ($item['tags']) {
            $tags = array_merge($tags, $item['tags']);
        }
    }
    $tags = array_unique($tags);

    natsort($tags);
    $tags = array_values($tags);
}

// JS Options
$options[] = (count($tags) && $settings['filter'] != 'none' && !$settings['filter_all']) ? 'filter: \'' . $tags[0] . '\'': '';
$options   = '{'.implode(',', array_filter($options)).'}';

// Custom Class
$class = $settings['class'] ? ' class="' . $settings['class'] . '"' : '';

?>

<div<?php echo $class; ?> data-uk-slideset="<?php echo $options; ?>">

    <?php if ($tags && $settings['filter'] != 'none' && $settings['filter_position'] == 'top') : ?>
    <?php echo $this->render('plugins/widgets/' . $widget->getConfig('name')  . '/views/_filter.php', compact('items', 'settings', 'tags')); ?>
    <?php endif; ?>

    <div class="uk-slidenav-position uk-margin">

        <ul class="uk-slideset <?php echo $grid; ?>">
        <?php foreach ($items as $item) :

                // Social Buttons
                $socials = '';
                if ($settings['social_buttons']) {
                    $socials .= $item['twitter'] ? '<div><a class="uk-icon-button uk-icon-twitter" href="'. $item->escape('twitter') .'"></a></div>': '';
                    $socials .= $item['facebook'] ? '<div><a class="uk-icon-button uk-icon-facebook" href="'. $item->escape('facebook') .'"></a></div>': '';
                    $socials .= $item['google-plus'] ? '<div><a class="uk-icon-button uk-icon-google-plus" href="'. $item->escape('google-plus') .'"></a></div>': '';
                    $socials .= $item['email'] ? '<div><a class="uk-icon-button uk-icon-envelope-o" href="mailto:'. $item->escape('email') .'"></a></div>': '';
                }

                // Second Image as Overlay
                $media2 = '';
                if ($settings['media_overlay'] == 'image') {
                    foreach ($item as $field) {
                        if ($field != 'media' && $item->type($field) == 'image') {
                            $media2 = $field;
                            break;
                        }
                    }
                }

                // Media Type
                $attrs  = array('class' => '');
                $width  = $item['media.width'];
                $height = $item['media.height'];

                if ($item->type('media') == 'image') {
                    $attrs['alt'] = strip_tags($item['title']);

                    $attrs['class'] .= ($border) ? $border : '';
                    $attrs['class'] .= ($settings['media_animation'] != 'none' && !$media2) ? ' uk-overlay-' . $settings['media_animation'] : '';

                    $width  = ($settings['image_width'] != 'auto') ? $settings['image_width'] : '';
                    $height = ($settings['image_height'] != 'auto') ? $settings['image_height'] : '';
                }

                if ($item->type('media') == 'video') {
                    $attrs['class'] = 'uk-responsive-width';
                    $attrs['controls'] = true;
                }

                if ($item->type('media') == 'iframe') {
                    $attrs['class'] = 'uk-responsive-width';
                }

                $attrs['width']  = ($width) ? $width : '';
                $attrs['height'] = ($height) ? $height : '';

                if (($item->type('media') == 'image') && ($settings['image_width'] != 'auto' || $settings['image_height'] != 'auto')) {
                    $media = $item->thumbnail('media', $width, $height, $attrs);
                } else {
                    $media = $item->media('media', $attrs);
                }

                // Second Image as Overlay
                if ($media2) {

                    $attrs['class'] .= ' uk-overlay-panel uk-overlay-image';
                    $attrs['class'] .= ($settings['media_animation'] != 'none') ? ' uk-overlay-' . $settings['media_animation'] : '';

                    $media2 = $item->thumbnail($media2, $width, $height, $attrs);
                }

                // Link and Overlay
                $overlay       = '';
                $overlay_hover = '';
                $panel_hover   = '';

                if ($item['link']) {

                    if ($settings['panel_link']) {

                        $panel_hover .= ($settings['panel'] == 'box') ? ' uk-panel-box-hover' : '';
                        $panel_hover .= ($settings['panel'] == 'primary') ? ' uk-panel-box-primary-hover' : '';
                        $panel_hover .= ($settings['panel'] == 'secondary') ? ' uk-panel-box-secondary-hover' : '';

                        if (($settings['media_overlay'] == 'icon') ||
                            ($media2) ||
                            ($socials && $settings['media_overlay'] == 'social-buttons') ||
                            ($item['media'] && $settings['media'] && $settings['media_animation'] != 'none')) {
                            $panel_hover .= ' uk-overlay-hover';
                        }

                    } elseif ($settings['media_overlay'] == 'link' || $settings['media_overlay'] == 'icon' || $settings['media_overlay'] == 'image') {
                        $overlay = '<a class="uk-position-cover" href="' . $item->escape('link') . '"' . $link_target . '></a>';
                        $overlay_hover = ' uk-overlay-hover';
                    }

                    if ($settings['media_overlay'] == 'icon') {
                        $overlay = '<div class="uk-overlay-panel uk-overlay-background uk-overlay-icon uk-overlay-' . $settings['overlay_animation'] . '"></div>' . $overlay;
                    }

                    if ($media2) {
                        $overlay = $media2 . $overlay;
                    }

                }

                if ($socials && $settings['media_overlay'] == 'social-buttons') {

                    $overlay  = '<div class="uk-overlay-panel uk-overlay-background uk-overlay-' . $settings['overlay_animation'] . ' uk-flex uk-flex-center uk-flex-middle uk-text-center"><div>';
                    $overlay .= '<div class="uk-grid uk-grid-small" data-uk-grid-margin>' . $socials . '</div>';
                    $overlay .= '</div></div>';

                    $overlay_hover = !$settings['panel_link'] ? ' uk-overlay-hover' : '';
                }

                if ($overlay || ($settings['panel_link'] && $settings['media_animation'] != 'none')) {
                    $media  = '<div class="uk-overlay' . $overlay_hover . ' ' . $border . '">' . $media . $overlay . '</div>';
                }

                // Filter
                $filter = '';
                if ($item['tags'] && $settings['filter'] != 'none') {
                    $filter = ' data-uk-filter="' . implode(',', $item['tags']) . '"';
                }

                // Panel Title last
                if ($settings['title_size'] == 'panel' &&
                    !($item['media'] && $settings['media'] && $settings['media_align'] == 'bottom') &&
                    !($item['content'] && $settings['content']) &&
                    !($socials && ($settings['media_overlay'] != 'social-buttons')) &&
                    !($item['link'] && $settings['link'])) {
                        $title_size .= ' uk-margin-bottom-remove';
                }

            ?>

            <li<?php echo $filter; ?>>

                <div class="<?php echo $panel; ?><?php echo $panel_hover; ?> uk-text-<?php echo $settings['text_align']; ?>">

                    <?php if ($item['link'] && $settings['panel_link']) : ?>
                    <a class="uk-position-cover uk-position-z-index" href="<?php echo $item->escape('link'); ?>"<?php echo $link_target; ?>></a>
                    <?php endif; ?>

                    <?php if ($item['badge'] && $settings['badge'] && $settings['badge_position'] == 'panel') : ?>
                    <div class="uk-panel-badge <?php echo $badge_style; ?>"><?php echo $item['badge']; ?></div>
                    <?php endif; ?>

                    <?php if ($item['media'] && $settings['media'] && in_array($settings['media_align'], array('teaser', 'top'))) : ?>
                    <div class="uk-text-center <?php echo (($settings['media_align'] == 'teaser') ? 'uk-panel-teaser' : 'uk-margin uk-margin-top-remove'); ?>"><?php echo $media; ?></div>
                    <?php endif; ?>

                    <?php if ($item['title'] && $settings['title']) : ?>
                    <h3 class="<?php echo $title_size; ?>">

                        <?php if ($item['link']) : ?>
                            <a class="uk-link-reset" href="<?php echo $item->escape('link'); ?>"<?php echo $link_target; ?>><?php echo $item['title']; ?></a>
                        <?php else : ?>
                            <?php echo $item['title']; ?>
                        <?php endif; ?>

                        <?php if ($item['badge'] && $settings['badge'] && $settings['badge_position'] == 'title') : ?>
                        <span class="uk-margin-small-left <?php echo $badge_style; ?>"><?php echo $item['badge']; ?></span>
                        <?php endif; ?>

                    </h3>
                    <?php endif; ?>

                    <?php if ($item['media'] && $settings['media'] && $settings['media_align'] == 'bottom') : ?>
                    <div class="uk-margin uk-text-center"><?php echo $media; ?></div>
                    <?php endif; ?>

                    <?php if ($item['content'] && $settings['content']) : ?>
                    <div class="uk-margin"><?php echo $item['content']; ?></div>
                    <?php endif; ?>

                    <?php if ($socials && ($settings['media_overlay'] != 'social-buttons')) : ?>
                    <div class="uk-grid uk-grid-small uk-flex-<?php echo $settings['text_align']; ?>" data-uk-grid-margin><?php echo $socials; ?></div>
                    <?php endif; ?>

                    <?php if ($item['link'] && $settings['link']) : ?>
                    <p><a<?php if($link_style) echo ' class="' . $link_style . '"'; ?> href="<?php echo $item->escape('link'); ?>"<?php echo $link_target; ?>><?php echo $app['translator']->trans($settings['link_text']); ?></a></p>
                    <?php endif; ?>

                </div>

            </li>

        <?php endforeach; ?>
        </ul>

        <?php if ($settings['slidenav'] == 'above') : ?>
        <a href="#" class="uk-slidenav <?php if ($settings['slidenav_contrast']) echo 'uk-slidenav-contrast'; ?> uk-slidenav-previous uk-hidden-touch" data-uk-slideset-item="previous"></a>
        <a href="#" class="uk-slidenav <?php if ($settings['slidenav_contrast']) echo 'uk-slidenav-contrast'; ?> uk-slidenav-next uk-hidden-touch" data-uk-slideset-item="next"></a>
        <?php endif ?>

    </div>

    <?php if ($settings['slidenav'] == 'bottom') : ?>
    <div class="uk-flex uk-flex-<?php echo $settings['slidenav_align']; ?> uk-margin-top">
        <div class="uk-grid uk-grid-small">
            <div><a href="#" class="uk-slidenav uk-slidenav-previous" data-uk-slideset-item="previous"></a></div>
            <div><a href="#" class="uk-slidenav uk-slidenav-next" data-uk-slideset-item="next"></a></div>
        </div>
    </div>
    <?php endif ?>

    <?php if ($settings['nav']) : ?>
    <ul class="uk-slideset-nav uk-dotnav uk-flex-center uk-margin-bottom-remove"></ul>
    <?php endif ?>

    <?php if ($tags && $settings['filter'] != 'none' && $settings['filter_position'] == 'bottom') : ?>
    <?php echo $this->render('plugins/widgets/' . $widget->getConfig('name')  . '/views/_filter.php', compact('items', 'settings', 'tags')); ?>
    <?php endif; ?>

</div>
