<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\form_builder\models;

use app\helpers\StringHelper;
use app\modules\form_builder\widgets\ShortCode;
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubmissions()
    {
        return $this->hasMany(FormBuilderSubmission::className(), ['form_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFields()
    {
        return $this->hasMany(FormBuilderField::className(), ['form_id' => 'id'])->orderBy(['sort' => SORT_ASC]);
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

            switch ($field->type) {
                case 'reCaptcha':
                    $element = '<div class="g-recaptcha" data-sitekey="'. ArrayHelper::getValue(json_decode($field->data), 'recaptcha_api_key') .'"></div>';
                    break;
                case 'button':
                    $element = Html::button(ArrayHelper::getValue($fieldData, 'value', 'Send'), array_merge(
                        $attr,
                        [
                            'type'  => ArrayHelper::getValue($fieldData, 'type', 'text'),
                            'class' => ArrayHelper::getValue($fieldData, 'cssClass'),
                        ]
                    ));
                    break;
                case 'radioList':
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
                    break;
                case 'dropdownList':
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
                    break;
                case 'fileInput':
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
                    break;
                case 'checkbox':
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
                    break;
                default:
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
                    break;
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

    /**
     * @return mixed
     */
    public static function submitForm() {
        if (Yii::$app instanceof \yii\web\Application) {
            $request = Yii::$app->request;
            if (
                ($formBuilder = $request->post('FormBuilder')) &&
                ($formId = ArrayHelper::getValue($formBuilder, 'formId'))
            ) {
                $model = FormBuilderForms::find()
                    ->where(['id' => $formId, 'status' => FormBuilderForms::STATUS_PUBLISHED])
                    ->one();

                $models = ShortCode::getForm($model);

                /** @var FormBuilderForms $formModel */
                $formModel = $models['formModel'];
                $success = [];
                if ($formModel->load(Yii::$app->request->post()) && $formModel->validate()) {
                    $format = Yii::$app->request->get('format');

                    $submission = new FormBuilderSubmission();
                    $submission->form_id = $model->id;
                    $submission->created = time();
                    $submission->ip = Yii::$app->request->userIP;
                    $submission->status = FormBuilderSubmission::STATUS_DRAFT;

                    $attachments = [];
                    foreach ($models['model']->fields as $field) {
                        if ($field->type == 'fileInput' && ($attachment = UploadedFile::getInstance($formModel, $field->slug))) {
                            $fieldData = Json::decode($field->data);
                            $uploadsPath = Yii::getAlias($fieldData['destination']);
                            $urlDownload = Yii::getAlias('@webroot');
                            $urlDownload = Yii::getAlias('@web' . str_replace($urlDownload, '', $uploadsPath));
                            if (!file_exists($uploadsPath)) {
                                mkdir($uploadsPath, 0777, true);
                            }
                            $uniqid = uniqid();
                            $fileName = $uploadsPath . '/' . $uniqid . '.' . $attachment->extension;
                            $fileDownload = $urlDownload . '/' . $uniqid . '.' . $attachment->extension;
                            $attachment->saveAs($fileName);
                            $attributes = $formModel->attributes;
                            $attributes[$field->slug] = $fileDownload;
                            $formModel->attributes = $attributes;
                            foreach ($fieldData['attach_file_to'] as $datum) {
                                $attachments[$datum][] = $fileName;
                            }
                        }
                    }

                    $submission->data = Json::encode($formModel->attributes);
                    if ($submission->save()) {
                        if (ArrayHelper::getValue($model->data, 'email.sendToUser'))
                            if (ArrayHelper::getValue($model->data, 'email.sendToUser')) {
                                if (($field_id = ArrayHelper::getValue($model->data, 'email.userEmail')) && ($field = FormBuilderField::findOne($field_id))) {
                                    $userEmail = $formModel->{$field->slug};
                                    $mailer = Yii::$app->mailer->compose()
                                        ->setFrom(getenv('ROBOT_EMAIL'))
                                        ->setTo($userEmail)
                                        ->setSubject(ArrayHelper::getValue($model->data, 'email.userEmailSubject'))
                                        ->setHtmlBody($model->parseContent($formModel, 'email.userEmailContent'));

                                    foreach ($attachments as $key => $attachment) {
                                        if ($key == 'useremail') {
                                            foreach ($attachment as $filename) {
                                                $mailer->attach($filename);
                                            }
                                        }
                                    }

                                    $mailer->send();
                                }
                            }

                        if (ArrayHelper::getValue($model->data, 'email.sendToAdmin')) {
                            $email = ArrayHelper::getValue($model->data, 'email.adminEmail', getenv('ADMIN_EMAIL'));
                            $email = $model->parseFields($formModel, $email);
                            $email = str_replace(' ', '', $email);
                            $emails = explode(',', $email);

                            $mailer = Yii::$app->mailer
                                ->compose()
                                ->setFrom(getenv('ROBOT_EMAIL'))
                                ->setTo($emails)
                                ->setSubject(ArrayHelper::getValue($model->data, 'email.adminEmailSubject'))
                                ->setHtmlBody($model->parseContent($formModel, 'email.adminEmailContent'));

                            foreach ($attachments as $key => $attachment) {
                                if ($key == 'adminemail') {
                                    foreach ($attachment as $filename) {
                                        $mailer->attach($filename);
                                    }
                                }
                            }

                            $mailer->send();
                        }

                        if ($format == 'json') {
                            $success = ['success' => ArrayHelper::getValue($model->data, 'submission.content')];
                        } else {
                            Yii::$app->getSession()->setFlash('success', Yii::t('form_builder', 'Form submited.'));
                        }

                    } else {
                        if ($format == 'json') {
                            $success = ['error' => Yii::t('form_builder', 'Email not sending.')];
                        } else {
                            Yii::$app->getSession()->setFlash('danger', Yii::t('form_builder', 'Email not sending.'));
                        }
                    }

                    if ($format == 'json') {
                        return $success;
                    }

                    if (ArrayHelper::getValue($model->data, 'submission.after_submit') == 'thankyou') {
                        return ArrayHelper::getValue($model->data, 'submission.content');
                    } elseif (ArrayHelper::getValue($model->data, 'submission.after_submit') == 'redirect') {
                        Yii::$app->response->redirect(ArrayHelper::getValue($model->data, 'submission.after_submit_link'));
                    }
                }
            }
        }
        return false;
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

            /** @var $fields self::getFields() */
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
