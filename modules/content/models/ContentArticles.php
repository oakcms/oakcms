<?php

namespace app\modules\content\models;

use app\modules\admin\models\ModulesModules;
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
 * @property integer $create_user_id
 * @property integer $update_user_id
 * @property integer $published_at
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $status
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

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
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
            'trans' => [
                'class' => TranslateableBehavior::className(),
                'translationAttributes' => [
                    'slug', 'title', 'content', 'link', 'meta_title', 'meta_keywords', 'meta_description', 'settings', 'image'
                ]
            ],
        ];
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
            [['create_user_id', 'update_user_id','status', 'comment_status', 'access_type', 'category_id'], 'integer'],
            [['create_user_ip'], 'string', 'max' => 20],
            //[['settings'], 'string'],
            [['title', 'link', 'meta_title', 'meta_keywords', 'meta_description'], 'string', 'max' => 255],
            [['published_at'], 'filter', 'filter' => 'strtotime', 'skipOnEmpty' => true],
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
            'category_id' => Yii::t('content', 'Category ID'),
        ];
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->settingsAfterLanguage();
    }

    public function settingsAfterLanguage()
    {
        if($this->settings !== '' AND $this->settings !== null) {
            $this->settings = json_decode($this->settings, true);
        } else {
            $this->settings = ModulesModules::getDefaultSettings('content');
        }
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($this->create_user_ip == '') {
                $this->create_user_ip = Yii::$app->request->userIP;
            }

            if(!$this->settings || !is_array($this->settings)){
                $this->settings = ModulesModules::getDefaultSettings('content');
            }
            $this->settings = json_encode($this->settings);

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
    }

    public function setSetting($settings)
    {
        $newSettings = [];
        foreach ($this->settings as $key => $value) {

            $newSettings[$key] = [
                'value' => is_bool($value['value']) ? ($settings[$key] ? true : false) : ($settings[$key] ? $settings[$key] : ''),
                'type' => $value['type']
            ];
        }
        $this->settings = $newSettings;
    }
}
