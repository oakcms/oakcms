<?php
/**
 * @package    oakcms/oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */
return [
    'name' => 'clear',
    'title' => Yii::t('text', 'Clear'),
    'preview_image' => Yii::getAlias('@web').'/application/modules/text/views/frontend/layouts/clear/preview.png',
    'viewFile' => '@app/modules/text/views/frontend/layouts/clear/view.php',
    'settings' => [
        'hide_title' => [
            'type' => 'checkbox',
            'value' => 0
        ],
        'title_size' => [
            'type' => 'textInput',
            'value' => 'h2',
        ],
        'sub_title_size' => [
            'type' => 'textInput',
            'value' => 'h3',
        ]
    ],
];
