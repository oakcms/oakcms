<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 16.09.2016
 * Project: osnovasite
 * File name: plugin.php
 */

return [
    'name' => 'fixed_right_bord_phone',
    'title' => Yii::t('text', 'Fixed Right Bord Phone'),
    'preview_image' => Yii::getAlias('@web').'/application/templates/frontend/base/modules/text/layouts/fixed_right_bord_phone/preview.png',
    'viewFile' => '@app/templates/frontend/base/modules/text/layouts/fixed_right_bord_phone/view.php',
    'settings' => [
        'cssClass' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'form' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'phone' => [
            'type' => 'textInput',
            'value' => ''
        ]
    ],
];
