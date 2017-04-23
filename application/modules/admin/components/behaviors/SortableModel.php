<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\admin\components\behaviors;

use yii\db\ActiveRecord;

/**
 * Sortable behavior. Enables model to be sorted manually by admin
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
