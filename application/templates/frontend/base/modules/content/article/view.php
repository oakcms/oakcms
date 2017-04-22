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
 * Date: 24.09.2016
 * Project: osnovasite
 * File name: view.php
 *
 * @var $model \app\modules\content\models\ContentArticles
 * @var $categoryModel \app\modules\content\models\ContentCategory
 * @var $breadcrumbs \yii\widgets\Breadcrumbs
 * @var $this \app\components\CoreView
 */

use yii\helpers\Url;
use yii\helpers\StringHelper;

$rating_id = 'rating_'.$model->id;

$meta_title = ($model->meta_title != '') ? $model->meta_title : $model->title;
$meta_description = StringHelper::truncate((($model->meta_description != '') ? $model->meta_description : strip_tags($model->description)), '140', '');
$meta_keywords = ($model->meta_keywords != '') ? $model->meta_keywords : implode(', ', explode(' ', $model->title));

$this->setSeoData($meta_title, $meta_description, $meta_keywords);
$this->pageTitle = $model->title;

$this->registerJsFile('//yastatic.net/es5-shims/0.0.2/es5-shims.min.js');
$this->registerJsFile('//yastatic.net/share2/share.js');

?>

<article>
    <div class="block-img">
        <div class="image-holder">
            <img src="<?= $model->getUploadUrl('image') ?>" alt="Все о работе в офисе: очень хорошая  зарплата и  работа" class="fake-img">
            <div style="background: url('<?= $model->getUploadUrl('image') ?>') no-repeat;background-position: center;background-repeat: no-repeat;background-size: cover;" class="img"></div>
        </div>
    </div>
    <h1><?= $model->title ?></h1>
    <div class="block-info-article inline-layout">
        <time><?= date('d.m.Y', $model->published_at) ?></time>
        <p class="view-user">
            <?= Yii::t(
                'catalog',
                '{n, plural, one{# user} other{# users}}',
                ['n' => $model->getBehavior('hit')->getHitsCount()]
            ) ?>
        </p>
    </div>
    <?= $model->description ?>
    <?= $model->content ?>
    <div data-services="facebook,vkontakte,twitter,odnoklassniki,gplus" class="ya-share2"></div>
</article>

<?= \app\modules\text\api\Text::get('latest_news') ?>
