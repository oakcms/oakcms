<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.5
 */

use kartik\builder\BaseForm;

return [
    'name' => 'radioList',
    'title' => Yii::t('form_builder', 'Radio List'),
    'icon' => '<i class="fa fa-check-circle-o" aria-hidden="true"></i>',
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
        'items' => [
            'type' => BaseForm::INPUT_WIDGET,
            'widgetClass' => \app\modules\admin\widgets\AceEditor::className(),
            'options' => [
                'mode' => 'yaml',
                'containerOptions' => [
                    'style' => 'height: 150px'
                ]
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
        'required' => [
            'type' => BaseForm::INPUT_WIDGET,
            'widgetClass' => '\oakcms\bootstrapswitch\Switcher',
        ]
    ],
    'rules' => [
        [['name', 'label'], 'required'],
        [['additionalAttributes', 'value', 'label'], 'string'],
        [['name', 'label', 'items'], 'string', ['max' => 100]],
        [['required'], 'integer'],
    ]
];
