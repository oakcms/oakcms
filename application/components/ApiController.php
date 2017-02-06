<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 11.01.2017
 * Project: kn-group-site
 * File name: ApiController.php
 */

namespace app\components;


use app\modules\text\api\Text;

class ApiController extends Controller
{
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => \yii\filters\ContentNegotiator::className(),
                'formatParam' => '_format',
                'formats' => [
                    //'application/xml'  => \yii\web\Response::FORMAT_XML,
                    'application/json' => \yii\web\Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    public function renderWidgets($model, $attribute)
    {
        if(isset($model->{$attribute})){
            $model->{$attribute} = (new \app\components\ShortCode)->parse('block', $model->{$attribute}, function($attrs) {
                if (isset($attrs['id'])) {
                    return Text::get($attrs['id'], true);
                } elseif(isset($attrs['position'])) {
                    return Text::get($attrs['position']);
                } else {
                    return 'error';
                }
            });

            if (!$app = include(__DIR__.'/../modules/widgets/widgetkit/widgetkit_yii2.php')) {
                return;
            }

            $model->{$attribute} = $app['shortcode']->parse('widgetkit', $model->{$attribute}, function($attrs) use ($app) {
                return $app->renderWidget($attrs);
            });
        }
        return $model;
    }
}
