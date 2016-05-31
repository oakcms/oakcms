<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 15.05.2016
 * Project: oakcms
 * File name: modules.php
 */

return [
    'admin' => [
        'class' => 'app\modules\admin\Module',
        'controllerMap' => [
            'file-manager-elfinder' => [
                'class' => 'mihaildev\elfinder\Controller',
                'access' => ['manager'],
                'disabledCommands' => ['netmount'],
                'roots' => [
                    [
                        'baseUrl' => '@web/uploads',
                        'basePath' => '@webroot/uploads',
                        'path'   => '/',
                        'access' => ['read' => 'manager', 'write' => 'manager']
                    ]
                ]
            ]
        ],
    ],
    'system' => [
        'class' => 'app\modules\system\Module',
    ]
];
