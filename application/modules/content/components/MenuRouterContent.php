<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1
 *
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 08.01.2017
 * Project: kn-group-site
 * File name: MenuRouteContent.php
 */
namespace app\modules\content\components;

use app\components\menu\MenuRouter;
use app\components\menu\MenuRequestInfo;
use app\modules\menu\models\MenuItem;
use app\modules\content\models\ContentCategory;
use app\modules\content\models\ContentArticles;

class MenuRouterContent extends MenuRouter
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
                'menuRoute' => 'content/category/view',
                'handler'   => 'parseCategoryView',
            ],
            [
                'menuRoute' => 'content/product/view',
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
                'requestRoute'  => 'content/category/view',
                'requestParams' => ['id'],
                'handler'       => 'createCategoryView',
            ],
            [
                'requestRoute'  => 'content/product/view',
                'requestParams' => ['id'],
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
                ($category = ContentCategory::findOne([$requestInfo->menuParams['id']])) &&
                ($article = ContentArticles::findOne([$requestInfo->requestRoute])) &&
                $category->id == $article->category_id
            ) {
                return ['content/article/view', ['slug' => $article->slug]];
            }
        }
        /** @var ContentCategory $menuCategory */
        if ($menuCategory = ContentCategory::findOne([$requestInfo->menuParams['id']])) {
            return ['content/category/view', ['slug' => $menuCategory->slug]];
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
        if ($path = $requestInfo->menuMap->getMenuPathByRoute(MenuItem::toRoute('content/category/view', ['id' => $requestInfo->requestParams['id']]))) {
            unset($requestInfo->requestParams['id']);

            return MenuItem::toRoute($path, $requestInfo->requestParams);
        } else {
            return "shop/category/" . $requestInfo->requestParams['id'];
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
