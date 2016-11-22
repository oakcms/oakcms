<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 16.09.2016
 * Project: osnovasite
 * File name: plugin.php
 */

return [
    'name' => 'clear_widget',
    'title' => Yii::t('text', 'Clear Widget'),
    'preview_image' => Yii::getAlias('@web').'/application/templates/frontend/base/modules/text/views/frontend/layouts/clear_widget/preview.png',
    'viewFile' => '@app/templates/frontend/base/modules/text/views/frontend/layouts/clear_widget/view.php',
    'settings' => [
        'cssClass' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'widgetkit' => [
            'type' => 'textInput',
            'value' => ''
        ]
    ],
];
