<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 21.11.2016
 * Project: kardamon_blog
 * File name: tag.php
 */
?>

<section class="thumbnav">
    <?foreach ($articles as $article):?>
        <?= $this->render('_item.php', ['model' => $article]); ?>
    <?endforeach;?>
</section>
