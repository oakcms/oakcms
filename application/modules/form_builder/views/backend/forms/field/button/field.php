<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

use kartik\builder\BaseForm;

return [
    'name' => 'button',
    'title' => Yii::t('form_builder', 'Button'),
    'icon' => '<i class="fa fa-bold" aria-hidden="true"></i>',
    'render' => __DIR__ . '/render.php',
    'attributes' => [
        'type' => [
            'type' => BaseForm::INPUT_DROPDOWN_LIST,
            'items' => [
                'submit' => 'Submit',
                'reset' => 'Reset',
                'button' => 'Button'
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
            'options' => ['value' => 'Send'],
        ],
        'cssClass' => [
            'type' => BaseForm::INPUT_TEXT,
            'options' => ['value' => 'btn btn-default']
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
        ]
    ],
    'rules' => [
        [['type', 'value', 'name', 'label'], 'required'],
        [['additionalAttributes', 'label'], 'string'],
        ['cssClass', 'string', ['max' => 100]],
    ]
];
