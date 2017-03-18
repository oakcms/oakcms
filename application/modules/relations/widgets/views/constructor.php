<?php
use yii\helpers\Url;
?>
<div class="relations-constructor">
    <input type="hidden" name="send_relations" value="yes" />
	<div class="oakcms-relations">

    </div>
    <div class="row oakcms-new-relation">
        <div class="col-md-12 col-lg-12">
            <div class="form-group">
                <a href="<?=Url::toRoute(["/admin/relations/default/list", 'model' => $model->getRelatedModel()]);?>" class="oakcms-related-choise btn btn-success">Выбрать... <span class="glyphicon glyphicon-plus add-option"></span></a>
            </div>
        </div>
    </div>
</div>
