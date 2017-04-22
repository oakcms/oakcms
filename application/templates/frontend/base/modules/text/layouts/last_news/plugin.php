<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 16.09.2016
 * Project: osnovasite
 * File name: plugin.php
 */

return [
    'name' => 'last_news',
    'title' => Yii::t('text', 'Last News'),
    'preview_image' => Yii::getAlias('@web').'/application/templates/frontend/base/modules/text/layouts/last_news/preview.png',
    'viewFile' => '@app/templates/frontend/base/modules/text/layouts/last_news/view.php',
    'settings' => [
        'cssClass' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'showWrapper' => [
            'type' => 'checkbox',
            'value' => 0
        ],
        'tag' => [
            'type' => 'select',
            'items' => [
                'h2' => 'h2',
                'h3' => 'h3',
                'h4' => 'h4',
                'h5' => 'h5',
                'h6' => 'h6',
            ],
            'value' => 'h2'
        ],
        'title' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'description' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'newsCategory' => [
            'type' => 'select',
            'items' => function() {
                $items = \app\modules\content\models\ContentCategory::find()->published()->all();
                $items = \yii\helpers\ArrayHelper::map($items, 'id', 'title');
                return $items;
            },
            'value' => ''
        ]
    ],
];
