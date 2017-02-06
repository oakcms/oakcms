<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 15.05.2016
 * Project: oakcms
 * File name: ButtonDropdown.php
 */

namespace app\modules\admin\widgets;


class ButtonDropdown extends \yii\bootstrap\ButtonDropdown
{
    public function run()
    {
        $view = $this->getView();

        if ($this->clientOptions !== false) {
            // $js = "$.widget.bridge('uibutton', $.ui.button);";
            // $view->registerJs($js);
        }

        return parent::run();
    }
}
