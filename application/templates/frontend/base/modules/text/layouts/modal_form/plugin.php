<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 16.09.2016
 * Project: osnovasite
 * File name: plugin.php
 */

return [
    'name' => 'modal_form',
    'title' => Yii::t('text', 'Modal Form'),
    'preview_image' => Yii::getAlias('@web').'/application/templates/frontend/base/modules/text/layouts/modal_form/preview.png',
    'viewFile' => '@app/templates/frontend/base/modules/text/layouts/modal_form/view.php',
    'settings' => [
        'id' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'form' => [
            'type' => 'formBuilder',
            'value' => ''
        ]
    ],
];
