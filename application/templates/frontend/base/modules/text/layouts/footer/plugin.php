<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 16.09.2016
 * Project: osnovasite
 * File name: plugin.php
 */

return [
    'name' => 'footer',
    'title' => Yii::t('text', 'Footer'),
    'preview_image' => Yii::getAlias('@web').'/application/templates/frontend/base/modules/text/views/frontend/layouts/footer/preview.png',
    'viewFile' => '@app/templates/frontend/base/modules/text/views/frontend/layouts/footer/view.php',
    'settings' => [
        'cssClass' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'telephone' => [
            'type' => 'textInput',
            'value' => '(067) 323-78-07'
        ],
        'sales_department' => [
            'type' => 'textInput',
            'value' => '08298 Киевская обл., пгт. Коцюбинское, ул. Пономарёва, 26'
        ],
        'schedule' => [
            'type' => 'textInput',
            'value' => 'Пн. - Пт. 9:00 - 18:00'
        ],
        'links' => [
            'type' => 'textarea',
            'value' => ''
        ],
        'facebook_link' => [
            'type' => 'textarea',
            'value' => 'https://www.facebook.com/kotsyubynsk.com.ua/'
        ]
    ],
];
