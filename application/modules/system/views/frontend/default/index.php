<?php

/**
 * @var $this \app\components\View;
 * @var $dataProviderCatalog \app\modules\catalog\models\CatalogItems;
 */

use \yii\helpers\Url;
use app\modules\text\api\Text;
$this->setSeoData(Yii::$app->keyStorage->get('siteName'), '', '', '/');

?>
