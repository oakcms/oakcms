<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 16.09.2016
 * Project: osnovasite
 * File name: plugin.php
 */

return [
    'name' => 'vacancyOther',
    'title' => Yii::t('text', 'Vacancy Other'),
    'preview_image' => Yii::getAlias('@web').'/application/templates/frontend/base/modules/text/layouts/vacancyOther/preview.png',
    'viewFile' => '@app/templates/frontend/base/modules/text/layouts/vacancyOther/view.php',
    'settings' => [
        'cssClass' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'title' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'category' => [
            'type' => 'checkboxList',
            'items' => function() {
                $categories = \app\modules\catalog\models\CatalogCategory::find()
                    ->published()
                    ->all();
                return \yii\helpers\ArrayHelper::map($categories, 'id', 'title');
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
