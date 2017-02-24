<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 16.09.2016
 * Project: osnovasite
 * File name: plugin.php
 */

return [
    'name' => 'default',
    'title' => Yii::t('text', 'Default'),
    'preview_image' => Yii::getAlias('@web').'/application/modules/text/views/frontend/layouts/default/preview.png',
    'viewFile' => '@app/modules/text/views/frontend/layouts/default/view.php',
    'settings' => [
        'cssClass' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'id' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'hideTitle' => [
            'type' => 'checkbox',
            'value' => 0
        ]
    ],
];
