<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

use kartik\builder\BaseForm;

return [
    'name' => 'textarea',
    'title' => Yii::t('form_builder', 'Text Area'),
    'icon' => '<i class="fa fa-pencil-square-o" aria-hidden="true"></i>',
    'render' => __DIR__ . '/render.php',
    'attributes' => [
        'type' => [
            'type' => BaseForm::INPUT_HIDDEN,
            'label' => false,
            'options' => ['value' => BaseForm::INPUT_TEXTAREA]
        ],
        'label' => [
            'type' => BaseForm::INPUT_TEXT,
        ],
        'name' => [
            'type' => BaseForm::INPUT_TEXT,
        ],
        'value' => [
            'type' => BaseForm::INPUT_TEXT,
        ],
        'cssClass' => [
            'type' => BaseForm::INPUT_TEXT,
            'options' => ['value' => 'form-control']
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
        'required' => [
            'type' => BaseForm::INPUT_WIDGET,
            'widgetClass' => '\oakcms\bootstrapswitch\Switcher',
        ]
    ],
    'rules' => [
        [['type', 'label', 'name'], 'required'],
        [['additionalAttributes', 'label', 'helpText', 'value'], 'string'],
        [['name', 'cssClass'], 'string', ['max' => 100]],
        ['required', 'integer'],
    ]
];
