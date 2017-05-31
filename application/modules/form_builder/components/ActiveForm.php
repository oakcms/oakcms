<?php
/**
 * @package    oakcms/oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\form_builder\components;

use app\modules\admin\widgets\Html;
use app\modules\form_builder\models\FormBuilder;
use yii\base\InvalidCallException;
use yii\base\Model;
use yii\helpers\Json;
use yii\widgets\ActiveFormAsset;

class ActiveForm extends \kartik\form\ActiveForm
{
    /**
     * @var Model
     */
    public $model;

    /**
     * @var integer
     */
    public $formId;

    public function run()
    {
        if($this->model instanceof FormBuilder) {
            if (!empty($this->_fields)) {
                throw new InvalidCallException('Each beginField() should have a matching endField() call.');
            }

            $content = ob_get_clean();
            echo Html::beginForm($this->action, $this->method, $this->options);
            echo Html::hiddenInput($this->model->formName() . '[formId]', $this->formId);
            echo $content;

            if ($this->enableClientScript) {
                $id = $this->options['id'];
                $options = Json::htmlEncode($this->getClientOptions());
                $attributes = Json::htmlEncode($this->attributes);
                $view = $this->getView();
                ActiveFormAsset::register($view);
                $view->registerJs("jQuery('#$id').yiiActiveForm($attributes, $options);");
            }

            echo Html::endForm();
        }
    }
}
