<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

use kartik\builder\BaseForm;

return [
    'name' => 'textInput',
    'title' => Yii::t('form_builder', 'Text Input'),
    'icon' => '<i class="fa fa-text-width" aria-hidden="true"></i>',
    'render' => __DIR__ . '/render.php',
    'attributes' => [
        'type' => [
            'type' => BaseForm::INPUT_DROPDOWN_LIST,
            'items' => [
                'text' => 'Text',
                'password' => 'Password',
                'email' => 'Email',
                'color' => 'Color',
                'tel' => 'Telephone'
            ]
        ],
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
            'type' => BaseForm::INPUT_TEXTAREA,
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
        [['cssClass', 'name'], 'string', ['max' => 100]],
        [['required'], 'integer'],
    ]
];
