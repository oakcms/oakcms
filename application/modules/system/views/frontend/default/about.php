<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 30.08.2016
 * Project: falconcity
 * File name: about.php
 */

$this->bodyClass = ['about']

?>

<section class="section section-1">
    <?if(LIVE_EDIT) \app\widgets\EditorInline::begin([
        'name' => 'about_section_1',
        'dataUrlEdit' => \yii\helpers\Url::to(['/text/default/save/']),
        'value' => \app\modules\text\api\Text::get('about_section_1')
    ]); ?>
    <?= (!LIVE_EDIT) ? \app\modules\text\api\Text::get('about_section_1'):'' ?>
    <?if(LIVE_EDIT) \app\widgets\EditorInline::end() ; ?>
</section>
<section class="section section-lux">
    <?if(LIVE_EDIT) \app\widgets\EditorInline::begin([
        'name' => 'about_lux_content',
        'dataUrlEdit' => \yii\helpers\Url::to(['/text/default/save/']),
        'value' => \app\modules\text\api\Text::get('about_lux_content')
    ]); ?>
    <?= (!LIVE_EDIT) ? \app\modules\text\api\Text::get('about_lux_content'):'' ?>
    <?if(LIVE_EDIT) \app\widgets\EditorInline::end() ; ?>
</section>

<section class="section section-proposition">
    <?if(LIVE_EDIT) \app\widgets\EditorInline::begin([
        'name' => 'about_section_proposition',
        'dataUrlEdit' => \yii\helpers\Url::to(['/text/default/save/']),
        'value' => \app\modules\text\api\Text::get('about_section_proposition')
    ]); ?>
    <?= (!LIVE_EDIT) ? \app\modules\text\api\Text::get('about_section_proposition'):'' ?>
    <?if(LIVE_EDIT) \app\widgets\EditorInline::end() ; ?>

</section>

<section id="developer" class="section section-5">
    <?if(LIVE_EDIT) \app\widgets\EditorInline::begin([
        'name' => 'developer',
        'dataUrlEdit' => \yii\helpers\Url::to(['/text/default/save/']),
        'value' => \app\modules\text\api\Text::get('developer')
    ]); ?>
    <?= (!LIVE_EDIT) ? \app\modules\text\api\Text::get('developer'):'' ?>
    <?if(LIVE_EDIT) \app\widgets\EditorInline::end(); ?>
</section>

<section class="latest-news section">
    <div class="container">
        <div class="section__title">
            ПОСЛЕДНИЕ НОВОСТИ
        </div>
    </div>
    <div class="section__bg">
        <div class="section__content">
            <div class="container">
                <div class="row">
                    <?php
                    $new = \app\modules\content\models\ContentArticles::find()->published()->limit(1)->orderBy('id DESC')->one()
                    ?>
                    <?= $this->renderFile('@app/modules/content/views/frontend/default/_item.php', ['model' => $new]) ?>
                </div>
            </div>
        </div>
    </div>
</section>
