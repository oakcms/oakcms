<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 05.04.2016
 * Project: oakcms
 * File name: index.php
 */

require(__DIR__ . '/vendor/autoload.php');
require(__DIR__ . '/application/components/env.php');
require(__DIR__ . '/vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/application/config/web.php');

(new yii\web\Application($config))->run();
