<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

namespace app\components\composer;


class Installer extends \yii\composer\Installer
{
    /**
     * @inheritdoc
     */
    public static function generateCookieValidationKey()
    {
        $configs = func_get_args();
        $key = self::generateRandomString();
        foreach ($configs as $config) {
            if (is_file($config)) {
                $content = preg_replace('/(("|\')COOKIE_VALIDATION_KEY("|\')\s*=\s*)(""|\'\')/', "\\1'$key'", file_get_contents($config), -1, $count);
                if ($count > 0) {
                    file_put_contents($config, $content);
                }
            }
        }
    }
}
