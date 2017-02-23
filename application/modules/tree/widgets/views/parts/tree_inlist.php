<?php
use yii\helpers\Html;

?>
<div class="row" data-key="<?= $category[$widget->idField] ?>">
    <div class="col-lg-6 col-xs-6">
        <input type="hidden" name="ids[]" value="<?= $category[$widget->idField]; ?>" />
        <?= $category[$widget->idField]; ?>.
        <strong><?= $category['name'] ?></strong>
    </div>
    <div class="col-lg-6 col-xs-6 oak-tree-right-col">
        <div class="buttons btn-group">
            <?php if ($widget->viewUrl) { ?>
                <?php if ($widget->viewUrlToSearch) { ?>
                    <?= Html::a(
                        '<span class="glyphicon glyphicon-eye-open">',
                        [$widget->viewUrl, $widget->viewUrlModelName => [$widget->viewUrlModelField => $category[$widget->idField]]],
                        ['class' => 'btn btn-xs blue', 'title' => 'Смотреть', 'style' => 'margin-right:0']
                    ); ?>
                <?php } else { ?>
                    <?= Html::a('<span class="glyphicon glyphicon-eye-open">', [$widget->viewUrl, 'id' => $category[$widget->idField]], ['class' => 'btn btn-xs green', 'title' => 'Смотреть']); ?>
                <?php } ?>
            <?php } ?>
            <?php if ($widget->updateUrl) { ?>
                <?= Html::a('<span class="glyphicon glyphicon-pencil">', [$widget->updateUrl, 'id' => $category[$widget->idField]], ['class' => 'btn btn-xs btn-default', 'title' => 'Редактировать']); ?>
            <?php } ?>
            <?= Html::a('<span class="glyphicon glyphicon-trash">', ['delete', 'id' => $category[$widget->idField]], [
                'class' => 'btn btn-xs red ',
                'data-confirm' => 'Вы уверены, что хотите удалить данную категорию?',
                'method' => 'post',
            ]); ?>
        </div>
    </div>
</div>
