<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 16.09.2016
 * Project: osnovasite
 * File name: plugin.php
 */

return [
    'name' => 'vacancyCustom',
    'title' => Yii::t('text', 'Vacancy Custom Home'),
    'preview_image' => Yii::getAlias('@web').'/application/templates/frontend/base/modules/text/layouts/vacancyCustom/preview.png',
    'viewFile' => '@app/templates/frontend/base/modules/text/layouts/vacancyCustom/view.php',
    'settings' => [
        'cssClass' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'title_1' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'link_1' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'image_1' => [
            'type' => 'mediaInput',
            'value' => ''
        ],
        'description_1' => [
            'type' => 'textarea',
            'value' => ''
        ],

        'title_2' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'link_2' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'image_2' => [
            'type' => 'mediaInput',
            'value' => ''
        ],
        'description_2' => [
            'type' => 'textarea',
            'value' => ''
        ],

        'title_3' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'link_3' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'image_3' => [
            'type' => 'mediaInput',
            'value' => ''
        ],
        'description_3' => [
            'type' => 'textarea',
            'value' => ''
        ],

        'title_4' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'link_4' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'image_4' => [
            'type' => 'mediaInput',
            'value' => ''
        ],
        'description_4' => [
            'type' => 'textarea',
            'value' => ''
        ],

        'title_5' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'link_5' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'image_5' => [
            'type' => 'mediaInput',
            'value' => ''
        ],
        'description_5' => [
            'type' => 'textarea',
            'value' => ''
        ],
    ],
];
