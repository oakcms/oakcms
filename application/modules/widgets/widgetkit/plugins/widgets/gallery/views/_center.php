<?php

// Buttons
$button_link = ($button_link) ? 'class="' . $button_link . '"' : '';
$button_lightbox = ($button_lightbox) ? 'class="' . $button_lightbox . '"' : '';

$buttons = array();
if ($item['link'] && $settings['link']) {
    $buttons['link'] = '<a ' . $button_link . ' href="' . $item->escape('link') . '"' . $link_target . '>' . $app['translator']->trans($settings['link_text']) . '</a>';
}
if ($settings['lightbox'] && $settings['lightbox_link']) {
    if ($settings['lightbox'] === 'slideshow') {
        $buttons['lightbox'] = '<a ' . $button_lightbox . ' href="#wk-3' . $settings['id'] . '" data-index="'.$index.'" data-{wk}-modal>' . $app['translator']->trans($settings['lightbox_text']) . '</a>';
    } else {
        $buttons['lightbox'] = '<a ' . $button_lightbox . ' ' . $lightbox . ' data-{wk}-lightbox="{group:\'.wk-2' . $settings['id'] . '\'}" ' . $lightbox_caption . '>' . $app['translator']->trans($settings['lightbox_text']) . '</a>';
    }

}

// Overlays
$hover_overlay = ($settings['hover_overlay']) ? '{wk}-overlay-' . $settings['overlay_animation'] : '{wk}-ignore';
$background = ($settings['overlay_background'] == 'hover') ? '{wk}-overlay-' . $settings['overlay_animation'] : '{wk}-ignore';

?>

<div class="{wk}-panel<?php if ($settings['animation'] != 'none') echo ' {wk}-invisible'; ?>">

    <figure class="{wk}-overlay {wk}-overlay-hover <?php echo $border; ?>">

        <?php echo $thumbnail; ?>

        <?php if ($settings['overlay_background'] != 'none') : ?>
        <div class="{wk}-overlay-panel {wk}-overlay-background <?php echo $background; ?>"></div>
        <?php endif; ?>

        <div class="{wk}-overlay-panel <?php echo $hover_overlay; ?> {wk}-flex {wk}-flex-center {wk}-flex-middle {wk}-text-center">
            <div>

                <?php if ($item['title'] && $settings['title']) : ?>
                <h3 class="<?php echo $title_size; ?> {wk}-margin-small"><?php echo $item['title']; ?></h3>
                <?php endif; ?>

                <?php if ($item['content'] && $settings['content']) : ?>
                <div class="{wk}-margin-small"><?php echo $item['content']; ?></div>
                <?php endif; ?>

                <?php if ($buttons) : ?>
                <div class="{wk}-grid {wk}-grid-small {wk}-flex-center {wk}-margin" data-{wk}-grid-margin>

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

        <?php if (!$buttons) : ?>
            <?php if ($settings['lightbox']) : ?>
                <?php if ($settings['lightbox'] === 'slideshow') : ?>
                    <a class="{wk}-position-cover" href="#wk-3<?php echo $settings['id']; ?>" data-index="<?php echo $index; ?>" data-{wk}-modal <?php echo $lightbox_caption; ?>></a>
                <?php else : ?>
                    <a class="{wk}-position-cover" <?php echo $lightbox; ?> data-{wk}-lightbox="{group:'.wk-1<?php echo $settings['id']; ?>'}" <?php echo $lightbox_caption; ?>></a>
                <?php endif; ?>
            <?php elseif ($item['link']) : ?>
                <a class="{wk}-position-cover" href="<?php echo $item->escape('link'); ?>"<?php echo $link_target; ?>></a>
            <?php endif; ?>
        <?php endif; ?>

    </figure>

</div>
