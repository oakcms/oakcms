<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 15.05.2016
 * Project: oakcms
 * File name: modules.php
 */

return [
    'system' => [
        'class' => 'app\modules\system\Module',
    ],
    'admin' => [
        'class' => 'app\modules\admin\Module',
        'modules' => [
            'system' => [
                'class'                     => 'app\modules\system\Module',
                'controllerNamespace'       => 'app\modules\system\controllers\backend',
                'viewPath'                  => '@app/modules/system/views/backend',
            ],
            'user' => [
                'class'                     => 'app\modules\user\Module',
                'controllerNamespace'       => 'app\modules\user\controllers\backend',
                'viewPath'                  => '@app/modules/user/views/backend',
            ],
            'seo' => [
                'class'                     => 'app\modules\seo\Module',
            ]
        ],
    ],
    'user' => [
        'class' => 'app\modules\user\Module',
        'controllerNamespace' => 'app\modules\user\controllers\frontend',
        'viewPath' => '@app/modules/user/views/frontend',
    ],
    'content' => [
        'class' => 'app\modules\content\Module',
    ],
];
