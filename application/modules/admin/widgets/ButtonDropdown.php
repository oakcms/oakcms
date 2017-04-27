<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\admin\widgets;


class ButtonDropdown extends \yii\bootstrap\ButtonDropdown
{
    public function run()
    {
        //$view = $this->getView();

        if ($this->clientOptions !== false) {
            // $js = "$.widget.bridge('uibutton', $.ui.button);";
            // $view->registerJs($js);
        }

        return parent::run();
    }
}
