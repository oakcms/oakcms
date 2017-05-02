<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.5
 */

use kartik\builder\BaseForm;

return [
    'name' => 'checkbox',
    'title' => Yii::t('form_builder', 'Check Box'),
    'icon' => '<i class="fa fa-check-square-o" aria-hidden="true"></i>',
    'render' => __DIR__ . '/render.php',
    'attributes' => [
        'name' => [
            'type' => BaseForm::INPUT_TEXT,
        ],
        'label' => [
            'type' => BaseForm::INPUT_TEXT,
        ],
        'value' => [
            'type' => BaseForm::INPUT_TEXTAREA,
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
        'required' => [
            'type' => BaseForm::INPUT_WIDGET,
            'widgetClass' => '\oakcms\bootstrapswitch\Switcher',
        ]
    ],
    'rules' => [
        [['label', 'name'], 'required'],
        [['additionalAttributes', 'label', 'helpText', 'value'], 'string'],
        [['cssClass', 'name'], 'string', ['max' => 100]],
        [['required'], 'integer'],
    ]
];
