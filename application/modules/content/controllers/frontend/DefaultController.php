<?php

namespace app\modules\content\controllers\frontend;

use app\modules\content\models\ContentArticles;
use yii\data\Pagination;
use yii\helpers\Url;
use yii\web\Controller;

/**
 * Default controller for the `content` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $query = ContentArticles::find()->published()->orderBy('id DESC');

        $countQuery = clone $query;
        $pages = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => 3,
            'forcePageParam' => false,
        ]);
        //$pages->pageSizeParam = false;

        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        return $this->render('index', [
            'models' => $models,
            'pages' => $pages,
        ]);
    }
}
