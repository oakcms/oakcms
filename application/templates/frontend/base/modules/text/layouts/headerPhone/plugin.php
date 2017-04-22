<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 16.09.2016
 * Project: osnovasite
 * File name: plugin.php
 */

return [
    'name' => 'headerPhone',
    'title' => Yii::t('text', 'Header Phone'),
    'preview_image' => Yii::getAlias('@web').'/application/templates/frontend/base/modules/text/layouts/headerPhone/preview.png',
    'viewFile' => '@app/templates/frontend/base/modules/text/layouts/headerPhone/view.php',
    'settings' => [
        'phoneLink' => [
            'type' => 'textInput',
            'value' => 'tel:380673620474',
            'options' => [
                'elementOptions' => [
                    'type' => 'tel'
                ]
            ]
        ],
        'phone' => [
            'type' => 'textInput',
            'value' => '+38 /067/ 362 04 74'
        ]
    ],
];
