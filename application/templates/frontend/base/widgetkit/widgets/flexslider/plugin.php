<?php

return array(
    'name' => 'widget/flexslider',
    'main' => '\\YOOtheme\\Widgetkit\\Widget\\Widget',
    'config' => array(
        'name'  => 'flexslider',
        'label' => 'flexslider',
        'core'  => false,
        'icon'  => __DIR__.'/widget.svg',
        'view'  => __DIR__.'/views/view.php',
        'item'  => array('title', 'content', 'media'),
        'settings' => array(
            'class'              => 'slider'
        ),
        'fields' => array(
            array(
                'type' => 'text',
                'name' => 'status',
                'label' => Yii::t('widgets', 'Status')
            )
        ),
    ),

    'events' => array(
        'init.site' => function($event, $app) {
            Yii::$app->view->registerJsFile(Yii::getAlias('@web').'/application/templates/frontend/base/widgetkit/widgets/flexslider/libs/jquery.flexslider-min.js', ['depends' => [\yii\web\JqueryAsset::className()]], 'flexslider');
            Yii::$app->view->registerCssFile(Yii::getAlias('@web').'/application/templates/frontend/base/widgetkit/widgets/flexslider/libs/flexslider.css', [], 'flexslider');
            Yii::$app->view->registerJs('$(\'.flexslider\').flexslider({animation: "slide"});', \yii\web\View::POS_END, 'flexslider');
        },

        'init.admin' => function($event, $app) {
            $app['angular']->addTemplate('flexslider.edit', __DIR__.'/views/edit.php', false);
        }
    )

);
