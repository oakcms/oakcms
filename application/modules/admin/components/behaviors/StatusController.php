<?php
namespace app\modules\admin\components\behaviors;

use app\components\ActiveRecord;
use Yii;

class StatusController extends \yii\base\Behavior
{
    public $model;
    public $error;

    public function changeStatus($id, $status)
    {
        $modelClass = $this->model;

        /** @var $model ActiveRecord */
        if(($model = $modelClass::findOne($id))){
            $model->status = $status;
            if(!$model->update()) {
                var_dump($model->getErrors());
                exit;
            }
        }
        else{
            $this->error = Yii::t('admin', 'Not found');
        }

        return $this->owner->formatResponse();
    }
}
