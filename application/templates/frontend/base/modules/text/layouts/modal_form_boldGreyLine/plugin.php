<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 16.09.2016
 * Project: osnovasite
 * File name: plugin.php
 */

return [
    'name' => 'modal_form_boldGreyLine',
    'title' => Yii::t('text', 'Modal Form Bold Grey line'),
    'preview_image' => Yii::getAlias('@web').'/application/templates/frontend/base/modules/text/layouts/modal_form_boldGreyLine/preview.png',
    'viewFile' => '@app/templates/frontend/base/modules/text/layouts/modal_form_boldGreyLine/view.php',
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
