<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 12.10.2016
 * Project: kotsyubynsk
 * File name: view.php
 */
?>

<div class="photo-slide">
    <?php
    foreach ($items as $item):
        if($item->type('media') == 'image') {
            $media = $item->thumbnail('media', '', '', array(), true);
        } else {
            $media = $item['media'];
        }

        ?>
    <div>
        <a class="fancybox-button" rel="fancybox-button" href="<?php echo $item['media'] ?>">
            <img src="<?= $item['media'] ?>" alt="<?= $item['title'] ?>">
        </a>
    </div>
    <?endforeach;?>
</div>
