<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 11.01.2017
 * Project: kn-group-site
 * File name: Theme.php
 */

namespace app\components;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\FileHelper;
use yii\helpers\VarDumper;

class ThemeFrontend extends \yii\base\Theme
{

    /**
     * Converts a file to a themed file if possible.
     * If there is no corresponding themed file, the original file will be returned.
     * @param string $path the file to be themed
     * @return string the themed file, or the original file if the themed version is not available.
     * @throws InvalidConfigException if [[basePath]] is not set
     */
    public function applyTo($path)
    {

        $pathMap = $this->pathMap;
        if (empty($pathMap)) {
            if (($basePath = $this->getBasePath()) === null) {
                throw new InvalidConfigException('The "basePath" property must be set.');
            }
            $pathMap = [Yii::$app->getBasePath() => [$basePath]];
        }

        $path = FileHelper::normalizePath($path);

        foreach ($pathMap as $from => $tos) {
            $oldfrom = $from;
            $from = FileHelper::normalizePath(Yii::getAlias($from)) . DIRECTORY_SEPARATOR;
            if (strpos($path, $from) === 0) {
                $n = strlen($from);

                foreach ((array) $tos as $to) {
                    $to = FileHelper::normalizePath(Yii::getAlias($to)) . DIRECTORY_SEPARATOR;
                    $file = $to . substr($path, $n);

                    if($oldfrom == '@app/modules') {
                        $file = str_replace('views\frontend\\', '', $file);
                    }

//                    if(Yii::$app->hasModule('content') && Yii::$app->controller->module->uniqueId == 'content') {
//                        var_dump($file);
//                    }
                    if (is_file($file)) {
                        return $file;
                    }
                }
            }
        }
        return $path;
    }
}
