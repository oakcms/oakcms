<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 01.06.2016
 * Project: oakcms
 * File name: Editor.php
 */

namespace app\widgets;

use dosamigos\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;

class Editor extends CKEditor
{
    public function init()
    {
        parent::init();
        $this->clientOptions = ElFinder::ckeditorOptions('/admin/file-manager-elfinder', [
            'entities'       => false,
            'allowedContent' => true,
            'baseHref'       => \Yii::$app->homeUrl,
            'filebrowserBrowseUrl' => [\Yii::$app->homeUrl.'admin/menu/item/ckeditor-select'],
            'preset' => ''
        ]);
    }
}
