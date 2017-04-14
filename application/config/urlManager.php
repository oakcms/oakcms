<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

return [
    'class'                             => 'app\components\UrlManager',
    'enablePrettyUrl'                   => true,
    'showScriptName'                    => false,
    'enableStrictParsing'               => true,
    'enableDefaultLanguageUrlCode'      => false,
    'normalizer' => [
        'class' => 'yii\web\UrlNormalizer',
    ],
    'rules' => [
        '<_m:[\w\-]+>/<_c:[\w\-]+>/<_a:[\w\-]+>/<id:\d+>'   => '<_m>/<_c>/<_a>',
        '<_m:[\w\-]+>/<_c:[\w\-]+>/<_a:[\w\-]+>'            => '<_m>/<_c>/<_a>',
    ],
    'ignoreLanguageUrlPatterns' => [
        '#^admin#' => '#^admin#',
    ]
];
