<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\content\models;

use app\behaviors\NestedSetsBehavior;
use app\modules\content\models\query\ContentPagesQuery;
use app\modules\field\behaviors\AttachFields;
use app\modules\text\api\Text;
use Yii;
use app\components\ActiveQuery;
use app\components\ActiveRecord;
use app\modules\admin\components\behaviors\SettingModel;
use app\components\behaviors\TranslateableBehavior;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Url;

/**
 * This is the model class for table "{{%content_pages}}".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $layout
 * @property string $title
 * @property string $slug
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $ordering
 * @property integer $lft
 * @property integer $rgt
 * @property integer $level
 */
class ContentPages extends ActiveRecord
{
    const STATUS_PUBLISHED = 1;
    const STATUS_DRAFT = 0;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%content_pages}}';
    }

    /**
     * @inheritdoc
     * @return ContentPagesQuery
     */
    public static function find()
    {
        return new ContentPagesQuery(get_called_class());
    }

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
            'description' => function($model) {
                return $model->description;
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
            'background_image' => function($model) {
                return $model->background_image;
            },
            'settings' => function($model) {
                return $model->settings;
            },
            'parent' => function($model) {
                if($model->parent) {
                    $parent = $model->parent;
                    return [
                        'title' => $parent->title,
                        'link' => Url::to($parent->getFrontendViewLink())
                    ];
                }
                return false;
            },
            'fields' => function($model) {
                $return = [];
                foreach ($model->getFields() as $field) {
                    if($field->type == 'textBlock') {
                        $return[$field->slug] = Text::get((int)$model->getField($field->slug), true);
                    } else {
                        $return[$field->slug] = $model->getField($field->slug);
                    }
                }
                return $return;
            }
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
            NestedSetsBehavior::className(),
            AttachFields::className(),
            [
                'class'     => SluggableBehavior::className(),
                'attribute' => 'title',
            ],
            'sortable' => [
                'class' => \kotchuprik\sortable\behaviors\Sortable::className(),
                'query' => self::find(),
                'orderAttribute' => 'ordering'
            ],
            'trans' => [
                'class' => TranslateableBehavior::className(),
                'translationAttributes' => [
                    'slug', 'title', 'content', 'description', 'meta_title', 'meta_keywords', 'meta_description', 'settings'
                ]
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content', 'status'], 'required'],
            [['status', 'created_at', 'parent_id', 'lft', 'rgt', 'level', 'ordering', 'updated_at'], 'integer'],
            [['meta_title', 'meta_keywords', 'meta_description', 'layout'], 'string', 'max' => 255],
            [['title', 'description', 'settings', 'background_image'], 'string'],
            ['parent_id', 'default', 'value' => 0],
            [['slug'], 'string', 'max' => 150],
            [
                ['slug'],
                'unique',
                'targetClass' => ContentPagesLang::className(),
                'targetAttribute' => 'slug',
                'filter' => function ($query) {
                    /**
                     * @var $query ActiveQuery
                     */
                    $query->joinWith(['page'])
                        ->andWhere(ContentPagesLang::tableName().'.content_pages_id <> :a_id', ['a_id' => $this->id]);

                    /*
                    if ($parent = self::findOne($this->parent_id)) {
                        $query->andWhere(self::tableName().'.lft>=:lft AND '.self::tableName().'.rgt<=:rgt AND '.self::tableName().'.level=:level', [
                            'lft'      => $parent->lft,
                            'rgt'      => $parent->rgt,
                            'level'    => $parent->level + 1
                        ]);
                    }
                    */

                    return $query;
                }
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('content', 'ID'),
            'parent_id' => Yii::t('content', 'Parent Category ID'),
            'layout' => Yii::t('content', 'Layout'),
            'status' => Yii::t('content', 'Status'),
            'created_at' => Yii::t('content', 'Created At'),
            'updated_at' => Yii::t('content', 'Updated At'),
        ];
    }

    public function saveNode($runValidation = true, $attributes = null)
    {
        if ($this->getIsNewRecord()) {
            // если parent_id не задан, то ищем корневой элемент
            if ($parent = $this->parent_id ? self::findOne($this->parent_id) : self::find()->roots()->one()) {
                $this->parent_id = $parent->id;

                return $this->appendTo($parent, $runValidation, $attributes);
            } else {
                // если рутового элемента не существует, то сохраняем модель как корневую
                return $this->makeRoot($runValidation, $attributes);
            }
        }

        // модель перемещена в другую модель

        if (
            $this->getOldAttribute('parent_id') != $this->parent_id &&
            $newParent = $this->parent_id ? self::findOne($this->parent_id) : self::find()->roots()->one()
        ) {
            $this->parent_id = $newParent->id;

            return $this->appendTo($newParent, $runValidation, $attributes);
        }
        // просто апдейт
        return $this->save($runValidation, $attributes);
    }


    public function getTranslations()
    {
        return $this->hasMany(ContentPagesLang::className(), ['content_pages_id' => 'id']);
    }

    /**
     * @return array
     */
    public static function statusLabels($status = false) {
        $statuses = [
            self::STATUS_PUBLISHED => Yii::t('admin', 'Published'),
            self::STATUS_DRAFT => Yii::t('admin', 'Unpublished'),
        ];
        if($status !== false) {
            return $statuses[$status];
        } else {
            return $statuses;
        }
    }

    public function getStatusLabel() {
        return self::statusLabels($this->status);
    }

    /**
     * @return ContentPages
     */
    public function getParent()
    {
        return $this->hasOne(self::className(), ['id' => 'parent_id']);
    }

    /**
     * @inheritdoc
     */
    public function getFrontendViewLink()
    {
        return ['/content/page/view', 'slug' => $this->slug];
    }

    /**
     * @inheritdoc
     */
    public static function frontendViewLink($model)
    {
        return ['/content/page/view', 'slug' => $model['slug']];
    }

    /**
     * @inheritdoc
     */
    public function getBackendViewLink()
    {
        return ['/admin/content/page/view', 'id' => $this->id];
    }

    /**
     * @inheritdoc
     */
    public static function backendViewLink($model)
    {
        return ['/admin/content/page/view', 'id' => $model['id']];
    }

    public function afterDelete()
    {
        parent::afterDelete(); // TODO: Change the autogenerated stub

        // Видалення перекладу
        foreach ($this->getTranslations()->all() as $translations) {
            $translations->delete();
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub

        if (array_key_exists('ordering', $changedAttributes)) {
            $this->ordering ? $this->parent->reorderNode('ordering') : $this->parent->reorderNode('lft');
        }
    }
}
