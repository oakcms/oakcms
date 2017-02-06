<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

$config = [
    'name'     => 'framework/yii',
    'main'     => 'YOOtheme\\Widgetkit\\Framework\\Yii\\YiiPlugin',
    'autoload' => [
        'YOOtheme\\Widgetkit\\Framework\\Yii\\' => 'src',
    ],
];

return $config;
