<?php
use newerton\fancybox\FancyBox;
use yii\helpers\Url;
use yii\helpers\Html;
?>
<div class="relations-constructor">
    <input type="hidden" name="send_relations" value="yes" />
	<div class="pistol88-relations">

    </div>
    <div class="row pistol88-new-relation">
        <div class="col-md-12 col-lg-12">
            <div class="form-group">
                <a href="<?=Url::toRoute(["/admin/relations/default/list", 'model' => $model->getRelatedModel()]);?>" class="pistol88-related-choise btn btn-success">Выбрать... <span class="glyphicon glyphicon-plus add-option"></span></a>
            </div>
        </div>
    </div>
</div>
<?=FancyBox::widget([
    'target' => '.pistol88-related-choise',
    'helpers' => false,
    'mouse' => true,
    'config' => [
        'maxWidth' => '500px',
        'type' => 'iframe',
        'maxHeight' => '500px',
        'playSpeed' => 7000,
        'padding' => 0,
        'fitToView' => false,
        'width' => '500px',
        //'height' => '440px',
        'autoSize' => true,
        'openEffect' => 'elastic',
        'closeEffect' => 'elastic',
        'openOpacity' => true,
    ]
]);
?>
