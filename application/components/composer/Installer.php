<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.5
 */

namespace app\components\composer;


class Installer extends \yii\composer\Installer
{
    /**
     * @inheritdoc
     */
    public static function generateCookieValidationKey()
    {
        $key            = self::generateRandomString();
        $db_host        = self::readStdinUser('Data Base Host', 'localhost');
        $db_name        = self::readStdinUser('Data Base Name', 'oakcms');
        $db_username    = self::readStdinUser('Data Base User Name', 'root');
        $db_password    = self::readStdinUser('Data Base User Password');

        $content = preg_replace(
            [
                '/DB_HOST(.*)=(.*)(\r|\n)/',
                '/DB_NAME(.*)=(.*)(\r|\n)/',
                '/DB_USERNAME(.*)=(.*)(\r|\n)/',
                '/DB_PASSWORD(.*)=(.*)(\r|\n)/',
                '/COOKIE_VALIDATION_KEY(.*)=(.*)(\r|\n)/',
            ],
            [
                'DB_HOST$1= '.$db_host.'$3',
                'DB_NAME$1= '.$db_name.'$3',
                'DB_USERNAME$1= '.$db_username.'$3',
                'DB_PASSWORD$1= '.$db_password.'$3',
                'COOKIE_VALIDATION_KEY$1= '.$key.'$3',
            ],
            file_get_contents('.env-dist'),
            -1,
            $count
        );

        if ($count > 0) {
            file_put_contents('.env', $content);
            echo shell_exec('yii migrate');
        }
    }


    /**
     * @param string $prompt
     * @param string $default
     * @return string
     */
    private static function readStdinUser($prompt, $default = '')
    {
        while (!isset($input)) {
            echo $prompt . (($default) ? " [$default]" : '') . ': ';
            $input = (trim(fgets(STDIN)));
            if (empty($input) && !empty($default)) {
                $input = $default;
            }
        }
        return $input;
    }
}
