<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 22.04.2016
 * Project: design4web.lok
 * File name: view.php
 */

use yii\helpers\Html;
use app\modules\language\models\Language;
?>

<div class="languages">
    <ul id="languages__items">
        <?php
        $c = count($langs);
        foreach ($langs as $k=>$lang):?>
            <!--<li class="languages__items__item <?php /*= ($current->id == $lang->id) ? 'active' : '' */
            ?>">
                <?php /*if($lang->url != $current->url) {
                    if($lang->url == Lang::getDefaultLang()->url) {
                        echo Html::a($lang->name, '/');
                    } else {
                        if(Yii::$app->getRequest()->getLangUrl() == '/') {
                            $getLangUrl = '';
                        } else {
                            $getLangUrl = Yii::$app->getRequest()->getLangUrl();
                        }
                        echo Html::a($lang->name,'/'.$lang->url);
                    }
                } else {
                    echo '<span>'.$lang->name.'</span>';
                }
                if($c != $k+1) {
                    echo ' / ';
                }
                */?>
            </li>-->
        <?php endforeach;?>
    </ul>
</div>

