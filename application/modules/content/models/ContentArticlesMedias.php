<?php

namespace app\modules\content\models;

use app\modules\admin\models\Medias;
use Yii;

/**
 * This is the model class for table "{{%content_articles_medias}}".
 *
 * @property integer $id
 * @property integer $content_articles_id
 * @property integer $media_id
 * @property integer $ordering
 */
class ContentArticlesMedias extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%content_articles_medias}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content_articles_id', 'media_id', 'ordering'], 'integer'],
            [['content_articles_id', 'media_id'], 'unique', 'targetAttribute' => ['content_articles_id', 'media_id'], 'message' => 'The combination of Content Articles ID and Media ID has already been taken.'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('content', 'ID'),
            'content_articles_id' => Yii::t('content', 'Content Articles ID'),
            'media_id' => Yii::t('content', 'Media ID'),
            'ordering' => Yii::t('content', 'Ordering'),
        ];
    }

    public function getShopMedias()
    {
        return $this->hasMany(Medias::className(), ['media_id' => 'media_id']);
    }

    public function afterDelete()
    {
        parent::afterDelete();

        foreach($this->getShopMedias()->all() as $media){
            $media->delete();
        }
    }
}
