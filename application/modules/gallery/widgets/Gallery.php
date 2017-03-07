<?php
namespace app\modules\gallery\widgets;

use kartik\file\FileInput;
use yii\helpers\Html;
use yii\helpers\Url;

class Gallery extends \yii\base\Widget
{
    public $model = null;
    public $previewSize = '140x140';
    public $fileInputPluginLoading = true;
    public $fileInputPluginOptions = [];
    public $label = 'Images';

    public function init()
    {
        $view = $this->getView();
        $view->on($view::EVENT_END_BODY, function ($event) {
            echo $this->render('modal');
        });
        \app\modules\gallery\assets\GalleryAsset::register($this->getView());
    }

    public function run()
    {
        $model = $this->model;
        $params = [];
        $img = '';
        $label = '<label class="control-label">' . $this->label . '</label>';
        if ($model->getGalleryMode() == 'single') {
            if ($model->hasImage()) {
                $image = $this->model->getImage();
                $img = $this->getImagePreview($image);
                $params = $this->getParams($image->id);
            }

            return Html::tag('div', $label . $img, $params) . '<br style="clear: both;" />' . $this->getFileInput();
        }
        $elements = $this->model->getImages();
        $cart = Html::ul(
            $elements,
            [
                'item'  => function ($item) {
                    return $this->row($item);
                },
                'class' => 'oak-gallery',
            ]);

        return Html::tag('div', $label . $cart . '<br style="clear: both;" />' . $this->getFileInput());
    }

    private function getImagePreview($image)
    {
        $size = (explode('x', $this->previewSize));

        $delete = Html::a('<i class="icon-trash"></i>', '#', [
            'data-action' => Url::toRoute(['/admin/gallery/default/delete']),
            'class' => 'delete btn red delete_image btn-small'
        ]);

        $write = Html::a('<i class="fa fa-edit"></i>', '#', [
            'data-action' => Url::toRoute(['/admin/gallery/default/modal']),
            'class' => 'write btn green change_image btn-small'
        ]);

        $img = Html::img($image->getUrl($this->previewSize), [
            'data-action' => Url::toRoute(['/admin/gallery/default/setmain']),
            'width' => $size[0],
            'height' => $size[1],
            'class' => 'thumb fancybox'
        ]);

        $a = Html::a($img, $image->getUrl());

        $ouput = '<div class="controls photo_album photo_album-v">';
        $ouput .= '<div class="fon"></div>';
        $ouput .= '<div class="btn-group btn-group-xs btn-group-solid f-s_0">';
        $ouput .= $write;
        $ouput .= $delete;
        $ouput .= '</div>';
        $ouput .= $a;
        $ouput .= '</div>';

        return $ouput;
    }

    private function getParams($id)
    {
        $model = $this->model;

        return [
            'class'      => 'oak-gallery-item',
            'data-model' => $model::className(),
            'data-id'    => $model->id,
            'data-image' => $id,
        ];
    }

    private function getFileInput()
    {
        return FileInput::widget([
            'name'          => $this->model->getInputName() . '[]',
            'options'       => [
                'accept'   => 'image/*',
                'multiple' => $this->model->getGalleryMode() == 'gallery',
            ],
            'pluginOptions' => $this->fileInputPluginOptions,
            'pluginLoading' => $this->fileInputPluginLoading,
        ]);
    }

    private function row($image)
    {
        if ($image instanceof \app\modules\gallery\models\PlaceHolder) {
            return '';
        }
        $class = ' oak-gallery-row';
        if ($image->isMain) {
            $class .= ' main';
        }
        $liParams = $this->getParams($image->id);
        $liParams['class'] .= $class;

        return Html::tag('li', $this->getImagePreview($image), $liParams);
    }
}
