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

class ProfileController extends BackendController
{

    public function actionIndex() {

        $model = UserProfile::findOne(\Yii::$app->getUser()->getId());

        if (!$model->load(Yii::$app->request->post()) && !$model->save()) {
            $this->flash('error', implode(' ', $model->getErrors()));
        }

        $this->layout = '//clear_content';
        return $this->render('index', [
            'model' => $model,
        ]);
    }
}
