<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 01.06.2016
 * Project: oakcms
 * File name: Editor.php
 */

namespace app\widgets;


use mihaildev\ckeditor\CKEditor;

class Editor extends CKEditor
{
    public function init()
    {
        parent::init();
        $this->editorOptions = \mihaildev\elfinder\ElFinder::ckeditorOptions('/admin/file-manager-elfinder', [
            'preset' => 'full',
            //'skin' => 'office2013'
        ]);
    }
}
