<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 16.09.2016
 * Project: osnovasite
 * File name: plugin.php
 */

return [
    'name' => 'headerOtherPage',
    'title' => Yii::t('text', 'Header Other Page'),
    'preview_image' => Yii::getAlias('@web').'/application/templates/frontend/base/modules/text/layouts/headerOtherPage/preview.png',
    'viewFile' => '@app/templates/frontend/base/modules/text/layouts/headerOtherPage/view.php',
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
        'telephone' => [
            'type' => 'textInput',
            'value' => '+38 /067/ 362 04 74'
        ],
        'telephone2' => [
            'type' => 'textInput',
            'value' => '+38 /050/ 371 98 00'
        ],
        'telephone3' => [
            'type' => 'textInput',
            'value' => '+38 /050/ 111 11 11'
        ],
        'form1' => [
            'type' => 'textInput',
            'value' => 'добавить резюме'
        ],
        'form1Link' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'form2' => [
            'type' => 'textInput',
            'value' => 'Обратный звонок'
        ],
        'form2Link' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'title' => [
            'type' => 'textInput',
            'value' => 'Легальная работа в Европе'
        ],
        'description' => [
            'type' => 'textInput',
            'value' => 'от 500 до 1500 долларов в месяц'
        ],
        'formCenter' => [
            'type' => 'textInput',
            'value' => 'Получить консультацию'
        ],
        'formCenterLink' => [
            'type' => 'textInput',
            'value' => ''
        ]
    ],
];
