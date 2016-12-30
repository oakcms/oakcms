<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 23.12.2016
 * Project: oakcms
 * File name: ShowMainGallary.php
 */

namespace app\modules\shop\widgets;


use app\modules\shop\models\Product;
use yii\base\Widget;
use yii\bootstrap\Html;
use yii\caching\Cache;
use yii\di\Instance;
use yii\helpers\ArrayHelper;

class ShowMainGallery extends Widget
{
    /**
     * @var Product
     */
    public $model = null;
    public $content = "";
    public $tag = 'div';
    public $options = [];
    public $template = "{mainImage}{content}{thumbs}";
    public $mainImageOptions = [];
    public $mainImageLinkOptions = [];
    public $itemOptions = [];
    public $itemTemplate = "\n<a href=\"{url}\">\n{image}\n</a>\n";
    private $_items;


    /**
     * @var string|Cache
     */
    public $cache = 'cache';

    /**
     * @var int
     */
    public $cacheDuration;

    /**
     * @var \yii\caching\Dependency
     */
    public $cacheDependency;

    public function init()
    {
        \app\modules\shop\assets\MainGalleryAsset::register($this->getView());

        return parent::init();
    }

    public function run()
    {
        if ($this->cache) {
            /** @var Cache $cache */
            $this->cache = Instance::ensure($this->cache, Cache::className());
            $cacheKey = [__CLASS__, $this->_items, $this->model->id];

            if (($this->_items = $this->cache->get($cacheKey)) === false) {

                $this->_items = $this->model->getImages();

                $this->cache->set($cacheKey, $this->_items, $this->cacheDuration, $this->cacheDependency);
            }
        } else {
            $this->_items = $this->model->getImages();
        }

        $items = $this->_items;


        if (!empty($items)) {
            $options = $this->options;

            $tag = ArrayHelper::remove($options, 'tag', 'div');
            $options = ArrayHelper::merge($options, ['data-magnific-galley' => true, 'data-product-photo-scope' => true, 'class' => 'product-photo']);

            echo Html::tag($tag, $this->renderItems($items), $options);
        }
    }

    /**
     * Recursively renders the items gallery (without the container tag).
     * @param array $items the items gallery to be rendered recursively
     * @return string the rendering result
     */
    protected function renderItems($items)
    {
        $n = count($items);

        $mainImageTag = ArrayHelper::remove($this->mainImageOptions, 'tag', 'div');

        $mainImageLinkOptions = ArrayHelper::merge($this->mainImageLinkOptions, [
            'target' => '_blank',
            'class' => 'product-photo__item product-photo__item--lg',
            'data-product-photo-link' => true,
            'data-magnific-galley-main' => true,
            'data-magnific-galley-title' => $this->model->name,
            'data-magnific-galley-close-text' => \Yii::t('shop', 'Close'),
        ]);
        $mainImageOptions = $this->mainImageLinkOptions;

        $lines = [];
        $linesThumbImages = [];

        $lines[] = Html::beginTag($mainImageTag, $mainImageOptions);
        $lineMainImage = Html::a(
            Html::img($this->model->getImage()->getUrl('835x'), [
                'class' => 'product-photo__img',
                'data-product-photo' => true,
                'data-zoom-image-small' => true,
                'data-zoom-image' => $this->model->getImage()->getUrl()
            ]) .
            Html::tag('span', '', ['class' => 'product-photo__zoom hidden hidden-sm hidden-xs', 'data-zoom-wrapper' => true]),
            $this->model->getImage()->getUrl(),
            $mainImageLinkOptions
        );

        $linesThumbImages[] = Html::beginTag('ul', ['class' => 'product-photo__thumbs']);
        foreach ($items as $i => $item) {
            $thumbImageLinkOptions = [
                'class' => 'product-photo__thumb-item',
                'data-product-photo-thumb' => true,
                'data-magnific-galley-thumb' => true,
                'data-magnific-galley-title' => $this->model->name,
            ];
            if($item->isMain == 1 OR $i == 0) {
                $thumbImageLinkOptions = ArrayHelper::merge($thumbImageLinkOptions, ['data-product-photo-thumb-active' => true]);
            }
            $linesThumbImages[] = Html::tag(
                'li',
                Html::a(Html::img($item->getUrl('x100'), ['class' => 'product-photo__thumb-img']), $item->getUrl(), $thumbImageLinkOptions),
                ['class' => 'product-photo__thumb']
            );
        }
        $linesThumbImages[] = Html::endTag('ul');
        $lines[] = strtr($this->template, [
            '{mainImage}' => $lineMainImage,
            '{content}' => $this->content,
            '{thumbs}' => implode("", $linesThumbImages),
        ]);
        $lines[] = Html::endTag($mainImageTag);


        return implode("\n", $lines);
    }
}
