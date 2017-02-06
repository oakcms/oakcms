<?php

// Filter Nav
$tabs_center = '';
if ($settings['filter'] == 'tabs') {

    $filter = ($settings['filter_position'] != 'top') ? '{wk}-tab {wk}-tab-'. $settings['filter_position'] : '{wk}-tab';
    $filter .= ($settings['filter_align'] == 'right') ? ' {wk}-tab-flip' : '';
    $filter .= ($settings['filter_align'] != 'center') ? ' {wk}-margin {wk}-margin-bottom-remove' : '';

    // Center
    $tabs_center = '';
    if ($settings['filter_align'] == 'center') {
        $tabs_center = '{wk}-tab-center {wk}-margin {wk}-margin-bottom-remove';
        if ($settings['filter_position'] == 'bottom') {
            $tabs_center .= ' {wk}-tab-center-bottom';
        }
    }

} elseif ($settings['filter'] != 'none') {

    switch ($settings['filter']) {
        case 'text':
            $filter = '{wk}-subnav';
            break;
        case 'lines':
            $filter = '{wk}-subnav {wk}-subnav-line';
            break;
        case 'nav':
            $filter = '{wk}-subnav {wk}-subnav-pill';
            break;
    }

    $filter .= ' {wk}-flex-' . $settings['filter_align'];
}

?>

<?php if ($tabs_center) : ?>
<div class="<?php echo $tabs_center; ?>">
<?php endif ?>

<ul class="<?php echo $filter; ?>"<?php if ($settings['filter'] == 'tabs') echo ' data-{wk}-tab'?>>

    <?php if ($settings['filter_all']) : ?>
    <li class="{wk}-active" data-{wk}-filter=""><a href="#"><?php echo $app['translator']->trans('All'); ?></a></li>
    <?php endif ?>

    <?php foreach ($tags as $i => $tag) : ?>
    <li data-{wk}-filter="<?php echo $tag; ?>"><a href="#"><?php echo ucwords($tag); ?></a></li>
    <?php endforeach; ?>

</ul>

<?php if ($tabs_center) : ?>
</div>
<?php endif ?>
