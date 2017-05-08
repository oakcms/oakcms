<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\form_builder\models;

use app\components\ActiveQuery;
use Yii;
use yii\behaviors\SluggableBehavior;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\validators\UniqueValidator;

/**
 * This is the model class for table "{{%form_builder_field}}".
 *
 * @property integer $id
 * @property integer $form_id
 * @property integer $sort
 * @property string $type
 * @property string $label
 * @property string $slug
 * @property string $options
 * @property string $roles
 * @property string $data
 */
class FormBuilderField extends \app\components\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%form_builder_field}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'slug' => [
                'class' => SluggableBehavior::className(),
                'attribute' => 'label',
                'slugAttribute' => 'slug',
                'ensureUnique' => true,
                'immutable' => true,
                'uniqueValidator' => [
                    'filter' => function ($query) {
                        /** @var $query ActiveQuery */
                        if($this->isNewRecord) {
                            return $query->andWhere('form_id <> :form_id', ['form_id' => $this->form_id]);
                        } else {
                            return $query->andWhere('form_id = :form_id AND id <> :id', ['form_id' => $this->form_id, 'id' => $this->id]);
                        }
                    }
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
            [['form_id', 'label', 'slug'], 'required'],
            [['form_id', 'sort'], 'integer'],
            [['options', 'roles', 'data'], 'string'],
            [['type', 'label', 'slug'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'       => Yii::t('form_builder', 'ID'),
            'form_id'  => Yii::t('form_builder', 'Form ID'),
            'sort'     => Yii::t('form_builder', 'sort'),
            'type'     => Yii::t('form_builder', 'Type'),
            'label'    => Yii::t('form_builder', 'Label'),
            'slug'     => Yii::t('form_builder', 'Slug'),
            'options'  => Yii::t('form_builder', 'Options'),
            'roles'    => Yii::t('form_builder', 'Roles'),
            'data'     => Yii::t('form_builder', 'Data'),
        ];
    }

    public static function getJson($form_id) {
        $fields = self::find()->select(['data'])->where(['form_id' => $form_id])->asArray()->all();
        $return = [];
        foreach ($fields as $field) {
            $return[] = Json::decode(ArrayHelper::getValue($field, 'data', []));
        }
        return Json::encode($return);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->slug = str_replace('-', '_', $this->slug);
            return true;
        } else {
            return false;
        }
    }

    public function FieldProcess(&$fieldData, &$modelFormField) {
        if (is_file($fileL = Yii::getAlias('@app/modules/form_builder/views/backend/forms/field/' . $this->type . '/field.php'))) {
            $fieldData = require $fileL;

            // Перебираємо ключі
            $attributes = [];
            foreach (ArrayHelper::getValue($fieldData, 'attributes', []) as $k => $attribute) {
                $attributes[] = $k;
            }

            $modelFormField = new FormBuilder($attributes);
            foreach (ArrayHelper::getValue($fieldData, 'rules', []) as $rule) {
                $modelFormField->addRule(
                    ArrayHelper::getValue($rule, 0),
                    ArrayHelper::getValue($rule, 1),
                    ArrayHelper::getValue($rule, 2, [])
                );
            }

            $modelFormField->addRule('name', 'match', ['pattern' => '/(^|.*\])([\w\.]+)(\[.*|$)/']);
            $modelFormField->addRule('name', 'unique', [
                'targetClass' => FormBuilderField::className(),
                'targetAttribute' => 'slug',
                'filter' => function ($query) {
                    /** @var $query ActiveQuery */
                    if($this->isNewRecord) {
                        return $query->andWhere('form_id = :form_id', ['form_id' => $this->form_id]);
                    } else {
                        return $query->andWhere('form_id = :form_id AND id <> :id', ['form_id' => $this->form_id, 'id' => $this->id]);
                    }
                }
            ]);

            $modelFormField->load(['FormBuilder' => Json::decode($this->data)]);

            if ($modelFormField->load(Yii::$app->request->post()) && $modelFormField->validate()) {
                $saveData = [];
                foreach ($modelFormField->attributes() as $attribute) {
                    $saveData[$attribute] = $modelFormField->{$attribute};
                }
                $this->label = ArrayHelper::getValue($saveData, 'label');
                $this->slug = ArrayHelper::getValue($saveData, 'name');
                $this->data = Json::encode($saveData);
                if ($this->save()) {
                    Yii::$app->getSession()->setFlash(
                        'success',
                        Yii::t('form_builder', '{fieldName} saved.', [
                            'fieldName' => $fieldData['title']
                        ])
                    );
                }
            }

            if (Json::decode($this->data)) {
                foreach (Json::decode($this->data) as $k=>$item) {
                    $fieldData['attributes'][$k]['options']['value'] = $item;
                }
            }
        }
    }
}
