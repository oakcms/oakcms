<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

namespace app\modules\relations\widgets;

use app\modules\shop\models\Product;
use yii\helpers\Html;
use yii\helpers\Url;
use yii;

class Constructor extends \yii\base\Widget
{
    /**
     * @var object \app\modules\relations\behaviors\AttachRelations
     */

    /** @var $model Product  */
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
                $js .= 'oakcms.relations.renderRow("'.str_replace('\\', '\\\\', $related::className()).'", "'.Html::encode($related->getId()).'", "'.Html::encode($related->getName()).'");';
            }
        }

        $this->getView()->registerJs($js);

        return $this->render($this->view, ['model' => $this->model]);
    }
}
