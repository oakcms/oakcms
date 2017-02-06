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

?>

<div class="{wk}-panel<?php if ($settings['animation'] != 'none') echo ' {wk}-invisible'; ?>">

    <figure class="{wk}-overlay {wk}-overlay-hover <?php echo $border; ?>">

        <?php echo $thumbnail; ?>

        <div class="{wk}-overlay-panel {wk}-overlay-bottom {wk}-overlay-background {wk}-overlay-<?php echo $settings['overlay_animation']; ?>">

            <?php if ($buttons) : ?>
            <div class="{wk}-flex {wk}-flex-middle {wk}-flex-wrap {wk}-clearfix {wk}-margin" data-{wk}-margin>

                <?php if (($item['title'] && $settings['title']) || ($item['content'] && $settings['content'])) : ?>
                <div class="{wk}-flex-item-auto {wk}-float-left">

                    <?php if ($item['title'] && $settings['title']) : ?>
                    <h3 class="<?php echo $title_size; ?> {wk}-margin-bottom-remove"><?php echo $item['title']; ?></h3>
                    <?php endif; ?>

                    <?php if ($item['content'] && $settings['content']) : ?>
                    <div><?php echo $item['content']; ?></div>
                    <?php endif; ?>

                </div>
                <?php endif; ?>

                <div class="{wk}-grid {wk}-grid-small {wk}-float-right" data-{wk}-grid-margin>

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
                <h3 class="<?php echo $title_size; ?> {wk}-margin-bottom-remove"><?php echo $item['title']; ?></h3>
                <?php endif; ?>

                <?php if ($item['content'] && $settings['content']) : ?>
                <div><?php echo $item['content']; ?></div>
                <?php endif; ?>

            <?php endif; ?>

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
