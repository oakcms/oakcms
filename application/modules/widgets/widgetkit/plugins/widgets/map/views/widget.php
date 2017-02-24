<?php

$map_id  = uniqid('wk-map');
$markers = array();
$width   = $settings['width']  == 'auto' ? 'auto'  : ((int)$settings['width']).'px';
$height  = $settings['height'] == 'auto' ? '300px' : ((int)$settings['height']).'px';

// Markers
foreach ($items as $i => $item) {

    if (isset($item['location']) && $item['location']) {

        $icon = trim(isset($item['location']['marker']) ? $item['location']['marker'] : '');

        if ($icon && !filter_var($icon, FILTER_VALIDATE_URL) && realpath($icon)) {
            $icon = $app['request']->getBaseUrl() .'/'. $item['location']['marker'];
        }

        $marker = array(
            'lat'     => $item['location']['lat'],
            'lng'     => $item['location']['lng'],
            'icon'    => $icon,
            'title'   => $item['title'],
            'content' => ''
        );

        if (($item['title'] && $settings['title']) ||
            ($item['content'] && $settings['content']) ||
            ($item['media'] && $settings['media'])) {
                $marker['content'] = $app->convertUrls($this->render('plugins/widgets/' . $widget->getConfig('name')  . '/views/_content.php', compact('item', 'settings')));
        }

        $markers[] = $marker;
    }
}

$settings['markers'] = $markers;
$settings['directionsText'] = $app['translator']->trans('Get Directions');

?>

<script type="widgetkit/map" data-id="<?php echo $map_id;?>" data-class="<?php echo $settings['class']; ?> {wk}-img-preserve" data-style="width:<?php echo $width?>;height:<?php echo $height?>;">
    <?php echo json_encode($settings) ?>
</script>
