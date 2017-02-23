<?php

namespace app\modules\content\models;

use app\components\CategoryModel;
use app\modules\field\behaviors\AttachFields;
use creocoder\nestedsets\NestedSetsBehavior;
use Yii;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use dosamigos\translateable\TranslateableBehavior;

/**
 * This is the model class for table "{{%content_category}}".
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property integer $status
 * @property string $layout
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $tree
 * @property integer $lft
 * @property integer $rgt
 * @property integer $depth
 * @property string $name
 * @property string $icon
 * @property integer $icon_type
 * @property integer $active
 * @property integer $selected
 * @property integer $disabled
 * @property integer $readonly
 * @property integer $visible
 * @property integer $collapsed
 * @property integer $movable_u
 * @property integer $movable_d
 * @property integer $movable_l
 * @property integer $movable_r
 * @property integer $removable
 * @property integer $removable_all
 */

class ContentCategory extends CategoryModel
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
            'slug' => function($model) {
                return $model->slug;
            },
            'content' => function($model) {
                return $model->content;
            },
            'meta_title' => function($model) {
                return $model->meta_title;
            },
            'meta_keywords' => function($model) {
                return $model->meta_keywords;
            },
            'meta_description' => function($model) {
                return $model->meta_description;
            },
            'settings' => function($model) {
                return $model->settings;
            }
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return parent::behaviors() + [
            AttachFields::className(),
            TimestampBehavior::className(),
            [
                'class' => SluggableBehavior::className(),
                'attribute' => 'title',
                'immutable' => true
            ],
            'trans' => [
                'class' => TranslateableBehavior::className(),
                'translationAttributes' => [
                    'slug', 'title', 'content', 'meta_title', 'meta_keywords', 'meta_description', 'settings'
                ]
            ]
        ];
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%content_category}}';
    }

    /**
     * @inheritdoc
     */

    public function rules()
    {
        return [
            ['title', 'required'],
            ['title', 'trim'],
            ['title', 'string', 'max' => 128],
            //['image', 'image'],
            [['slug'], 'string', 'max' => 150],
            [['content'], 'string'],
            [['layout', 'meta_title', 'meta_keywords', 'meta_description'], 'string', 'max' => 500],
            [
                ['slug'],
                'unique',
                'targetClass' => ContentCategoryLang::className(),
                'targetAttribute' => 'slug',
                'filter' => function ($query) {
                    /**
                     * @var $query ActiveQuery
                     */
                    $query->andWhere('content_category_id <> :a_id', ['a_id' => $this->id]);
                    return $query;
                }
            ],
            ['status', 'default', 'value' => self::STATUS_ON],
            [['status', 'created_at', 'updated_at', 'tree', 'lft', 'rgt', 'depth', 'icon_type', 'active', 'selected', 'disabled', 'readonly', 'visible', 'collapsed', 'movable_u', 'movable_d', 'movable_l', 'movable_r', 'removable', 'removable_all'], 'integer'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('content', 'ID'),
            'slug' => Yii::t('admin', 'Slug'),
            'title' => Yii::t('admin', 'Title'),
            'status' => Yii::t('content', 'Status'),
            'created_at' => Yii::t('content', 'Created At'),
            'updated_at' => Yii::t('content', 'Updated At'),
            'tree' => Yii::t('content', 'Tree tree identifier'),
            'lft' => Yii::t('content', 'Nested set left property'),
            'rgt' => Yii::t('content', 'Nested set right property'),
            'depth' => Yii::t('content', 'Nested set level / depth'),
            'name' => Yii::t('content', 'The tree node name / label'),
            'icon' => Yii::t('content', 'The icon to use for the node'),
            'icon_type' => Yii::t('content', 'Icon Type: 1 = CSS Class, 2 = Raw Markup'),
            'active' => Yii::t('content', 'Whether the node is active (will be set to false on deletion)'),
            'selected' => Yii::t('content', 'Whether the node is selected/checked by default'),
            'disabled' => Yii::t('content', 'Whether the node is enabled'),
            'readonly' => Yii::t('content', 'Whether the node is read only (unlike disabled - will allow toolbar actions)'),
            'visible' => Yii::t('content', 'Whether the node is visible'),
            'collapsed' => Yii::t('content', 'Whether the node is collapsed by default'),
            'movable_u' => Yii::t('content', 'Whether the node is movable one position up'),
            'movable_d' => Yii::t('content', 'Whether the node is movable one position down'),
            'movable_l' => Yii::t('content', 'Whether the node is movable to the left (from sibling to parent)'),
            'movable_r' => Yii::t('content', 'Whether the node is movable to the right (from sibling to child)'),
            'removable' => Yii::t('content', 'Whether the node is removable (any children below will be moved as siblings before deletion)'),
            'removable_all' => Yii::t('content', 'Whether the node is removable along with descendants'),
            'parent' => Yii::t('content', 'Parent Category'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getFrontendViewLink()
    {
        return ['/content/category/view', 'slug' => $this->slug];
    }

    /**
     * @inheritdoc
     */
    public static function frontendViewLink($model)
    {
        return ['/content/category/view', 'slug' => $model['slug']];
    }

    /**
     * @inheritdoc
     */
    public function getBackendViewLink()
    {
        return ['/admin/content/category/update', 'id' => $this->id];
    }

    /**
     * @inheritdoc
     */
    public static function backendViewLink($model)
    {
        return ['/admin/content/category/update', 'id' => $model['id']];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(ContentArticles::className(), ['category_id' => 'id'])
            ->andWhere([ContentArticles::tableName().'.status' => ContentArticles::STATUS_PUBLISHED])
            ->orderBy([ContentArticles::tableName().'.status' => ContentArticles::STATUS_PUBLISHED]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslations()
    {
        return $this->hasMany(ContentCategoryLang::className(), ['content_category_id' => 'id']);
    }


    public function afterDelete()
    {
        parent::afterDelete(); // TODO: Change the autogenerated stub

        // Видалення перекладу
        foreach ($this->getTranslations()->all() as $translations) {
            $translations->delete();
        }
    }
}
