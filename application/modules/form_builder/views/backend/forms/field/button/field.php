<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
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
            'type' => BaseForm::INPUT_TEXTAREA,
        ]
    ],
    'rules' => [
        [['type', 'value'], 'required'],
        [['additionalAttributes', 'label'], 'string'],
        ['cssClass', 'string', ['max' => 100]],
    ]
];
