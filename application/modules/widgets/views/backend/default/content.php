<?php

/**
 * @var $this \app\components\FrontendView;
 */

$this->bodyClass[] = 'com_widgetkit';

$this->params['actions_buttons'] = [
    [
        'label' => Yii::t('admin', 'Create'),
        'options' => [
            'form' => 'content-articles-id',
            'type' => 'submit'
        ],
        'icon' => 'fa fa-save',
        'color' => 'btn-success'
    ],
];

\mihaildev\elfinder\AssetsCallBack::register($this);
$app->handle();
