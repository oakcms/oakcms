<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 26.03.2016
 * Project: oakcms
 * File name: urlManager.php
 */

return [
    'enablePrettyUrl' => true,
    'showScriptName' => false,
    'enableStrictParsing' => true,
    'rules' => [
        '' => 'system/default/index',
        '<module:[\w\-]+>/<controller:[\w\-]+>/<action:[\w\-]+>' => '<module>/<controller>/<action>',

        /*[
            'pattern'   => '<action:error>',
            'route'     => 'main/default/<action>',
            'suffix'    => '',
        ],

        [
            'pattern'   => '<action:(login|logout|signup|email-confirm|request-password-reset|password-reset)>',
            'route'     => 'user/default/<action>',
            'suffix'    => '',
        ],

        [
            'pattern'   => '<module:[\w\-]+>/<controller:[\w\-]+>/<action:[\w\-]+>/<id:\d+>',
            'route'     => '<module>/<controller>/<action>',
            'suffix'    => '',
        ],

        [
            'pattern'   => '<module:[\w\-]+>/<controller:[\w\-]+>/<id:\d+>',
            'route'     => '<module>/<controller>/view',
            'suffix'    => '',
        ],

        [
            'pattern'   => '<module:[\w\-]+>',
            'route'     => '<module>/default/index',
            'suffix'    => '',
        ],

        [
            'pattern'   => '<module:[\w\-]+>/<controller:[\w\-]+>',
            'route'     => '<module>/<controller>/index',
            'suffix'    => '',
        ],*/
    ],
];
