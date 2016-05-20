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
        'modules' => [
            'user' => [
                'class'                     => 'app\modules\user\Module',
                'controllerNamespace'       => 'app\modules\user\controllers\backend',
                'viewPath'                  => '@app/modules/user/views/backend',
            ],
        ],
    ],
    'user' => [
        'class' => 'app\modules\user\Module',
        'controllerNamespace' => 'app\modules\user\controllers\frontend',
        'viewPath' => '@app/modules/user/views/frontend',
    ],
    'system' => [
        'class' => 'app\modules\system\Module',
    ],
    'content' => [
        'class' => 'app\modules\content\Module',
    ],
];
