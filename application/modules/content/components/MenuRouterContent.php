<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\content\components;

use app\modules\content\models\ContentPages;
use app\modules\menu\behaviors\MenuMap;
use app\modules\menu\behaviors\MenuRouter;
use app\modules\menu\behaviors\MenuRequestInfo;
use app\modules\menu\models\MenuItem;
use app\modules\content\models\ContentCategory;
use app\modules\content\models\ContentArticles;

class MenuRouterContent extends MenuRouter
{
    private $_categoryPaths = [];
    private $_articlesPaths = [];
    private $_pagesPaths = [];

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
                'menuRoute' => 'content/page/view',
                'handler'   => 'parsePageView',
            ],
            [
                'menuRoute' => 'content/article/view',
                'handler'   => 'parseArticleView',
            ]
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
                'requestParams' => ['slug'],
                'handler'       => 'createCategoryView',
            ],
            [
                'requestRoute'  => 'content/page/view',
                'requestParams' => ['slug'],
                'handler'       => 'createPageView',
            ],
            [
                'requestRoute'  => 'content/article/view',
                'requestParams' => ['catslug', 'slug'],
                'handler'       => 'createArticleView',
            ]
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
                ($category = ContentCategory::find()
                    ->select(['{{%content_category}}.id'])
                    ->joinWith(['translations'])
                    ->where(['{{%content_category_lang}}.slug' => $requestInfo->menuParams['slug']])
                    ->published()
                    ->asArray()
                    ->one()) &&
                ($article = ContentArticles::find()
                    ->select(['{{%content_articles}}.category_id', '{{%content_articles}}.id'])
                    ->joinWith(['translations'])
                    ->where(['{{%content_articles_lang}}.slug' => $requestInfo->requestRoute])
                    ->published()
                    ->asArray()
                    ->one()) &&
                $category['id'] == $article['category_id']
            ) {
                return ['content/article/view', ['catslug' => $requestInfo->menuParams['slug'], 'slug' => $requestInfo->requestRoute]];
            }
        }

        /** @var ContentCategory $menuCategory */
        if (
            $menuCategory = ContentCategory::find()
                ->joinWith(['translations'])
                ->where(['{{%content_category_lang}}.slug' => $requestInfo->menuParams['slug']])
                ->published()
                ->one()
        ) {
            return ['content/category/view', ['slug' => $menuCategory->slug]];
        }
    }

    /**
     * @param MenuRequestInfo $requestInfo
     *
     * @return array
     */
    public function parsePageView($requestInfo)
    {
        if(isset($requestInfo->requestRoute) && $requestInfo->requestRoute != '') {
            if($page = ContentPages::findOne([$requestInfo->requestRoute])) {
                return ['content/page/view', ['slug' => $page->slug]];
            }
        }
        /** @var ContentCategory $menuCategory */
        if ($menuPage = ContentPages::findOne([$requestInfo->menuParams['slug']])) {
            return ['content/page/view', ['slug' => $menuPage->slug]];
        }
    }

    /**
     * @param MenuRequestInfo $requestInfo
     *
     * @return array
     */
    public function parseArticleView($requestInfo)
    {
        /** @var ContentArticles $menuCategory */
        $categoryModel = ContentCategory::find()->published()
            ->joinWith(['translations'])
            ->andWhere(['{{%content_category_lang}}.slug' => $requestInfo->menuParams['catslug']])
            ->one();

        $model = ContentArticles::find()->published()
            ->joinWith(['translations'])
            ->andWhere(['{{%content_articles_lang}}.slug' => $requestInfo->menuParams['slug']])
            ->one();

        if($model !== null || $categoryModel !== null) {
            return ['content/article/view', ['slug' => $model->slug]];
        }
    }

    /**
     * @param MenuRequestInfo $requestInfo
     *
     * @return mixed|null|string
     */
    public function createCategoryView($requestInfo)
    {
        if ($path = $requestInfo->menuMap->getMenuPathByRoute(MenuItem::toRoute('content/category/view', ['slug' => $requestInfo->requestParams['slug']]))) {
            unset($requestInfo->requestParams['slug']);

            return MenuItem::toRoute($path, $requestInfo->requestParams);
        } else {
            return "content/category/" . $requestInfo->requestParams['slug'];
        }
    }

    /**
     * @param MenuRequestInfo $requestInfo
     *
     * @return mixed|null|string
     */
    public function createPageView($requestInfo)
    {
        if ($path = $requestInfo->menuMap->getMenuPathByRoute(MenuItem::toRoute('content/page/view', ['slug' => $requestInfo->requestParams['slug']]))) {
            unset($requestInfo->requestParams['slug']);

            return MenuItem::toRoute($path, $requestInfo->requestParams);
        }

        return $this->findPageMenuPath($requestInfo->requestParams['slug'], $requestInfo->menuMap);
    }

    /**
     * @param MenuRequestInfo $requestInfo
     *
     * @return mixed|null|string
     */
    public function createArticleView($requestInfo)
    {
        if ($path = $requestInfo->menuMap->getMenuPathByRoute(MenuItem::toRoute('content/article/view', ['slug' => $requestInfo->requestParams['slug']]))) {
            unset($requestInfo->requestParams['catslug'], $requestInfo->requestParams['slug']);

            return MenuItem::toRoute($path, $requestInfo->requestParams);
        }

        return $this->findArticleMenuPath($requestInfo->requestParams['catslug'], $requestInfo->requestParams['slug'], $requestInfo->menuMap);
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
     * @param $categorySlug
     * @param $menuMap MenuMap
     *
     * @return null|string
     */
    private function findCategoryMenuPath($categorySlug, $menuMap)
    {
        /** @var Category $category */

        if (!isset($this->_categoryPaths[$menuMap->language][$categorySlug])) {
            if ($path = $menuMap->getMenuPathByRoute(MenuItem::toRoute('content/category/view', ['slug' => $categorySlug]))) {
                $this->_categoryPaths[$menuMap->language][$categorySlug] = $path;
            } elseif (($category = ContentCategory::findOne($categorySlug)) && $category->parent_id != 0 && $path = $this->findCategoryMenuPath($category->parent_id, $menuMap)) {
                $this->_categoryPaths[$menuMap->language][$categorySlug] = $path . '/' . $category->slug;
            } else {
                $this->_categoryPaths[$menuMap->language][$categorySlug] = false;
            }
        }
        return $this->_categoryPaths[$menuMap->language][$categorySlug];
    }

    private function findPageMenuPath($pageSlug, $menuMap)
    {
        $page = ContentPages::find()->published()
            ->joinWith(['translations'])
            ->andWhere(['{{%content_pages_lang}}.slug' => $pageSlug])
            ->one();

        if (!isset($this->_pagesPaths[$menuMap->language][$pageSlug]) || $page) {
            if ($path = $menuMap->getMenuPathByRoute(MenuItem::toRoute('content/page/view', ['slug' => $pageSlug]))) {
                $this->_pagesPaths[$menuMap->language][$pageSlug] = $path;
            } else {
                $this->_pagesPaths[$menuMap->language][$pageSlug] = false;
            }
        }

        return $this->_pagesPaths[$menuMap->language][$pageSlug];
    }

    private function findArticleMenuPath($articleCatSlug, $articleSlug, $menuMap)
    {
        $category = ContentCategory::find()->published()
            ->joinWith(['translations'])
            ->andWhere(['{{%content_category_lang}}.slug' => $articleCatSlug])
            ->one();

        $article = ContentArticles::find()->published()
            ->joinWith(['translations'])
            ->andWhere(['{{%content_articles_lang}}.slug' => $articleSlug])
            ->one();

        if (!isset($this->_articlesPaths[$menuMap->language][$articleSlug]) || $category || $article) {
            if ($path = $menuMap->getMenuPathByRoute(MenuItem::toRoute('content/article/view', ['catslug' => $articleCatSlug, 'slug' => $articleSlug]))) {
                $this->_articlesPaths[$menuMap->language][$articleSlug] = $path;
            } elseif (isset($article->category) && ($path = $this->findCategoryMenuPath($article->category->slug, $menuMap))) {
                $this->_articlesPaths[$menuMap->language][$articleSlug] = $path . '/' . $article->slug;
            } else {
                $this->_articlesPaths[$menuMap->language][$articleSlug] = false;
            }
        }

        return $this->_articlesPaths[$menuMap->language][$articleSlug];
    }
}
