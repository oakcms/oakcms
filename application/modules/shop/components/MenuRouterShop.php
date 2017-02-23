<?php
/**
 * @link      https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license   https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package   yii2-platform-basic
 * @version   1.0.0
 */

namespace app\modules\shop\components;

use app\modules\menu\behaviors\MenuMap;
use app\modules\menu\behaviors\MenuRequestInfo;
use app\modules\menu\behaviors\MenuRouter;
use app\modules\menu\models\MenuItem;
use app\modules\shop\models\Category;
use app\modules\shop\models\Product;

/**
 * Class MenuRouterShop
 * @package oakcms
 */
class MenuRouterShop extends MenuRouter
{
    private $_categoryPaths = [];
    private $_productPaths = [];

    /**
     * @inheritdoc
     */
    public function parseUrlRules()
    {
        return [
            [
                'menuRoute' => 'shop/category/view',
                'handler'   => 'parseCategoryView',
            ],
            [
                'menuRoute' => 'shop/product/view',
                'handler'   => 'parseProductView',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function createUrlRules()
    {
        return [
            [
                'requestRoute'  => 'shop/category/view',
                'requestParams' => ['slug'],
                'handler'       => 'createCategoryView',
            ],
            [
                'requestRoute'  => 'shop/product/view',
                'requestParams' => ['slug'],
                'handler'       => 'createProductView',
            ],
        ];
    }

    /**
     * @param MenuRequestInfo $requestInfo
     *
     * @return array
     */
    public function parseCategoryView($requestInfo)
    {
        if(isset($requestInfo->requestRoute) && $requestInfo->requestRoute != '') {
            if(
                ($category = Category::findOne(['slug' => $requestInfo->menuParams['slug']])) &&
                ($product = Product::findOne(['slug' => $requestInfo->requestRoute])) &&
                $product->category->id == $product->category_id
            ) {
                return ['shop/product/view', ['slug' => $product->slug]];
            }
        }
        /** @var Category $menuCategory */
        if ($menuCategory = Category::findOne(['slug' => $requestInfo->menuParams['slug']])) {
            return ['shop/category/view', ['slug' => $menuCategory->slug]];
        }
    }

    /**
     * @param MenuRequestInfo $requestInfo
     *
     * @return array
     */
    public function parseProductView($requestInfo)
    {
        /** @var Category $menuCategory */
        if ($menuCategory = Product::findOne(['slug' => $requestInfo->menuParams['slug']])) {
            return ['shop/product/view', ['slug' => $menuCategory->slug]];
        }
    }

    /**
     * @param MenuRequestInfo $requestInfo
     *
     * @return mixed|null|string
     */
    public function createCategoryView($requestInfo)
    {
        if ($path = $requestInfo->menuMap->getMenuPathByRoute(MenuItem::toRoute('shop/category/view', ['slug' => $requestInfo->requestParams['slug']]))) {
            unset($requestInfo->requestParams['id'], $requestInfo->requestParams['slug']);

            return MenuItem::toRoute($path, $requestInfo->requestParams);
        } else {
            return "shop/category/" . $requestInfo->requestParams['slug'];
        }
    }

    /**
     * @param MenuRequestInfo $requestInfo
     *
     * @return mixed|null|string
     */
    public function createProductView($requestInfo)
    {
        if ($path = $requestInfo->menuMap->getMenuPathByRoute(MenuItem::toRoute('shop/product/view', ['slug' => $requestInfo->requestParams['slug']]))) {
            unset($requestInfo->requestParams['id'], $requestInfo->requestParams['slug']);

            return MenuItem::toRoute($path, $requestInfo->requestParams);
        }

        return $this->findProductMenuPath($requestInfo->requestParams['slug'], $requestInfo->menuMap);
    }

    /**
     * @param MenuRequestInfo $requestInfo
     *
     * @return mixed|null|string
     */
    public function createCategoryGuide($requestInfo)
    {
        //ищем пункт меню ссылающийся на категорию данного поста либо ее предков
        if (isset($requestInfo->requestParams['slug'])) {
            //можем привязаться к пункту меню ссылающемуся на категорию новостей к которой принадлежит данный пост(напрямую либо косвенно)
            if ($path = $this->findCategoryMenuPath($requestInfo->requestParams['slug'], $requestInfo->menuMap)) {
                unset($requestInfo->requestParams['id'], $requestInfo->requestParams['slug']);

                return MenuItem::toRoute($path, $requestInfo->requestParams);
            }
        }
    }

    /**
     * Находит путь к пункту меню ссылающемуся на категорию $categoryId, либо ее предка
     * Если путь ведет к предку, то достраиваем путь категории $categoryId
     *
     * @param $categoryId
     * @param $menuMap MenuMap
     *
     * @return null|string
     */
    private function findCategoryMenuPath($categorySlug, $menuMap)
    {
        /** @var Category $category */

        if (!isset($this->_categoryPaths[$menuMap->language][$categorySlug])) {
            if ($path = $menuMap->getMenuPathByRoute(MenuItem::toRoute('shop/category/view', ['slug' => $categorySlug]))) {
                $this->_categoryPaths[$menuMap->language][$categorySlug] = $path;
            } elseif (($category = Category::findOne($categorySlug)) && $category->parent_id != 0 && $path = $this->findCategoryMenuPath($category->parent_id, $menuMap)) {
                $this->_categoryPaths[$menuMap->language][$categorySlug] = $path . '/' . $category->slug;
            } else {
                $this->_categoryPaths[$menuMap->language][$categorySlug] = false;
            }
        }

        return $this->_categoryPaths[$menuMap->language][$categorySlug];
    }

    private function findProductMenuPath($productSlug, $menuMap)
    {
        $product = Product::findOne(['slug' => $productSlug]);

        if (!isset($this->_productPaths[$menuMap->language][$productSlug])) {
            if ($path = $menuMap->getMenuPathByRoute(MenuItem::toRoute('shop/product/view', ['slug' => $productSlug]))) {
                $this->_productPaths[$menuMap->language][$productSlug] = $path;
            } elseif (isset($product->category) && ($path = $this->findCategoryMenuPath($product->category->slug, $menuMap))) {
                $this->_productPaths[$menuMap->language][$productSlug] = $path . '/' . $product->slug;
            } else {
                $this->_productPaths[$menuMap->language][$productSlug] = false;
            }
        }

        return $this->_productPaths[$menuMap->language][$productSlug];
    }
}
