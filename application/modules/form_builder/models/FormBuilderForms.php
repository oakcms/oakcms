<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

namespace app\modules\form_builder\models;

use app\helpers\StringHelper;
use kartik\builder\Form;
use Yii;
use yii\behaviors\SluggableBehavior;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

/**
 * This is the model class for table "{{%form_builder_forms}}".
 *
 * @property integer $id
 * @property string  $title
 * @property string  $slug
 * @property integer $sort
 * @property integer $status
 * @property array   $data
 */
class FormBuilderForms extends \app\components\ActiveRecord
{
    const STATUS_PUBLISHED = 1;
    const STATUS_DRAFT = 0;

    /**
     * @var array
     */
    public $errors = [];

    /**
     * @var array
     */
    public $fieldsErrors = [];

    /**
     * @var array
     */
    public $fieldsAttributes = [];

    /**
     * @var object
     */
    public $modelForm = null;

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
                'class'         => SluggableBehavior::className(),
                'attribute'     => 'title',
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

    public function getSubmissions()
    {
        return $this->hasMany(FormBuilderSubmission::className(), ['form_id' => 'id']);
    }

    public function getFields()
    {
        return $this->hasMany(FormBuilderField::className(), ['form_id' => 'id'])
            ->orderBy(['sort' => SORT_ASC]);
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

    public function renderForm($form)
    {
        $data = $this->data;
        $html = ArrayHelper::getValue($data, 'design.html', '');
        $css = ArrayHelper::getValue($data, 'design.css');
        $javascript = ArrayHelper::getValue($data, 'design.javascript');
        $attributesForm = [];

        $view = Yii::$app->getView();
        $view->registerCss($css);
        $view->registerJS($javascript, View::POS_END);
        foreach ($this->getFields()->all() as $field) {
            $fieldData = Json::decode($field->data);

            $additionalAttr = ArrayHelper::getValue($fieldData, 'additionalAttributes');

            $attr = [];
            if($additionalAttr) {
                $attr = \Symfony\Component\Yaml\Yaml::parse($additionalAttr);
            }

            if ($field->type == 'button') {
                $element = Html::button(ArrayHelper::getValue($fieldData, 'value', 'Send'), array_merge(
                    $attr,
                    [
                        'type'  => ArrayHelper::getValue($fieldData, 'type', 'text'),
                        'class' => ArrayHelper::getValue($fieldData, 'cssClass'),
                    ]
                ));
            } elseif($field->type == 'radioList') {
                $element = \app\modules\admin\widgets\Html::settingField(
                    $field->slug,
                    [
                        'type'     => $field->type,
                        'value'    => ArrayHelper::getValue($fieldData, 'value', ''),
                        'options'  => [
                            'elementOptions' => array_merge(
                                $attr,
                                [
                                    'class' => ArrayHelper::getValue($fieldData, 'cssClass', ''),
                                    'id'    => Html::getInputId($this->modelForm, $field->slug),
                                    'type'  => ArrayHelper::getValue($fieldData, 'type', 'text'),
                                ]
                            ),
                        ],
                        'items'    => \Symfony\Component\Yaml\Yaml::parse(ArrayHelper::getValue($fieldData, 'items')),
                        'hint'     => ArrayHelper::getValue($fieldData, 'helpText', ''),
                        'template' => '{element}',
                    ],
                    'form_builder',
                    'FormBuilder'
                );
                $attributesForm[$field->slug] = [
                    'type'  => $field->type,
                    'items' => \Symfony\Component\Yaml\Yaml::parse(ArrayHelper::getValue($fieldData, 'items'))
                ];
            } elseif($field->type == 'dropdownList') {
                $element = \app\modules\admin\widgets\Html::settingField(
                    $field->slug,
                    [
                        'type'     => $field->type,
                        'value'    => ArrayHelper::getValue($fieldData, 'value', ''),
                        'items'    => \Symfony\Component\Yaml\Yaml::parse(ArrayHelper::getValue($fieldData, 'items')),
                        'options'  => [
                            'elementOptions' => array_merge(
                                $attr,
                                [
                                    'class' => ArrayHelper::getValue($fieldData, 'cssClass', ''),
                                    'id'    => Html::getInputId($this->modelForm, $field->slug),
                                    'type'  => ArrayHelper::getValue($fieldData, 'type', 'text'),
                                ]
                            ),
                        ],
                        'hint'     => ArrayHelper::getValue($fieldData, 'helpText', ''),
                        'template' => '{element}',
                    ],
                    'form_builder',
                    'FormBuilder'
                );
                $attributesForm[$field->slug] = [
                    'type' => $field->type,
                    'items' => \Symfony\Component\Yaml\Yaml::parse(ArrayHelper::getValue($fieldData, 'items'))
                ];
            } elseif($field->type == 'fileInput') {
                $element = \app\modules\admin\widgets\Html::fileInput(
                    "FormBuilder[{$field->slug}]",
                    null,
                    array_merge(
                        $attr,
                        [
                            'class' => ArrayHelper::getValue($fieldData, 'cssClass', ''),
                            'id'    => Html::getInputId($this->modelForm, $field->slug),
                        ]
                    )
                );
                $attributesForm[$field->slug] = [
                    'type' => $field->type,
                    'items' => \Symfony\Component\Yaml\Yaml::parse(ArrayHelper::getValue($fieldData, 'items'))
                ];
            } elseif($field->type == 'checkbox') {
                $uniqeId = uniqid($field->slug.'_');
                $element = Html::hiddenInput("FormBuilder[{$field->slug}]", 0).
                    Html::checkbox(
                    "FormBuilder[{$field->slug}]",
                    ArrayHelper::getValue($fieldData, 'value', 0),
                    ['id' => $uniqeId]
                ). Html::label($field->label, $uniqeId);
                $attributesForm[$field->slug] = [
                    'type' => $field->type,
                ];
            } else {
                $element = \app\modules\admin\widgets\Html::settingField(
                    $field->slug,
                    [
                        'type'     => $field->type,
                        'value'    => ArrayHelper::getValue($fieldData, 'value', ''),
                        'options'  => [
                            'elementOptions' => array_merge(
                                $attr,
                                [
                                    'class' => ArrayHelper::getValue($fieldData, 'cssClass', ''),
                                    'id'    => Html::getInputId($this->modelForm, $field->slug),
                                    'type'  => ArrayHelper::getValue($fieldData, 'type', 'text'),
                                ]
                            ),
                        ],
                        'hint'     => ArrayHelper::getValue($fieldData, 'helpText', ''),
                        'template' => '{element}',
                    ],
                    'form_builder',
                    'FormBuilder'
                );
                $attributesForm[$field->slug] = [
                    'type' => $field->type
                ];
            }

            $html = str_replace(
                [
                    '{' . $field->slug . ':label}',
                    '{' . $field->slug . ':body}',
                    '{' . $field->slug . ':description}',
                    '{' . $field->slug . ':validation}',
                ],
                [
                    ArrayHelper::getValue($fieldData, 'label', ''),
                    $element,
                    ArrayHelper::getValue($fieldData, 'helpText', ''),
                ],
                $html
            );
        }

        if(count($attributesForm)) {
            Form::widget([
                'model'      => $this->modelForm,
                'form'       => $form,
                'columns'    => 2,
                'attributes' => $attributesForm,
            ]);
        }

        $html = str_replace(
            [
                '{form:id}',
                '{form:title}',
                '{error}',
            ],
            [
                $this->id,
                $this->title,
                '',
            ],
            $html
        );
        echo $html;
    }

    public function setAttributesFields($attributes)
    {
        $this->fieldsAttributes = $attributes;
    }

    public function setModelForm($model)
    {
        $this->modelForm = $model;
    }

    public function load($data, $formName = null)
    {
        if (parent::load($data, $formName)) {
            $options = [];

            $options['design'] = ArrayHelper::getValue($data, 'design', []);
            $options['submission'] = ArrayHelper::getValue($data, 'submission', []);
            $options['email'] = ArrayHelper::getValue($data, 'email');

            $this->data = Json::encode($options);
        } else {
            return false;
        }

        return true;
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->data = Json::decode($this->data);
        $attributes = [];
        foreach ($this->getFields()->all() as $k => $attribute) {
            $attributes[$attribute->slug] = $attribute->label;
        }
        $this->fieldsAttributes = $attributes;
    }

    public function parseContent($formModel, $content) {

        $html = ArrayHelper::getValue($this->data, $content, '');

        return $this->parseFields($formModel, $html);
    }
    public function parseFields($formModel, $html) {
        foreach ($this->getFields()->all() as $field) {
            $html = str_replace(
                [
                    '{' . $field->slug . ':label}',
                    '{' . $field->slug . ':value}'
                ],
                [
                    ArrayHelper::getValue($formModel, 'attributeLabels.'.$field->slug, ''),
                    ArrayHelper::getValue($formModel, $field->slug, ''),
                ],
                $html
            );
        }
        return $html;
    }

    /**
     * Batch copy items to a new category or current.
     *
     * @param   array $ids An array of row IDs.
     *
     * @return boolean.
     */
    public static function batchCopy($ids)
    {

        while (!empty($ids)) {
            $id = array_shift($ids);


            $model = self::findOne($id);
            $fields = $model->fields;
            $model->id = null;
            $model->title = StringHelper::increment($model->title);
            $model->slug = StringHelper::increment($model->slug, 'dash');
            $model->data = Json::encode($model->data);
            $model->isNewRecord = true;
            if ($model->save()) {
                foreach ($fields as $field) {
                    /** @var $field FormBuilderField */
                    $field->form_id = $model->id;
                    $field->id = null;
                    $field->isNewRecord = true;
                    $field->save();
                }
            }
        }

        return true;
    }

    public function afterDelete()
    {
        parent::afterDelete();

        foreach ($this->getSubmissions()->all() as $submission) {
            $submission->delete();
        }

        foreach ($this->getFields()->all() as $field) {
            $field->delete();
        }
    }
}
