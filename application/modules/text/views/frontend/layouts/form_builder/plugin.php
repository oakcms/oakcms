<?php
/**
 * @package    oakcms/oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 16.09.2016
 * Project: osnovasite
 * File name: plugin.php
 */

use yii\helpers\ArrayHelper;
use app\modules\form_builder\models\FormBuilderForms;

$return = [
    'name' => 'form_builder',
    'title' => Yii::t('text', 'Form Builder'),
    'preview_image' => Yii::getAlias('@web').'/application/modules/text/views/frontend/layouts/form_builder/preview.png',
    'viewFile' => '@app/modules/text/views/frontend/layouts/form_builder/view.php',
    'settings' => [
        'form' => [
            'type' => 'select',
            'value' => '',
            'items' => function() {
                $items = FormBuilderForms::find()
                    ->select(['id', 'title'])
                    ->where(['status' => FormBuilderForms::STATUS_PUBLISHED])
                    ->asArray()
                    ->all();

                return ArrayHelper::map($items, 'id', 'title');
            },
        ]
    ],
];

if(!Yii::$app->hasModule('form_builder')) {
    $return = null;
}

return $return;
