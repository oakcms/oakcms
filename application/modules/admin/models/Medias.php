<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "{{%admin_medias}}".
 *
 * @property integer $media_id
 * @property string $file_title
 * @property string $file_description
 * @property string $file_meta
 * @property string $file_mimetype
 * @property string $file_type
 * @property string $file_url
 * @property string $file_url_thumb
 * @property string $file_params
 * @property integer $status
 * @property string $created_on
 * @property integer $created_by
 * @property string $modified_on
 * @property integer $modified_by
 */
class Medias extends \yii\db\ActiveRecord
{
    const STATUS_PUBLISHED = 1;
    const STATUS_DRAFT = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%admin_medias}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'created_by', 'modified_by'], 'integer'],
            [['created_on', 'modified_on'], 'safe'],
            [['file_title'], 'string', 'max' => 126],
            [['file_description', 'file_meta'], 'string', 'max' => 254],
            [['file_mimetype'], 'string', 'max' => 64],
            [['file_type'], 'string', 'max' => 32],
            [['file_url', 'file_url_thumb'], 'string', 'max' => 900],
            [['file_params'], 'string', 'max' => 17500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'media_id' => Yii::t('admin', 'Media ID'),
            'file_title' => Yii::t('admin', 'File Title'),
            'file_description' => Yii::t('admin', 'File Description'),
            'file_meta' => Yii::t('admin', 'File Meta'),
            'file_mimetype' => Yii::t('admin', 'File Mimetype'),
            'file_type' => Yii::t('admin', 'File Type'),
            'file_url' => Yii::t('admin', 'File Url'),
            'file_url_thumb' => Yii::t('admin', 'File Url Thumb'),
            'file_params' => Yii::t('admin', 'File Params'),
            'status' => Yii::t('admin', 'Status'),
            'created_on' => Yii::t('admin', 'Created On'),
            'created_by' => Yii::t('admin', 'Created By'),
            'modified_on' => Yii::t('admin', 'Modified On'),
            'modified_by' => Yii::t('admin', 'Modified By'),
        ];
    }

    public function getBigImage()
    {
        return str_replace('//', '/', Yii::$app->homeUrl.$this->file_url);
    }
    public function getThumbImage()
    {
        return str_replace('//', '/', Yii::$app->homeUrl.$this->file_url_thumb);
    }
    public function getBigImageUrl()
    {
        return Yii::getAlias('@webroot').$this->file_url;
    }
    public function getThumbImageUrl()
    {
        return Yii::getAlias('@webroot').$this->file_url_thumb;
    }

    /**
     * Видалення файлів після видалення елементу з бази даних
     */
    public function afterDelete()
    {
        parent::afterDelete();
        unlink($this->getBigImageUrl());
        unlink($this->getThumbImageUrl());
    }
}
