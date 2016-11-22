<?php

namespace app\modules\relations\widgets;

use yii\helpers\Html;
use yii\helpers\Url;
use yii;

class Constructor extends \yii\base\Widget
{
    /**
     * @var object \app\modules\relations\behaviors\AttachRelations
     */
    public $model = null;
    public $inAttribute = 'relations';
    public $view = 'constructor';

    public function init()
    {
        \app\modules\relations\assets\RelationsAsset::register($this->getView());
    }

    public function run()
    {
        $js = '';

        if($relations = $this->model->getRelations()) {
            foreach($relations->all() as $related) {
                $js .= 'pistol88.relations.renderRow("'.str_replace('\\', '\\\\', $related::className()).'", "'.Html::encode($related->getId()).'", "'.Html::encode($related->getName()).'");';
            }
        }

        $this->getView()->registerJs($js);

        return $this->render($this->view, ['model' => $this->model]);
    }
}
