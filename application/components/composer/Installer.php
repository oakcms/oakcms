<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.5
 */

namespace app\components\composer;

class Installer
{
    private static $_db_host     = 'localhost';
    private static $_db_name     = 'oakcms';
    private static $_db_username = 'root';
    private static $_db_password = '';

    /**
     * @inheritdoc
     */
    public static function generateConfig()
    {
        $key = self::generateRandomString();

        require(__DIR__ . '/../../../vendor/autoload.php');
        require(__DIR__ . '/../../../vendor/yiisoft/yii2/Yii.php');

        while (!self::hasDbConnect()) {}

        $content = preg_replace(
            [
                '/DB_HOST(.*)=(.*)(\r|\n)/',
                '/DB_NAME(.*)=(.*)(\r|\n)/',
                '/DB_USERNAME(.*)=(.*)(\r|\n)/',
                '/DB_PASSWORD(.*)=(.*)(\r|\n)/',
                '/COOKIE_VALIDATION_KEY(.*)=(.*)(\r|\n)/',
            ],
            [
                'DB_HOST$1= '.self::$_db_host.'$3',
                'DB_NAME$1= '.self::$_db_name.'$3',
                'DB_USERNAME$1= '.self::$_db_username.'$3',
                'DB_PASSWORD$1= '.self::$_db_password.'$3',
                'COOKIE_VALIDATION_KEY$1= '.$key.'$3',
            ],
            file_get_contents('.env-dist'),
            -1,
            $count
        );

        if ($count > 0) {
            file_put_contents('.env', $content);
            echo shell_exec('application/yii migrate --interactive=0');
        }
    }


    protected static function hasDbConnect() {
        self::$_db_host     = self::readStdinUser('Data Base Host', 'localhost');
        self::$_db_name     = self::readStdinUser('Data Base Name', 'oakcms');
        self::$_db_username = self::readStdinUser('Data Base User Name', 'root');
        self::$_db_password = self::readStdinUser('Data Base User Password');

        while (($confirm = self::readStdinUser('Are the data entered correctly?', 'no')) != 'no' || $confirm != 'n') {

            if($confirm!= 'no' && $confirm != 'n' && $confirm != 'yes' && $confirm != 'y') {
                continue;
            }


            if($confirm == 'yes' || $confirm == 'y') {
                $connection = new \yii\db\Connection([
                    'dsn' => 'mysql:host='.self::$_db_host.';port=3306;dbname='.self::$_db_name,
                    'username' => self::$_db_username,
                    'password' => self::$_db_password,
                ]);

                try {
                    $connection->open();
                    return true;
                } catch (\Exception $e) {
                    echo 'Wrong permission to db'.PHP_EOL;
                    return false;
                }
            }

            return false;
        }
        return false;
    }

    protected static function generateRandomString()
    {
        if (!extension_loaded('openssl')) {
            throw new \Exception('The OpenSSL PHP extension is required by Yii2.');
        }
        $length = 32;
        $bytes = openssl_random_pseudo_bytes($length);
        return strtr(substr(base64_encode($bytes), 0, $length), '+/=', '_-.');
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
