<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 12.10.2016
 * Project: kotsyubynsk
 * File name: _items.php
 */

use app\modules\admin\widgets\Html;
use yii\helpers\Url;
use yii\helpers\StringHelper;

?>

<div class="container-fluid">
    <div class="col-sm-4">
        <div class="news-photo">
            <a href="<?= Url::to(['/content/article/view', 'catslug' => $model->category->slug, 'slug'=>$model->slug]) ?>">
                <img src="<?= $model->getUploadUrl('image') ?>" alt="<?= $model->title ?>">
            </a>
        </div>
    </div>

    <div class="col-sm-7">
        <h3>
            <?= Html::a($model->title, ['/content/article/view', 'catslug' => $model->category->slug, 'slug'=>$model->slug]); ?>
        </h3>

        <p>
            <?= StringHelper::truncateWords(strip_tags($model->content), 20) ?>
        </p>
    </div>
</div>
