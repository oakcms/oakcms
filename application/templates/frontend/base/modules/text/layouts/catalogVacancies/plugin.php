<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 16.09.2016
 * Project: osnovasite
 * File name: plugin.php
 */

return [
    'name' => 'catalogVacancies',
    'title' => Yii::t('text', 'Catalog Vacancies'),
    'preview_image' => Yii::getAlias('@web').'/application/templates/frontend/base/modules/text/layouts/catalogVacancies/preview.png',
    'viewFile' => '@app/templates/frontend/base/modules/text/layouts/catalogVacancies/view.php',
    'settings' => [
        'cssClass' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'title' => [
            'type' => 'textInput',
            'value' => 'Каталог вакансий'
        ]
    ],
];
