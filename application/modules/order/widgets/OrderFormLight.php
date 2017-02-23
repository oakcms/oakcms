<?php

namespace app\modules\order\widgets;

use yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\modules\order\models\Order;
use app\modules\order\models\PaymentType;
use app\modules\order\models\ShippingType;
use app\modules\order\models\Field;
use app\modules\order\models\FieldValue;

class OrderFormLight extends \yii\base\Widget
{

    public $view = 'order-form/light';
    public $useAjax = false;
    public $nextStep = false;
    public $staffer = false;

    public function init()
    {
        \app\modules\order\assets\OrderFormLightAsset::register($this->getView());
        \app\modules\order\assets\CreateOrderAsset::register($this->getView());

        return parent::init();
    }

    public function run()
    {
        $paymentTypes = ArrayHelper::map(PaymentType::find()->orderBy('order DESC')->all(), 'id', 'name');

        $orderModel = yii::$app->orderModel;
        $model = new $orderModel;

        $this->getView()->registerJs("oak.createorder.updateCartUrl = '".Url::toRoute(['tools/cart-info'])."';");

        return $this->render($this->view, [
            'model' => $orderModel,
            'paymentTypes' => $paymentTypes,
            'useAjax' => $this->useAjax,
            'nextStep' => $this->nextStep,
            'staffer' => $this->staffer,
        ]);
    }

}
