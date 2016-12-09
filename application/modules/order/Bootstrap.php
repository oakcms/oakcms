<?php
namespace app\modules\order;

use yii\base\BootstrapInterface;
use yii;

class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app)
    {
        if(!$app->has('orderModel')) {
            $app->set('orderModel', ['class' => 'app\modules\order\models\Order']);
        }

        if(!$app->has('paymentModel')) {
            $app->set('paymentModel', ['class' => 'app\modules\order\models\Payment']);
        }

        if(!$app->has('order')) {
            $app->set('order', ['class' => 'app\modules\order\Order']);
        }

        if(empty($app->modules['gridview'])) {
            $app->setModule('gridview', [
                'class' => '\kartik\grid\Module',
            ]);
        }

        if (!isset($app->i18n->translations['order']) && !isset($app->i18n->translations['order*'])) {
            $app->i18n->translations['order'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => __DIR__.'/messages',
                'forceTranslation' => true
            ];
        }
    }
}
