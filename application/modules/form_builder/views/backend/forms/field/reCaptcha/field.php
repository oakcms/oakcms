<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

use kartik\builder\BaseForm;

return [
    'name' => 'reCaptcha',
    'title' => Yii::t('form_builder', 'reCaptcha'),
    'icon' => '<i class="fa fa-lock" aria-hidden="true"></i>',
    'render' => __DIR__ . '/render.php',
    'attributes' => [
        'name' => [
            'type' => BaseForm::INPUT_TEXT,
        ],
        'label' => [
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
        'recaptcha_api_key' => [
            'type' => BaseForm::INPUT_TEXT,
        ],
        'recaptcha_api_key_secret' => [
            'type' => BaseForm::INPUT_TEXT,
        ],
    ],
    'rules' => [
        [['label', 'name', 'recaptcha_api_key', 'recaptcha_api_key_secret'], 'required'],
        [['additionalAttributes', 'label', 'helpText'], 'string'],
        [['cssClass', 'name'], 'string', ['max' => 100]]
    ]
];
