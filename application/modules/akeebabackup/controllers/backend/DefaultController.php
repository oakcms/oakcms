<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\akeebabackup\controllers\backend;

use yii\helpers\Url;

/**
 * Class DefaultController
 * @package app\modules\akeebabackup\controllers\backend
 */
class DefaultController extends \app\components\BackendController
{

    public function actionIndex() {

        if (!defined('AKEEBA_SOLO_YII_ROOTURL')) {
            define('AKEEBA_SOLO_YII_ROOTURL', Url::home());
        }

        if (!defined('AKEEBA_SOLO_YII_URL')) {
            $bootstrapUrl = Url::to(['/admin/akeebabackup']);
            define('AKEEBA_SOLO_YII_URL', $bootstrapUrl);
        }

        /*if (!defined('AKEEBA_SOLO_YII_SITEURL')) {
            $baseUrl = plugins_url('app/index.php', self::$absoluteFileName);
            define('AKEEBA_SOLO_YII_SITEURL', substr($baseUrl, 0, -10));
        }*/

        return $this->render('index');
    }
}
