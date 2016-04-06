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
    'rules' => [
        '' => 'system/default/index',
        '<action:error>' => 'main/default/<action>',
        '<action:(login|logout|signup|email-confirm|request-password-reset|password-reset)>' => 'user/default/<action>',
        '<module:[\w\-]+>/<controller:[\w\-]+>/<action:[\w\-]+>/<id:\d+>' => '<module>/<controller>/<action>',
        '<module:[\w\-]+>/<controller:[\w\-]+>/<id:\d+>' => '<module>/<controller>/view',
        '<module:[\w\-]+>' => '<module>/default/index',
        '<module:[\w\-]+>/<controller:[\w\-]+>' => '<module>/<controller>/index',
    ],
];