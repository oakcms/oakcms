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
 * Date: 11.10.2016
 * Project: kotsyubynsk
 * File name: CoreView.php
 */

namespace app\components;

use app\modules\system\components\SeoViewBehavior;
use yii\helpers\Url;

/**
 * Class CoreView
 * @package oakcms
 *
 * @mixin SeoViewBehavior
 */
class CoreView extends \rmrevin\yii\minify\View
{
    public $bodyClass = [];
    public $adminPanel = true;
    public $modalLayout = '@app/modules/admin/views/layouts/_modal';

    public $pageTitle = '';
    public $pageTitleHeading = 'h3';

    public function isAdmin() {
        $rHostInfo = Url::home(true);
        if (!\Yii::$app->user->isGuest && strpos(\Yii::$app->request->absoluteUrl, $rHostInfo.'admin') !== false) {
            return true;
        } else {
            return false;
        }
    }

    public function applyModalLayout()
    {
        \Yii::$app->layout = $this->modalLayout;
    }

    public function init()
    {

        if(!\Yii::$app->user->isGuest) {
            if(($locale = \Yii::$app->user->identity->userProfile->locale) != '') {
                \Yii::$app->language = $locale;
            }
        } else {
            \Yii::$app->language = \Yii::$app->keyStorage->get('language');
        }

        parent::init();
    }
}
