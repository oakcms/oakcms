<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 30.08.2016
 * Project: falconcity
 * File name: EditorInline.php
 */

namespace app\widgets;


use app\components\View;
use app\modules\admin\widgets\Html;
use mihaildev\ckeditor\Assets;
use mihaildev\ckeditor\CKEditor;
use yii\helpers\Json;
use yii\web\JsExpression;

class EditorInline extends CKEditor
{
    public $dataUrlEdit;
    public $editorOptions = [
        'inline' => true
    ];
    public $_inline = true;

    public function init()
    {
        $this->editorOptions = \mihaildev\elfinder\ElFinder::ckeditorOptions('/admin/file-manager-elfinder', [
            'preset' => 'full',
            'extraPlugins' => 'sourcedialog',
            'allowedContent' => true,
            'inline' => true,
            'height' => 'auto'
        ]);
        parent::init();
    }

    public function run()
    {
        Assets::register($this->getView());

        echo Html::beginTag('div', $this->containerOptions);
        if ($this->hasModel()) {
            echo Html::activeTextarea($this->model, $this->attribute, $this->options);
        } else {
            echo Html::textarea($this->name, $this->value, $this->options);
        }

        echo Html::endTag('div');
        $js = [
            'mihaildev.ckEditor.registerOnChange('.Json::encode($this->options['id']).');'
        ];

        if(isset($this->editorOptions['filebrowserUploadUrl']))
            $js[] = "mihaildev.ckEditor.registerCsrf();";

        if(!isset($this->editorOptions['on']['instanceReady']))
            $this->editorOptions['on']['instanceReady'] = new JsExpression("function( ev ){".implode(' ', $js)."}");

        if($this->_inline){
            $JavaScript = "CKEDITOR.inline(";
            $JavaScript .= Json::encode($this->options['id']);
            $JavaScript .= empty($this->editorOptions) ? '' : ', '.Json::encode($this->editorOptions);

            $JavaScript .= ");";
            $JavaScript .= "$('#".$this->options['id']."').on('change', function() {
                function periodicData(data) {
                    $.ajax({
                        type: 'POST',
                        url: '".$this->dataUrlEdit."',
                        dataType: 'json',
                        data: {data: data,'attr':'".$this->name."'},
                        beforeSend: function (data) {},
                        success: function (data) {console.log(data)},
                        error: function (xhr, ajaxOptions, thrownError) {
                            alert(xhr.status);
                            alert(thrownError);
                        },
                        complete: function (data) {}
                    });
                }
                periodicData($(this).val());
            });";

            $this->getView()->registerJs($JavaScript, View::POS_END);
            $this->getView()->registerCss('#'.$this->containerOptions['id'].', #'.$this->containerOptions['id'].' .cke_textarea_inline{height: '.$this->editorOptions['height'].'px;}');
        }else{
            $JavaScript = "CKEDITOR.replace(";
            $JavaScript .= Json::encode($this->options['id']);
            $JavaScript .= empty($this->editorOptions) ? '' : ', '.Json::encode($this->editorOptions);
            $JavaScript .= ");";

            $this->getView()->registerJs($JavaScript, View::POS_END);
        }
    }
}
