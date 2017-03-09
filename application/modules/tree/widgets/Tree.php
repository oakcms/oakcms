<?php

/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

namespace app\modules\tree\widgets;

class Tree extends \yii\base\Widget
{
    public $model = null;
    public $updateUrl = 'category/update';
    public $updateNestableUrl = 'category/update-nestable';
    public $viewUrl = 'product/index';
    public $deleteUrl = 'category/delete';
    public $viewUrlToSearch = true;
    public $viewUrlModelName = 'ProductSearch';
    public $viewUrlModelField = 'category_id';
    public $orderField = 'sort';
    public $parentField = 'parent_id';
    public $idField = 'id';
    public $view = 'index';

    public function init()
    {
        parent::init();

        \app\modules\tree\assets\WidgetAsset::register($this->getView());
    }

    public function run()
    {
        $model = $this->model;

        if ($this->orderField) {
            $list = $model::find()->orderBy($this->orderField)->asArray()->all();
        } else {
            $list = $model::find()->asArray()->all();
        }
        $itemsTree = self::buildArray($list, 0, $this->idField, $this->parentField);

        return $this->render($this->view, [
            'categoriesTree' => self::treeBuild($itemsTree),
            'widget' => $this,
        ]);
    }

    private function buildArray(
        $items, $currentElementId = 0, $idKeyname = 'id', $parentIdKeyname = 'parent_id', $parentarrayName = 'childs'
    ) {
        if (empty($items)) return [];
        $return = [];
        foreach ($items as $item) {
            if ($item[$parentIdKeyname] == $currentElementId) {
                $item[$parentarrayName] = self::buildArray($items, $item[$idKeyname], $idKeyname, $parentIdKeyname, $parentarrayName);
                $return[] = $item;
            }
        }

        return $return;
    }

    private function treeBuild($items)
    {
        $return = '';
        foreach ($items as $item) {
            $return .= '<li class="dd-item dd3-item" data-id="'.$item[$this->idField].'">';
            $return .= $this->render('parts/tree_inlist.php', ['widget' => $this, 'category' => $item]);
            if (!empty($item['childs'])) {
                $return .= '<ol class="dd-list">';
                $return .= $this->treeBuild($item['childs']);
                $return .= '</ol>';
            }
            $return .= '</li>';
        }

        return $return;
    }
}
