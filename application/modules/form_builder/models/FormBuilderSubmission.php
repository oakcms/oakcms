<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\form_builder\models;

use Yii;

/**
 * This is the model class for table "{{%form_builder_submission}}".
 *
 * @property integer $id
 * @property integer $status
 * @property integer $form_id
 * @property string  $email
 * @property string  $ip
 * @property integer $created
 * @property string  $data
 */
class FormBuilderSubmission extends \app\components\ActiveRecord
{
    const STATUS_PUBLISHED = 1;
    const STATUS_DRAFT = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%form_builder_submission}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'form_id', 'created'], 'integer'],
            [['form_id', 'ip'], 'required'],
            [['data'], 'string'],
            [['email', 'ip'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'      => Yii::t('form_builder', 'ID'),
            'status'  => Yii::t('form_builder', 'Status'),
            'form_id' => Yii::t('form_builder', 'Form ID'),
            'email'   => Yii::t('form_builder', 'Email'),
            'ip'      => Yii::t('form_builder', 'Ip'),
            'created' => Yii::t('form_builder', 'Created'),
            'data'    => Yii::t('form_builder', 'Data'),
        ];
    }
}
