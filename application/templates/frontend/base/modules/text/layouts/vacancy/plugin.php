<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 16.09.2016
 * Project: osnovasite
 * File name: plugin.php
 */

return [
    'name' => 'vacancy',
    'title' => Yii::t('text', 'Vacancy Home'),
    'preview_image' => Yii::getAlias('@web').'/application/templates/frontend/base/modules/text/layouts/vacancy/preview.png',
    'viewFile' => '@app/templates/frontend/base/modules/text/layouts/vacancy/view.php',
    'settings' => [
        'cssClass' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'items' => [
            'type' => 'checkboxList',
            'items' => function() {
                $items = \app\modules\catalog\models\CatalogItems::find()
                    ->published()
                    ->all();
                return \yii\helpers\ArrayHelper::map($items, 'id', 'title');
            },
            'value' => [],
            'options' => [
                'elementOptions' => [
                    'class' => 'checkbox',
                    'itemOptions' => [
                        'style' => 'display: block'
                    ]
                ]
            ]
        ]
    ],
];
