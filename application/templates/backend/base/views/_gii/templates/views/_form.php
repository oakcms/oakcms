<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

/* @var $model \yii\db\ActiveRecord */
$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

echo "<?php\n";
?>

use yii\helpers\Html;
use app\modules\admin\widgets\Button;
use app\modules\admin\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */
/* @var $form yii\widgets\ActiveForm */

$this->params['actions_buttons'] = [
    [
        'label' => $model->isNewRecord ? <?= $generator->generateString('Create') ?> : <?= $generator->generateString('Update') ?>,
        'options' => [
            'form' => '<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-id',
            'type' => 'submit'
        ],
        'icon' => 'fa fa-save',
        'iconPosition' => Button::ICON_POSITION_LEFT,
        'size' => Button::SIZE_SMALL,
        'disabled' => false,
        'block' => false,
        'type' => Button::TYPE_CIRCLE,
        'color' => 'btn-success'
    ],
    [
        'label' => <?= $generator->generateString('Save & Continue Edit') ?>,
        'options' => [
            'onclick' => 'sendFormReload("#<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-id")',
        ],
        'icon' => 'fa fa-check-circle',
        'iconPosition' => Button::ICON_POSITION_LEFT,
        'size' => Button::SIZE_SMALL,
        'disabled' => false,
        'block' => false,
        'type' => Button::TYPE_CIRCLE,
        'color' => 'btn-success'
    ]
]
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">

    <?= "<?php " ?>$form = ActiveForm::begin([
        'options'=>[
            'id'=>'<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-id',
        ],
    ]); ?>

<?php foreach ($generator->getColumnNames() as $attribute) {
    if (in_array($attribute, $safeAttributes)) {
        echo "    <?= " . $generator->generateActiveField($attribute) . " ?>\n";
    }
} ?>

    <?= "<?php " ?>ActiveForm::end(); ?>

</div>
<script>
    function sendFormReload(elm) {
        $(elm).append($("<input type='hidden' name='submit-type' value='continue'>"));
        $(elm).submit();
        return false;
    }
</script>
