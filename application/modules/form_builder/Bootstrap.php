<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\form_builder;

use app\components\Controller;
use app\components\CoreView;
use yii\base\BootstrapInterface;
use Yii;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        \yii\base\Event::on(Controller::className(), Controller::EVENT_BEFORE_ACTION, function () {
            Yii::$app->view->on(CoreView::EVENT_AFTER_RENDER, ['app\modules\form_builder\widgets\ShortCode', 'shortCode']);
        });
    }
}
