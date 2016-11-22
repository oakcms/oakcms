<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 16.09.2016
 * Project: osnovasite
 * File name: plugin.php
 */

return [
    'name' => 'question',
    'title' => Yii::t('text', 'Question'),
    'preview_image' => Yii::getAlias('@web').'/application/templates/frontend/base/modules/text/views/frontend/layouts/question/preview.png',
    'viewFile' => '@app/templates/frontend/base/modules/text/views/frontend/layouts/question/view.php',
    'settings' => [
        'cssClass' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'text_top' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'text_bottom' => [
            'type' => 'textInput',
            'value' => ''
        ]
    ],
];
