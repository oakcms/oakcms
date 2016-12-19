<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace app\modules\shop\components;


use app\components\menu\MenuMap;
use app\components\menu\MenuRequestInfo;
use app\components\menu\MenuRouter;
use app\modules\menu\models\MenuItem;
use app\modules\shop\models\Category;

/**
 * Class MenuRouterShop
 * @package oakcms
 */
class MenuRouterShop extends MenuRouter
{
    /**
     * @inheritdoc
     */
    public function parseUrlRules()
    {
        return [
            [
                'menuRoute' => 'shop/category/view',
                'handler' => 'parseCategoryView'
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
                'requestRoute' => 'shop/category/view',
                'requestParams' => ['slug'],
                'handler' => 'createCategoryView'
            ]
        ];
    }

    /**
     * @param MenuRequestInfo $requestInfo
     * @return array
     */
    public function parseCategoryView($requestInfo)
    {
        /** @var Category $menuCategory */
        if ($menuCategory = Category::findOne(['slug' => $requestInfo->menuParams['slug']])) {
            return ['shop/category/view', ['slug' => $menuCategory->slug]];
        }
    }

    /**
     * @param MenuRequestInfo $requestInfo
     * @return mixed|null|string
     */
    public function createCategoryView($requestInfo)
    {
        if ($path = $requestInfo->menuMap->getMenuPathByRoute(MenuItem::toRoute('shop/category/view', ['slug' => $requestInfo->requestParams['slug']]))) {
            unset($requestInfo->requestParams['id'], $requestInfo->requestParams['slug']);
            return MenuItem::toRoute($path, $requestInfo->requestParams);
        } else {
            return "shop/category/".$requestInfo->requestParams['slug'];
        }
    }

    /**
     * @param MenuRequestInfo $requestInfo
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

    private $_categoryPaths = [];

    /**
     * Находит путь к пункту меню ссылающемуся на категорию $categoryId, либо ее предка
     * Если путь ведет к предку, то достраиваем путь категории $categoryId
     * @param $categoryId
     * @param $menuMap MenuMap
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
}
