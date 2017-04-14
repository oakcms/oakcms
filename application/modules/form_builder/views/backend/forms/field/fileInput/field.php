<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.5
 */

use kartik\builder\BaseForm;

return [
    'name' => 'fileInput',
    'title' => Yii::t('form_builder', 'File Input'),
    'icon' => '<i class="fa fa-file" aria-hidden="true"></i>',
    'render' => __DIR__ . '/render.php',
    'attributes' => [
        'name' => [
            'type' => BaseForm::INPUT_TEXT,
        ],
        'label' => [
            'type' => BaseForm::INPUT_TEXT,
        ],
        'value' => [
            'type' => BaseForm::INPUT_TEXT,
        ],
        'cssClass' => [
            'type' => BaseForm::INPUT_TEXT,
            'options' => [
                'value' => 'form-control'
            ]
        ],
        'additionalAttributes' => [
            'type' => BaseForm::INPUT_WIDGET,
            'widgetClass' => \app\modules\admin\widgets\AceEditor::className(),
            'options' => [
                'mode' => 'yaml',
                'containerOptions' => [
                    'style' => 'height: 150px'
                ]
            ]
        ],
        'helpText' => [
            'type' => BaseForm::INPUT_TEXT,
        ],
        'fileSize' => [
            'type' => BaseForm::INPUT_TEXT,
            'options' => [
                'onkeyup' => 'javascript:this.value=this.value.replace(/[^0-9]/g, \'\');'
            ]
        ],
        'extNames' => [
            'type' => BaseForm::INPUT_WIDGET,
            'widgetClass' => \dosamigos\selectize\SelectizeTextInput::className(),
            'options' => [
                'clientOptions' => [
                    'create' => true
                ]
            ]
        ],
        'destination' => [
            'type' => BaseForm::INPUT_TEXT,
            'options' => [
                'value' => '@webroot/uploads/form_builder'
            ]
        ],
        'attach_file_to' => [
            'type' => BaseForm::INPUT_DROPDOWN_LIST,
            'items' => [
                'useremail' => 'User Email',
                'adminemail' => 'Admin Email'
            ],
            'options' => [
                'multiple' => 'multiple'
            ]
        ],
        'required' => [
            'type' => BaseForm::INPUT_WIDGET,
            'widgetClass' => '\oakcms\bootstrapswitch\Switcher',
        ]
    ],
    'rules' => [
        [['label', 'name'], 'required'],
        [['additionalAttributes', 'label', 'helpText', 'value', 'fileSize', 'extNames', 'destination'], 'string'],
        [['cssClass', 'name'], 'string', ['max' => 100]],
        [['attach_file_to'], 'safe'],
        [['required'], 'integer'],
    ]
];
