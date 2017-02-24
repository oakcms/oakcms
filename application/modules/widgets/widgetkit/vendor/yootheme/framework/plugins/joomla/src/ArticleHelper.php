<?php

namespace YOOtheme\Framework\Joomla;

use ContentHelperRoute, JComponentHelper, JModelLegacy, JRoute, JText;

class ArticleHelper
{
    public function get($params)
    {
        // Ordering
        $direction = null;
        switch ($params['order']) {
            case 'featured':
                $ordering = 'fp.ordering';
                break;
            case 'random':
                $ordering = 'RAND()';
                break;
            case 'date':
                $ordering = 'created';
                break;
            case 'rdate':
                $ordering = 'created';
                $direction = 'DESC';
                break;
            case 'modified':
                $ordering = 'modified';
                break;
            case 'rmodified':
                $ordering = 'modified';
                $direction = 'DESC';
                break;
            case 'alpha':
                $ordering = 'title';
                break;
            case 'ralpha':
                $ordering = 'title';
                $direction = 'DESC';
                break;
            case 'hits':
                $ordering = 'hits';
                break;
            case 'rhits':
                $ordering = 'hits';
                $direction = 'DESC';
                break;
            case 'ordering':
            default:
                $ordering = 'a.ordering';
                break;
        }

        jimport('legacy.model.legacy');

        JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_content/models', 'ContentModel');
        $model = JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request' => true));
        $model->setState('params', JComponentHelper::getParams('com_content'));
        $model->setState('filter.published', 1);
        $model->setState('filter.access', true);
        $model->setState('list.ordering', $ordering);
        $model->setState('list.direction', $direction);
        $model->setState('list.start', 0);
        $model->setState('list.limit', (int) $params['items']);
        $model->setState('filter.language', \JLanguageMultilang::isEnabled());

        // categories filter
        if (($categories = (array) $params['catid']) && count($categories)) {
            $model->setState('filter.category_id', $categories);
        }

        $model->setState('filter.subcategories', $params['subcategories']);
        $model->setState('filter.max_category_levels', 999);

        // featured, accepted values ('hide' || 'only')
        if (!empty($params['featured'])) {
            $model->setState('filter.featured', $params['featured']);
        }

        return $model->getItems();
    }

    public function getUrl($item)
    {
        if (!class_exists('ContentHelperRoute')) {
            require_once(JPATH_SITE . '/components/com_content/helpers/route.php');
        }

        return JRoute::_(ContentHelperRoute::getArticleRoute($item->id, $item->catid));
    }
}
