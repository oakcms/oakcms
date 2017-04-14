<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 16.09.2016
 * Project: osnovasite
 * File name: plugin.php
 */

return [
    'name' => 'footer',
    'title' => Yii::t('text', 'Footer'),
    'preview_image' => Yii::getAlias('@web').'/application/templates/frontend/base/modules/text/layouts/footer/preview.png',
    'viewFile' => '@app/templates/frontend/base/modules/text/layouts/footer/view.php',
    'settings' => [
        'cssClass' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'logoTitle' => [
            'type' => 'textInput',
            'value' => 'Am lingwista'
        ],
        'logoDescription' => [
            'type' => 'textInput',
            'value' => 'Трудоустройство за границей'
        ],
        'menu' => [
            'type' => 'menuType',
            'value' => ''
        ],
        'country1' => [
            'type' => 'textInput',
            'value' => 'УКРАИНА'
        ],
        'address1' => [
            'type' => 'textInput',
            'value' => 'г. Ровно, ул. Кн.Ольги,5'
        ],
        'telephone1_1' => [
            'type' => 'textInput',
            'value' => '+38 /067/ 362 04 74'
        ],
        'telephone1_2' => [
            'type' => 'textInput',
            'value' => '+38 /050/ 371 98 00'
        ],
        'telephone1_3' => [
            'type' => 'textInput',
            'value' => '+38 /050/ 111 11 11'
        ],
        'country2' => [
            'type' => 'textInput',
            'value' => 'POLAND'
        ],
        'address2' => [
            'type' => 'textInput',
            'value' => 'ul. Sobieskiego, 11 Katowice'
        ],
        'telephone2_1' => [
            'type' => 'textInput',
            'value' => '+48 /500/87 83 62'
        ],
        'vkLink' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'gpluseLink' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'facebookLink' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'odLink' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'youtubePlayLink' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'twitterLink' => [
            'type' => 'textInput',
            'value' => ''
        ]
    ],
];
