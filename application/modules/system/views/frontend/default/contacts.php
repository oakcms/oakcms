<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 18.09.2016
 * Project: osnovasite
 * File name: contacts.php
 * @var $this \app\components\View;
 */

use app\modules\text\api\Text;

$this->setSeoData(Yii::t('system', 'Contacts'), '', '', '/');

$this->params['breadcrumbs'] = [
    'label' => Yii::t('system', 'Contacts'),
]

?>
<?php echo Text::get('contact'); ?>

