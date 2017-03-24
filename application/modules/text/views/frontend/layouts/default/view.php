<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.5
 */

/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 16.09.2016
 * Project: osnovasite
 * File name: view.php
 *
 * @var $model \app\modules\text\models\Text;
 */
?>
<section id="<?= $model->settings['id']['value'] ?>">
    <article class="container <?= $model->settings['cssClass']['value'] ?>">
        <?php if ((int)$model->settings['hideTitle']['value'] !== 1): ?>
            <h2><?= $model->title ?></h2>
            <?php if ($model->subtitle != ''): ?>
                <h3><?= $model->subtitle ?></h3>
            <?php endif; ?>
        <?php endif; ?>
        <?= $model->text ?>
    </article>
</section>
