<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

namespace app\modules\form_builder\models;

use Yii;
use yii\behaviors\SluggableBehavior;

/**
 * This is the model class for table "{{%form_builder_forms}}".
 *
 * @property integer $id
 * @property string  $title
 * @property string  $slug
 * @property integer $sort
 * @property integer $status
 * @property string  $data
 */
class FormBuilderForms extends \app\components\ActiveRecord
{
    const STATUS_PUBLISHED = 1;
    const STATUS_DRAFT = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%form_builder_forms}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'slug' => [
                'class' => SluggableBehavior::className(),
                'attribute' => 'title',
                'slugAttribute' => 'slug',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['sort', 'status'], 'integer'],
            [['data'], 'string'],
            [['slug'], 'unique'],
            [['title', 'slug'], 'string', 'max' => 255],
        ];
    }

    public function getSubmissions() {
        return $this->hasMany(FormBuilderSubmission::className(), ['form_id', 'id']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'     => Yii::t('form_builder', 'ID'),
            'title'  => Yii::t('form_builder', 'Title'),
            'slug'   => Yii::t('form_builder', 'Slug'),
            'sort'   => Yii::t('form_builder', 'Sort'),
            'status' => Yii::t('form_builder', 'Status'),
            'data'   => Yii::t('form_builder', 'Data'),
        ];
    }
}
