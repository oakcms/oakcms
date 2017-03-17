<?php

$this->bodyClass = ['news'];
$this->logoCont = '
<div class="logo__text logo__news">ПОСЛЕДНИЕ<br>НОВОСТИ</div>
';
$news = \app\modules\content\models\ContentArticles::find()->published()->where(['category_id'=>1])->all();
$this->setSeoData('News', '', '', '/news.html');
?>



<section class="latest-news section">
    <div class="section__bg">
        <div class="section__content">
            <?foreach ($models as $k=>$model):?>
                <?= $this->renderFile('@app/modules/content/views/frontend/default/_item_2.php', ['model' => $model, 'index' => $k]) ?>
            <?endforeach?>
            <?php echo \yii\widgets\LinkPager::widget([
                'pagination' => $pages,
                'registerLinkTags' => true
            ]); ?>
        </div>
    </div>
</section>

<section id="developer" class="section section-5" style="margin-bottom: 250px">
    <?if(LIVE_EDIT) \app\widgets\EditorInline::begin([
        'name' => 'developer',
        'dataUrlEdit' => \yii\helpers\Url::to(['/text/default/save/']),
        'value' => \app\modules\text\api\Text::get('developer')
    ]); ?>
    <?= (!LIVE_EDIT) ? \app\modules\text\api\Text::get('developer'):'' ?>
    <?if(LIVE_EDIT) \app\widgets\EditorInline::end(); ?>
</section>
