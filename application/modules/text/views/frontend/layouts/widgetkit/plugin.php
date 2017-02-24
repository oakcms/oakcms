<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

return [
    'name' => 'widgetkit',
    'title' => Yii::t('text', 'Widgetkit'),
    'preview_image' => Yii::getAlias('@web').'/application/modules/text/views/frontend/layouts/widgetkit/preview.png',
    'viewFile' => '@app/modules/text/views/frontend/layouts/widgetkit/view.php',
    'settings' => [
        'cssClass' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'id' => [
            'type' => 'textInput',
            'value' => ''
        ],
        'hideTitle' => [
            'type' => 'checkbox',
            'value' => 0
        ],
        'widgetkit' => [
            'type' => 'widgetkit',
            'value' => ''
        ]
    ],
];
