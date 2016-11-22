<?php
namespace app\components;

use Yii;
use yii\imagine\Image;

/**
 * Class ApiObject
 * @package yii\easyii\components
 */
class ApiObject extends \yii\base\Object
{
    /** @var \yii\base\Model  */
    public $model;

    /**
     * Generates ApiObject, attaching all settable properties to the child object
     * @param \yii\base\Model $model
     */
    public function __construct($model){
        $this->model = $model;

        foreach($model->attributes as $attribute => $value){
            if($this->canSetProperty($attribute)){
                $this->{$attribute} = $value;
            }
        }

        $this->init();
    }

    /**
     * calls after __construct
     */
    public function init(){}

    /**
     * Returns object id
     * @return int
     */
    public function getId(){
        return $this->model->primaryKey;
    }

    /**
     * Creates thumb from model->image attribute with specified width and height.
     * @param int|null $width
     * @param int|null $height
     * @param bool $crop if false image will be resize instead of cropping
     * @return string
     */
    public function thumb($width = null, $height = null, $crop = true)
    {
        if($this->image && ($width || $height)){
            return Image::thumbnail($this->image, $width, $height, $crop);
        }
        return '';
    }
}
