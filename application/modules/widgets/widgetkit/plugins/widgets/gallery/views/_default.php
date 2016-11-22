<?php

// Panel
$panel = '';
switch ($settings['panel']) {
    case 'box' :
        $panel = ' uk-panel-box';
        break;
    case 'primary' :
        $panel = ' uk-panel-box uk-panel-box-primary';
        break;
    case 'secondary' :
        $panel = ' uk-panel-box uk-panel-box-secondary';
        break;
    case 'hover' :
        $panel = ' uk-panel-hover';
        break;
}

// Buttons
if ($settings['overlay_center'] != 'buttons') {
    switch ($settings['link_style']) {
        case 'icon':
        case 'icon-small':
        case 'icon-medium':
        case 'icon-large':
        case 'icon-button':
            $button_link .= ' uk-icon-muted';
            break;
    }
    switch ($settings['lightbox_style']) {
        case 'icon':
        case 'icon-small':
        case 'icon-medium':
        case 'icon-large':
        case 'icon-button':
            $button_lightbox .= ' uk-icon-muted';
            break;
    }
}


$button_link = ($button_link) ? 'class="' . $button_link . '"' : '';
$button_lightbox = ($button_lightbox) ? 'class="' . $button_lightbox . '"' : '';

$buttons = array();
if ($item['link'] && $settings['link']) {
    $buttons['link'] = '<a ' . $button_link . ' href="' . $item->escape('link') . '"' . $link_target . '>' . $app['translator']->trans($settings['link_text']) . '</a>';
}
if ($settings['lightbox'] && $settings['lightbox_link']) {
    if ($settings['lightbox'] === 'slideshow') {
        $buttons['lightbox'] = '<a ' . $button_lightbox . ' href="#wk-3' . $settings['id'] . '" data-index="'.$index.'" data-uk-modal>' . $app['translator']->trans($settings['lightbox_text']) . '</a>';
    } else {
        $buttons['lightbox'] = '<a ' . $button_lightbox . ' ' . $lightbox . ' data-uk-lightbox="{group:\'.wk-2' . $settings['id'] . '\'}" ' . $lightbox_caption . '>' . $app['translator']->trans($settings['lightbox_text']) . '</a>';
    }
}

// Overlay Background
$background = ($settings['overlay_background'] == 'hover') ? 'uk-overlay-' . $settings['overlay_animation'] : 'uk-ignore';

?>

<div class="uk-panel<?php echo $panel; ?><?php if ($settings['animation'] != 'none') echo ' uk-invisible'; ?>">

    <div class="uk-panel-teaser">

        <figure class="uk-overlay uk-overlay-hover <?php echo $border; ?>">

            <?php echo $thumbnail; ?>

            <?php if ($thumbnail_overlay) : ?>
                <?php echo $thumbnail_overlay; ?>
            <?php endif; ?>

            <?php if ($settings['overlay_background'] != 'none') : ?>
            <div class="uk-overlay-panel uk-overlay-background <?php echo $background; ?>"></div>
            <?php endif; ?>

            <?php if ($settings['overlay_center'] == 'icon' || (!$buttons && $settings['overlay_center'] == 'buttons') || (!($item['content'] && $settings['content']) && $settings['overlay_center'] == 'content')) : ?>
                <div class="uk-overlay-panel uk-overlay-icon uk-overlay-<?php echo $settings['overlay_animation']; ?>"></div>
            <?php elseif (($settings['overlay_center'] == 'buttons') || ($settings['overlay_center'] == 'content')) : ?>
                <div class="uk-overlay-panel uk-overlay-<?php echo $settings['overlay_animation']; ?> uk-flex uk-flex-center uk-flex-middle uk-text-center">
                    <div>

                        <?php if ($item['content'] && $settings['content'] && $settings['overlay_center'] == 'content') : ?>
                        <div><?php echo $item['content']; ?></div>
                        <?php endif; ?>

                        <?php if ($buttons && $settings['overlay_center'] == 'buttons') : ?>
                        <div class="uk-grid uk-grid-small uk-flex-center" data-uk-grid-margin>

                            <?php if (isset($buttons['link'])) : ?>
                            <div><?php echo $buttons['link']; ?></div>
                            <?php endif; ?>

                            <?php if (isset($buttons['lightbox'])) : ?>
                            <div><?php echo $buttons['lightbox']; ?></div>
                            <?php endif; ?>

                        </div>
                        <?php endif; ?>

                    </div>
                </div>
            <?php endif; ?>

            <?php if ($settings['overlay_center'] != 'none' && ($settings['overlay_center'] != 'buttons' || !$buttons)) : ?>
                <?php if ($settings['lightbox']) : ?>
                    <?php if ($settings['lightbox'] === 'slideshow') : ?>
                        <a class="uk-position-cover" href="#wk-3<?php echo $settings['id']; ?>" data-index="<?php echo $index; ?>" data-uk-modal <?php echo $lightbox_caption; ?>></a>
                    <?php else : ?>
                        <a class="uk-position-cover" <?php echo $lightbox; ?> data-uk-lightbox="{group:'.wk-1<?php echo $settings['id']; ?>'}" <?php echo $lightbox_caption; ?>></a>
                    <?php endif; ?>
                <?php elseif ($item['link']) : ?>
                    <a class="uk-position-cover" href="<?php echo $item->escape('link'); ?>"<?php echo $link_target; ?>></a>
                <?php endif; ?>
            <?php endif; ?>

        </figure>

    </div>

    <?php if ($buttons && $settings['overlay_center'] != 'buttons') : ?>
    <div class="uk-flex uk-flex-middle uk-flex-wrap uk-clearfix uk-margin" data-uk-margin>

        <?php if (($item['title'] && $settings['title']) || ($item['content'] && $settings['content'] && $settings['overlay_center'] != 'content')) : ?>
        <div class="uk-flex-item-auto uk-float-left">

            <?php if ($item['title'] && $settings['title']) : ?>
            <h3 class="<?php echo $title_size; ?> uk-margin-bottom-remove"><?php echo $item['title']; ?></h3>
            <?php endif; ?>

            <?php if ($item['content'] && $settings['content'] && $settings['overlay_center'] != 'content') : ?>
            <div><?php echo $item['content']; ?></div>
            <?php endif; ?>

        </div>
        <?php endif; ?>

        <div class="uk-grid uk-grid-small uk-float-right" data-uk-grid-margin>

            <?php if (isset($buttons['link'])) : ?>
            <div><?php echo $buttons['link']; ?></div>
            <?php endif; ?>

            <?php if (isset($buttons['lightbox'])) : ?>
            <div><?php echo $buttons['lightbox']; ?></div>
            <?php endif; ?>

        </div>

    </div>
    <?php else : ?>

        <?php if ($item['title'] && $settings['title']) : ?>
        <h3 class="<?php echo $title_size; ?> uk-margin-bottom-remove"><?php echo $item['title']; ?></h3>
        <?php endif; ?>

        <?php if ($item['content'] && $settings['content'] && $settings['overlay_center'] != 'content') : ?>
        <div><?php echo $item['content']; ?></div>
        <?php endif; ?>

    <?php endif; ?>

</div>
