<?php

namespace app\modules\widgets\controllers\backend;

use yii\web\Controller;

/**
 * Default controller for the `widgets` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $app = require __DIR__.'/../../widgetkit/widgetkit_yii2.php';
        return $this->render('content', ['app' => $app]);
    }
}
