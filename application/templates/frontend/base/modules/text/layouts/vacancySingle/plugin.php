<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 16.09.2016
 * Project: osnovasite
 * File name: plugin.php
 */

return [
    'name' => 'vacancySingle',
    'title' => Yii::t('text', 'Vacancy Single'),
    'preview_image' => Yii::getAlias('@web').'/application/templates/frontend/base/modules/text/layouts/vacancySingle/preview.png',
    'viewFile' => '@app/templates/frontend/base/modules/text/layouts/vacancySingle/view.php',
    'settings' => [
        'cssClass' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'link' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'linkText' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'image' => [
            'type' => 'mediaInput',
            'value' => ''
        ],
        'imageAlt' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'content' => [
            'type' => 'textInput',
            'value' => ''
        ]
    ],
];
