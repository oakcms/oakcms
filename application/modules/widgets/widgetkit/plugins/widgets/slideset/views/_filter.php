<?php

// Filter Nav
$tabs_center = '';
if ($settings['filter'] == 'tabs') {

    $filter = ($settings['filter_position'] != 'top') ? 'uk-tab uk-tab-'. $settings['filter_position'] : 'uk-tab';
    $filter .= ($settings['filter_align'] == 'right') ? ' uk-tab-flip' : '';
    $filter .= ($settings['filter_align'] != 'center') ? ' uk-margin uk-margin-bottom-remove' : '';

    // Center
    $tabs_center = '';
    if ($settings['filter_align'] == 'center') {
        $tabs_center = 'uk-tab-center uk-margin uk-margin-bottom-remove';
        if ($settings['filter_position'] == 'bottom') {
            $tabs_center .= ' uk-tab-center-bottom';
        }
    }

} elseif ($settings['filter'] != 'none') {

    switch ($settings['filter']) {
        case 'text':
            $filter = 'uk-subnav';
            break;
        case 'lines':
            $filter = 'uk-subnav uk-subnav-line';
            break;
        case 'nav':
            $filter = 'uk-subnav uk-subnav-pill';
            break;
    }

    $filter .= ' uk-flex-' . $settings['filter_align'];
}

?>

<?php if ($tabs_center) : ?>
<div class="<?php echo $tabs_center; ?>">
<?php endif ?>

<ul class="<?php echo $filter; ?>"<?php if ($settings['filter'] == 'tabs') echo ' data-uk-tab'?>>

    <?php if ($settings['filter_all']) : ?>
    <li class="uk-active" data-uk-filter=""><a href="#"><?php echo $app['translator']->trans('All'); ?></a></li>
    <?php endif ?>

    <?php foreach ($tags as $i => $tag) : ?>
    <li data-uk-filter="<?php echo $tag; ?>"><a href="#"><?php echo ucwords($tag); ?></a></li>
    <?php endforeach; ?>

</ul>

<?php if ($tabs_center) : ?>
</div>
<?php endif ?>