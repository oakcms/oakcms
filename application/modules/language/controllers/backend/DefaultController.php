<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\modules\language\controllers\backend;

use yii\web\Controller;

/**
 * Default controller for the `language` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
