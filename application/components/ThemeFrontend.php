<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\components;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;

class ThemeFrontend extends \yii\base\Theme
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->registerTranslations();
    }

    public function registerTranslations()
    {
        if (preg_match('#app\\\\templates\\\\frontend\\\\(.*)\\\\#', self::className(), $idTemplate)) {
            $template = ArrayHelper::getValue($idTemplate, '1', '');
            \Yii::$app->i18n->translations['tpl_' . $template . '*'] = [
                'class'          => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en-US',
                'basePath'       => '@app/templates/frontend/' . $template . '/messages',
            ];
        }
    }

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
                        $file = str_replace('views'. DIRECTORY_SEPARATOR .'frontend'. DIRECTORY_SEPARATOR, '', $file);
                    }

                    if (is_file($file)) {
                        return $file;
                    }
                }
            }
        }
        return $path;
    }
}
