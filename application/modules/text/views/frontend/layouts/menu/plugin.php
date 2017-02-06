<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 16.09.2016
 * Project: osnovasite
 * File name: plugin.php
 */

return [
    'name' => 'menu',
    'title' => Yii::t('text', 'Menu'),
    'preview_image' => Yii::getAlias('@web').'/application/modules/text/views/frontend/layouts/menu/preview.png',
    'viewFile' => '@app/modules/text/views/frontend/layouts/menu/view.php',
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
        'menu_type_id' => [
            'type' => 'menuType',
            'value' => 0
        ],
        'menu_item_id' => [
            'type' => 'textInput',
            'value' => 0
        ],
        'start_lvl' => [
            'type' => 'textInput',
            'value' => 0
        ],
        'end_lvl' => [
            'type' => 'textInput',
            'value' => 0
        ]
    ],
];
