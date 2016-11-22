<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 16.09.2016
 * Project: osnovasite
 * File name: plugin.php
 */

return [
    'name' => 'header',
    'title' => Yii::t('text', 'Header'),
    'preview_image' => Yii::getAlias('@web').'/application/templates/frontend/base/modules/text/views/frontend/layouts/header/preview.png',
    'viewFile' => '@app/templates/frontend/base/modules/text/views/frontend/layouts/header/view.php',
    'settings' => [
        'cssClass' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'telephone' => [
            'type' => 'textInput',
            'value' => '+38 (067) 465-43-84'
        ]
    ],
];
