<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 16.09.2016
 * Project: osnovasite
 * File name: plugin.php
 */

return [
    'name' => 'modal_form_hairline',
    'title' => Yii::t('text', 'Modal Form Hairline'),
    'preview_image' => Yii::getAlias('@web').'/application/templates/frontend/base/modules/text/layouts/modal_form_hairline/preview.png',
    'viewFile' => '@app/templates/frontend/base/modules/text/layouts/modal_form_hairline/view.php',
    'settings' => [
        'cssClass' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'form' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'buttonName' => [
            'type' => 'textInput',
            'value' => ''
        ]
    ],
];
