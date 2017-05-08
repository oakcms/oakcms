<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\admin\components\behaviors;

use Yii;
use yii\db\ActiveRecord;

/**
 * CacheFlush behavior
 * @package yii\content\behaviors
 * @inheritdoc
 */
class CacheFlush extends \yii\base\Behavior
{
    /** @var  string */
    public $key = false;

    public function attach($owner)
    {
        parent::attach($owner);

        //if(!$this->key) $this->key = constant(get_class($owner).'::CACHE_KEY');
    }

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'flush',
            ActiveRecord::EVENT_AFTER_UPDATE => 'flush',
            ActiveRecord::EVENT_AFTER_DELETE => 'flush',
        ];
    }

    /**
     * Flush cache
     */
    public function flush()
    {
        if($this->key) {
            if(is_array($this->key)){
                foreach($this->key as $key){
                    Yii::$app->cache->delete($key);
                }
            } else {
                Yii::$app->cache->delete($this->key);
            }
        } else {
            Yii::$app->cache->flush();
        }
    }
}
