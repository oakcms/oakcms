<?php
namespace app\modules\admin\components\behaviors;

use Yii;

class StatusController extends \yii\base\Behavior
{
    public $model;
    public $error;

    public function changeStatus($id, $status)
    {
        $modelClass = $this->model;

        if(($model = $modelClass::findOne($id))){
            $model->status = $status;
            $model->update();
        }
        else{
            $this->error = Yii::t('admin', 'Not found');
        }

        return $this->owner->formatResponse();
    }
}
