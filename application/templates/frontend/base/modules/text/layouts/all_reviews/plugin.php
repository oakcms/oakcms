<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 16.09.2016
 * Project: osnovasite
 * File name: plugin.php
 */

return [
    'name' => 'all_reviews',
    'title' => Yii::t('text', 'All Reviews'),
    'preview_image' => Yii::getAlias('@web').'/application/templates/frontend/base/modules/text/layouts/all_reviews/preview.png',
    'viewFile' => '@app/templates/frontend/base/modules/text/layouts/all_reviews/view.php',
    'settings' => [
        'cssClass' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'link' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'buttonName' => [
            'type' => 'textInput',
            'value' => ''
        ]
    ],
];
