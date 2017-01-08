<?php

/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\user\controllers\backend;

use Yii;
use app\components\BackendController;
use app\modules\user\models\UserProfile;
use yii\helpers\VarDumper;


/**
 * Class ProfileController
 * @package app\modules\user\controllers\backend
 */
class ProfileController extends BackendController
{

    public function actionIndex() {

        $model = UserProfile::findOne(\Yii::$app->getUser()->getId());
        $model->setScenario('update');
        if ($model->load(Yii::$app->request->post())) {

            if(!$model->save()) {
                $this->flash('error', implode(' ', $model->getErrors()));
            }
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }
}
