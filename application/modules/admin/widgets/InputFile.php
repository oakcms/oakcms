<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 11.07.2016
 * Project: events.timhome.vn.loc
 * File name: InputFile.php
 */

namespace app\modules\admin\widgets;


use mihaildev\elfinder\AssetsCallBack;
use yii\helpers\Json;

class InputFile extends \mihaildev\elfinder\InputFile
{

    public $controller = '/admin/file-manager-elfinder';
    public $options = ['class' => 'form-control'];
    public $template = '<div class="input-group">{input}<div class="input-group-btn">{button}</div></div>';

    public function run()
    {
        if ($this->hasModel()) {
            $replace['{input}'] = Html::activeTextInput($this->model, $this->attribute, $this->options);
        } else {
            $replace['{input}'] = Html::textInput($this->name, $this->value, $this->options);
        }

        $this->buttonOptions = array_merge($this->buttonOptions, ['class' => 'btn btn-default add-file', 'type' => 'button']);

        $replace['{button}'] = Html::tag('button',
            Html::tag(
                'i',
                '',
                ['class' => 'fa fa-file-image-o']
            ),
            $this->buttonOptions
        );

        echo strtr($this->template, $replace);

        AssetsCallBack::register($this->getView());

        if (!empty($this->multiple))
            $this->getView()->registerJs("mihaildev.elFinder.register(" . Json::encode($this->options['id']) . ", function(files, id){ var _f = []; for (var i in files) { _f.push(files[i].url); } \$('#' + id).val(_f.join(', ')).trigger('change', [files, id]); return true;}); $(document).on('click','#" . $this->buttonOptions['id'] . "', function(){mihaildev.elFinder.openManager(" . Json::encode($this->_managerOptions) . ");});");
        else
            $this->getView()->registerJs("mihaildev.elFinder.register(" . Json::encode($this->options['id']) . ", function(file, id){ \$('#' + id).val(file.url).trigger('change', [file, id]);; return true;}); $(document).on('click', '#" . $this->buttonOptions['id'] . "', function(){mihaildev.elFinder.openManager(" . Json::encode($this->_managerOptions) . ");});");
    }
}
