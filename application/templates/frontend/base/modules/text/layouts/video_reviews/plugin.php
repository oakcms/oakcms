<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 16.09.2016
 * Project: osnovasite
 * File name: plugin.php
 */

use yii\helpers\ArrayHelper;
use app\modules\content\models\ContentArticles;
use app\modules\content\models\ContentCategory;

return [
    'name' => 'video_reviews',
    'title' => Yii::t('text', 'Video Review'),
    'preview_image' => Yii::getAlias('@web').'/application/templates/frontend/base/modules/text/layouts/video_reviews/preview.png',
    'viewFile' => '@app/templates/frontend/base/modules/text/layouts/video_reviews/view.php',
    'settings' => [
        'cssClass' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'title' => [
            'type' => 'textInput',
            'value' => 'Отзывы клиентов'
        ],
        'items' => [
            'type' => 'select2',
            'value' => [],
            'items' => function() {
                $category = ContentCategory::find()->where(['status' => ContentCategory::STATUS_PUBLISHED])->all();
                $options = [];
                foreach($category as $id => $p) {
                    $children = $p->items;
                    $child_options = [];
                    foreach($children as $child) {
                        $child_options[$child->id] = $child->title;
                    }
                    $options[$p->title] = $child_options;
                }
                return $options;
            },
            'options' => [
                'elementOptions' => [
                    'multiple' => true
                ]
            ]
        ],
    ],
];
