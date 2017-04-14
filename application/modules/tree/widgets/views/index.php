<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

use yii\helpers\Html;
use yii\web\View;

/**
 * @var $widget object
 * @var $categoriesTree string
 */

?>
<div class="oak-tree">
    <div class="dd nestable_list" data-url="<?= \yii\helpers\Url::to([$widget->updateNestableUrl]) ?>">
        <ol class="dd-list">
            <?= $categoriesTree; ?>
        </ol>
    </div>
</div>
