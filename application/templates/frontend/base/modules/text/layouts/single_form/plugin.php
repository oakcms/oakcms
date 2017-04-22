<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 16.09.2016
 * Project: osnovasite
 * File name: plugin.php
 */

return [
    'name' => 'single_form',
    'title' => Yii::t('text', 'Single form'),
    'preview_image' => Yii::getAlias('@web').'/application/templates/frontend/base/modules/text/layouts/single_form/preview.png',
    'viewFile' => '@app/templates/frontend/base/modules/text/layouts/single_form/view.php',
    'settings' => [
        'cssClass' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'form' => [
            'type' => 'formBuilder',
            'value' => ''
        ]
    ],
];
