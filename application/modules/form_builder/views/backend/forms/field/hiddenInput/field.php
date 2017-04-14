<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

use kartik\builder\BaseForm;

return [
    'name' => 'hiddenInput',
    'title' => Yii::t('form_builder', 'Hidden Input'),
    'icon' => '<i class="fa fa-eye-slash" aria-hidden="true"></i>',
    'render' => __DIR__ . '/render.php',
    'attributes' => [
        'type' => [
            'type' => BaseForm::INPUT_HIDDEN,
            'label' => false,
            'value' => 'hidden'
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
        'required' => [
            'type' => BaseForm::INPUT_WIDGET,
            'widgetClass' => '\oakcms\bootstrapswitch\Switcher',
        ]
    ],
    'rules' => [
        [['name', 'label'], 'required'],
        [['additionalAttributes', 'value', 'label'], 'string'],
        [['name', 'label'], 'string', ['max' => 100]],
        [['required'], 'integer'],
    ]
];
