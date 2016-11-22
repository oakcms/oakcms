<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 15.05.2016
 * Project: oakcms
 * File name: modules.php
 */
require __DIR__.'/../modules/admin/rbac/Rbac.php';
return [
    'admin' => [
        'class' => 'app\modules\admin\Module',
        'controllerMap' => [
            'file-manager-elfinder' => [
                'class' => 'mihaildev\elfinder\Controller',
                'enableCsrfValidation' => false,
                'access' => [ \app\modules\admin\rbac\Rbac::PERMISSION_ADMIN_PANEL ],
                'disabledCommands' => ['netmount'],
                'roots' => [
                    [
                        'baseUrl' => 'uploads',
                        'basePath' => '@webroot/uploads',
                        'path'   => '',
                        'access' => ['read' => \app\modules\admin\rbac\Rbac::PERMISSION_ADMIN_PANEL, 'write' => \app\modules\admin\rbac\Rbac::PERMISSION_ADMIN_PANEL]
                    ]
                ]
            ]
        ],
        'modules' => [
            'debug' => [
                'class' => 'yii\debug\Module',
            ]
        ]
    ],
    'system' => [
        'class' => 'app\modules\system\Module',
    ],
    'gridview' =>  ['class' => '\kartik\grid\Module']
];
