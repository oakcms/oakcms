<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 13.09.2016
 * Project: osnovasite
 * File name: Menu.php
 */

namespace app\modules\menu\api;

use app\components\API;
use app\modules\menu\models\MenuItem;
use yii\helpers\Json;
use yii\helpers\Url;

/**
 * Class Menu
 * @package oakcms
 *
 * @method array getMenuLvlAll(int $id) Вертає масив елементів вибриного меню
 * @method array getMenuLvl(int $id, int $startLvl, int $endLvl) Вертає масив елементів вибриного меню
 */

class Menu extends API  {

    /**
     * @param $id int Ідентефікатор меню
     * @return array
     */
    public function api_getMenuLvlAll($id) {
        $collection = MenuItem::find()->where(['menu_type_id' => $id])->orderBy(['ordering' => SORT_ASC])->all();
        return $this->generateMenuArray($collection);
    }

    /**
     * @param $id int Ідентефікатор меню
     * @param $startLvl int Початковий рівань вибірки
     * @param $endLvl int Кінцевий рівень вибірки
     * @return array
     */
    public function api_getMenuLvl($id, $startLvl, $endLvl) {
        $collection = MenuItem::find()
            ->andWhere(['menu_type_id' => $id])
            ->andFilterWhere(['<=', 'level', $endLvl])
            ->andFilterWhere(['>=', 'level', $startLvl])
            ->orderBy(['ordering' => SORT_ASC])
            ->all();

        return $this->generateMenuArray($collection);
    }

    /**
     * @param $collection array Масив меню
     * @return array
     */
    protected function generateMenuArray($rawItems) {


        $items = [];
        $urlManager = \Yii::$app->urlManager;

        /** @var MenuItem $model */
        while ($model = array_shift($rawItems)){
            $linkParams = (array)Json::decode($model->link_params);
            $items[] = [
                'id' => $model->id,
                'label' => @$linkParams['title'] ? $linkParams['title'] : $model->title,
                'url' => $model->link_type == MenuItem::LINK_ROUTE ? ($model->secure ? $urlManager->createAbsoluteUrl($model->getFrontendViewLink(), 'https') : $urlManager->createUrl($model->getFrontendViewLink())) : $model->link,
                'access_rule' => $model->access_rule,
                'submenuOptions' => [
                    'class' => 'level-' . $model->level
                ],
                'options' => [
                    'class' => @$linkParams['class'] ? $linkParams['class'] : null,
                    'target' => @$linkParams['target'] ? $linkParams['target'] : null,
                    'style' => @$linkParams['style'] ? $linkParams['style'] : null,
                    'rel' => @$linkParams['rel'] ? $linkParams['rel'] : null,
                    'onclick' => @$linkParams['onclick'] ? $linkParams['onclick'] : null,
                ]
            ];
        }

        return $items;
    }

    public static function getBreadcrumbs($slug) {
        $breadcrumbs = [];

        return $breadcrumbs;
    }

    public static function getBreadcrumbsById($id = null) {
        $breadcrumbs = [];

        if(($menu = MenuItem::find()->published()->andWhere(['id' => $id])->one()))
        {
            if(($rMenu = $menu->parents()->all())) {

                foreach ($rMenu as $item) {
                    $breadcrumbs[] = [
                        'label' => $item->title,
                        'url' => $item->url
                    ];
                }
            }
            $breadcrumbs[] = [
                'label' => $menu->title,
            ];
        }

        return $breadcrumbs;
    }
}
