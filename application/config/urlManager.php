<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 26.03.2016
 * Project: oakcms
 * File name: urlManager.php
 */

return [
    'class'                             => 'app\components\UrlManager',
    'enablePrettyUrl'                   => true,
    'cache'                             => false,
    'showScriptName'                    => false,
    'enableStrictParsing'               => true,
    //'enableDefaultLanguageUrlCode'      => false,
    'normalizer' => [
        'class' => 'yii\web\UrlNormalizer',
    ],
    'rules' => [
        '/'                                                 => 'system/default',
        '<_m:[\w\-]+>/<_c:[\w\-]+>/<_a:[\w\-]+>/<id:\d+>'   => '<_m>/<_c>/<_a>',
        '<_m:[\w\-]+>/<_c:[\w\-]+>/<_a:[\w\-]+>'            => '<_m>/<_c>/<_a>',
    ],
    /*'ignoreLanguageUrlPatterns' => [
        '#^admin#' => '#^admin#',
    ],*/
];
