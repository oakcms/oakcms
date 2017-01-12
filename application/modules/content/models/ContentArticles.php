<?php

namespace app\modules\content\models;

use app\components\behaviors\HitableBehavior;
use app\modules\admin\components\behaviors\SettingModel;
use app\modules\admin\models\Medias;
use dosamigos\translateable\TranslateableBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "{{%content_articles}}".
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property string $content
 * @property integer $create_user_id
 * @property integer $update_user_id
 * @property integer $published_at
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $status
 * @property string $image
 * @property integer $comment_status
 * @property string $create_user_ip
 * @property integer $access_type
 * @property integer $category_id
 * @property string $settings
 */
class ContentArticles extends \app\components\ActiveRecord
{
    const STATUS_PUBLISHED = 1;
    const STATUS_DRAFT = 0;


    public function fields()
    {
        return [
            'id',
            'title' => function($model) {
                return $model->title;
            },
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            SettingModel::className(),
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'create_user_id',
                'updatedByAttribute' => 'update_user_id',
            ],
            [
                'class' => SluggableBehavior::className(),
                'attribute' => 'title',
                'slugAttribute' => 'slug',
            ],
            'hit' => [
                'class' => HitableBehavior::className(),
                'attribute' => 'hits',          //attribute which should contain uniquie hits value
                'group' => false,               //group name of the model (class name by default)
                'delay' => 60 * 15,             //register the same visitor every hour
                'table_name' => '{{%hits}}'     //table with hits data
            ],
            [
                'class'                 => \mongosoft\file\UploadImageBehavior::className(),
                'attribute'             => 'image',
                'scenarios'             => ['insert', 'update'],
                'placeholder'           => '@webroot/uploads/user/non_image.png',
                'createThumbsOnSave'    => true,
                'path'                  => '@webroot/uploads/news/{id}',
                'url'                   => '@web/uploads/news/{id}',
                'thumbPath'             => '@webroot/uploads/news/{id}/thumb',
                'thumbUrl'              => '@web/uploads/news/{id}/thumb',
                'thumbs'                => [
                    'thumb'             => ['width' => 340, 'quality' => 100, 'mode' => 'outbound'],
                ],
            ],
            'trans' => [
                'class' => TranslateableBehavior::className(),
                'translationAttributes' => [
                    'slug', 'title', 'description', 'content', 'link', 'meta_title', 'meta_keywords', 'meta_description', 'settings'
                ]
            ],
        ];
    }

    public function getTags()
    {
        return $this->hasMany(ContentTags::className(), ['id' => 'content_tags_id'])->viaTable('{{%content_tag_assn}}', ['content_id' => 'id']);
    }

    public function getCategory() {
        return $this->hasOne(ContentCategory::className(), ['id' => 'category_id']);
    }

    public function getTranslations()
    {
        return $this->hasMany(ContentArticlesLang::className(), ['content_articles_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%content_articles}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'content', 'language'], 'required'],
            [['update_user_id','status', 'comment_status', 'access_type', 'category_id', 'main_image'], 'integer'],
            [['create_user_ip'], 'string', 'max' => 20],
            //[['settings'], 'string'],
            [['title', 'link', 'meta_title', 'meta_keywords', 'meta_description'], 'string', 'max' => 255],
            [['published_at'], 'filter', 'filter' => 'strtotime', 'skipOnEmpty' => true],
            ['published_at', 'default', 'value' => time()],
            //[['category_id'], 'exist', 'targetClass' => ArticleCategory::className(), 'targetAttribute' => 'id'],
            [['slug'], 'string', 'max' => 150],
            [
                ['slug'],
                'unique',
                'targetClass' => ContentArticlesLang::className(),
                'targetAttribute' => 'slug',
                'filter' => function ($query) {
                    /**
                     * @var $query ActiveQuery
                     */
                    $query->andWhere('content_articles_id <> :a_id', ['a_id' => $this->id]);
                    return $query;
                }
            ],
            [['image'], 'image', 'extensions' => 'jpg, jpeg, gif, png, JPG', 'on' => ['insert', 'update']],
            [['tagNames'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('content', 'ID'),
            'create_user_id' => Yii::t('content', 'Create User ID'),
            'update_user_id' => Yii::t('content', 'Update User ID'),
            'published_at' => Yii::t('content', 'Published At'),
            'created_at' => Yii::t('content', 'Created At'),
            'updated_at' => Yii::t('content', 'Updated At'),
            'status' => Yii::t('content', 'Status'),
            'comment_status' => Yii::t('content', 'Comment Status'),
            'create_user_ip' => Yii::t('content', 'Create User Ip'),
            'access_type' => Yii::t('content', 'Access Type'),
            'category_id' => Yii::t('content', 'Category'),
        ];
    }

    public function beforeValidate()
    {
        if(is_int($this->published_at)) {
            $this->published_at = date('Y-m-d H:i:s', $this->published_at);
        }

        return parent::beforeValidate(); // TODO: Change the autogenerated stub
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($this->create_user_ip == '') {
                $this->create_user_ip = Yii::$app->request->userIP;
            }

            return true;
        } else {
            return false;
        }
    }

    public function afterDelete()
    {
        parent::afterDelete(); // TODO: Change the autogenerated stub

        // Видалення перекладу
        foreach ($this->getTranslations()->all() as $translations) {
            $translations->delete();
        }

        foreach ($this->getArticlesMedias()->all() as $medias) {
            $medias->delete();
        }
    }

    public function getArticlesMedias()
    {
        return $this->hasMany(ContentArticlesMedias::className(), ['content_articles_id' => 'id']);
    }

    public function getMedias()
    {
        return $this->hasMany(Medias::className(), ['media_id' => 'media_id'])
            ->via('articlesMedias');
    }

    /**
     * Наступна стаття
     * @return array|null|\yii\db\ActiveRecord
     */
    public function getNext()
    {
        $record = self::find()
            ->where('id>:current_id AND category_id=:cat_id')
            ->addParams([':current_id'=>$this->id, ':cat_id'=>$this->category_id])
            ->limit(1)
            ->orderBy('id ASC')
            ->one();
        if($record!==null)
            return $record;
        return null;
    }

    /**
     * Попередня стаття
     * @return array|null|\yii\db\ActiveRecord
     */
    public function getPrevious()
    {
        $record = self::find()
            ->where('id<:current_id AND category_id=:cat_id')
            ->addParams([':current_id'=>$this->id, ':cat_id'=>$this->category_id])
            ->limit(1)
            ->orderBy('id DESC')
            ->one();
        if($record!==null)
            return $record;
        return null;
    }


    /**
     * @inheritdoc
     */
    public function getFrontendViewLink()
    {
        return ['/content/article/view', 'catslug' => $this->category->slug, 'slug' => $this->slug];
    }

}
