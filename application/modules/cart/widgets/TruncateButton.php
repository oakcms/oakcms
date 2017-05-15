<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\cart\widgets;

use yii\helpers\Html;
use yii;

class TruncateButton extends \yii\base\Widget
{
    public $text = NULL;
    public $cssClass = 'btn btn-danger';

    public function init()
    {
        parent::init();

        \app\modules\cart\assets\WidgetAsset::register($this->getView());

        if ($this->text == NULL) {
            $this->text = yii::t('cart', 'Truncate');
        }

        return true;
    }

    public function run()
    {
        return Html::a(Html::encode($this->text), ['/cart/default/truncate'], ['class' => 'oakcms-cart-truncate-button ' . $this->cssClass]);
    }
}
