<?php
namespace app\modules\admin\components\behaviors;

use app\components\ActiveRecord;
use Yii;

class StatusController extends \yii\base\Behavior
{
    /** @var string */
    public $model;

    /** @var string */
    public $error;

    /** @var string */
    public $statusField = 'status';

    public function changeStatus($id, $status)
    {
        $modelClass = $this->model;

        /** @var $model ActiveRecord */
        if(($model = $modelClass::findOne($id))){
            $model->{$this->statusField} = $status;
            $model->update(false, [$this->statusField]);
        }
        else{
            $this->error = Yii::t('admin', 'Not found');
        }

        return $this->owner->formatResponse();
    }
}
