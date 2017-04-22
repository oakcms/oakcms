<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 16.09.2016
 * Project: osnovasite
 * File name: plugin.php
 */

return [
    'name' => 'ourMission',
    'title' => Yii::t('text', 'Our Mission'),
    'preview_image' => Yii::getAlias('@web').'/application/templates/frontend/base/modules/text/layouts/ourMission/preview.png',
    'viewFile' => '@app/templates/frontend/base/modules/text/layouts/ourMission/view.php',
    'settings' => [
        'name1' => [
            'type' => 'textInput',
            'value' => 'Наша миссия:'
        ],
        'description1' => [
            'type' => 'textInput',
            'value' => 'Мы улучшаем качество жизни сограждан благодаря выгодным условиям труда'
        ],
        'name2' => [
            'type' => 'textInput',
            'value' => 'Наше виденье:'
        ],
        'description2' => [
            'type' => 'textInput',
            'value' => 'Стать лидером среди компаний по трудоустройству за границей в Украине благодаря лучшим вакансиям и сервису'
        ],
        'form' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'buttonName' => [
            'type' => 'textInput',
            'value' => 'ПОЛУЧИТЬ КОНСУЛЬТАЦИЮ'
        ]
    ],
];
