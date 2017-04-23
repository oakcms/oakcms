<?php

namespace app\modules\admin\components\behaviors;

use yii\db\ActiveRecord;

/**
 * Sortable behavior. Enables model to be sorted manually by admin
 * @package yii\easyii\behaviors
 */
class SortableModel extends \yii\base\Behavior
{
    public $orderAttribute = 'ordering';

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'findMaxOrderNum',
        ];
    }

    public function findMaxOrderNum()
    {
        if (!$this->owner->{$this->orderAttribute}) {
            $maxOrderNum = (int)(new \yii\db\Query())
                ->select('MAX(`' . $this->orderAttribute . '`)')
                ->from($this->owner->tableName())
                ->scalar();
            $this->owner->{$this->orderAttribute} = ++$maxOrderNum;
        }
    }
}
