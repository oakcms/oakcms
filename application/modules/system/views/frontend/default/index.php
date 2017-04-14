<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

/**
 * @var $this \app\components\View;
 * @var $dataProviderCatalog \app\modules\catalog\models\CatalogItems;
 */

use \yii\helpers\Url;
use app\modules\text\api\Text;
$this->setSeoData(Yii::$app->keyStorage->get('siteName'), '', '', '/');

?>
