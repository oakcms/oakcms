<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

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
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;

/**
 * Class Menu
 * @package oakcms
 *
 * @method static getMenuLvlAll(int $id) Вертає масив елементів вибриного меню
 * @method static getMenuLvl(int $id, int $startLvl, int $endLvl, $parent_id = false) Вертає масив елементів вибриного меню
 */

class Menu extends API
{
    /**
     * @param $id int Ідентефікатор меню
     * @return array
     */
    public function api_getMenuLvlAll($id) {
        $return = [];

        $collection = MenuItem::find()->type($id)->published()->language($this->language)->orderBy('lft')->all();

        if($collection !== null && count($collection) > 0) {
            $return = $this->generateMenuArray($collection, $collection[0]->level);
        }

        return $return;
    }

    /**
     * @param $id int Ідентефікатор меню
     * @param $startLvl int Початковий рівань вибірки
     * @param $endLvl int Кінцевий рівень вибірки
     * @param $parent_id mixed Вибірка піделементів меню
     * @return array
     */
    public function api_getMenuLvl($id, $startLvl, $endLvl, $parent_id = null) {
        $return = [];

        $collection = MenuItem::find()
            ->type($id)
            ->published()
            ->language($this->language)
            ->andFilterWhere(['<=', 'level', $endLvl + 1])
            ->andFilterWhere(['>=', 'level', $startLvl + 1])
            ->andFilterWhere(['=', 'parent_id', $parent_id])
            ->orderBy('lft')
            ->all();

        if($collection !== null && count($collection) > 0) {
            $return = $this->generateMenuArray($collection, $collection[0]->level);
        }

        return $return;
    }

    /**
     * Приводит выгрузку из бд к виду [[\yii\widgets\Menu::items]]
     * @param $rawItems array
     * @param $level integer
     * @return array
     */
    protected function generateMenuArray(&$rawItems, $level) {
        $items = [];
        $urlManager = \Yii::$app->urlManager;

        /** @var MenuItem $model */
        while ($model = array_shift($rawItems)) {
            if ($level == $model->level) {
                $linkParams = (array)Json::decode($model->link_params);

                $linkType = (
                    $model->link_type == MenuItem::LINK_ROUTE ?
                        (
                            $model->secure ?
                                $urlManager->createAbsoluteUrl($model->getFrontendViewLink(), 'https') :
                                $urlManager->createUrl($model->getFrontendViewLink())
                        ) : $model->link
                );

                $items[] = [
                    'id'             => $model->id,
                    'label'          => ArrayHelper::getValue($linkParams, 'title', $model->title),
                    'url'            => $linkType,
                    'access_rule'    => $model->access_rule,
                    'submenuOptions' => [
                        'class' => 'level-' . $model->level
                    ],
                    'options'        => [
                        'class'   => ArrayHelper::getValue($linkParams, 'class'),
                        'target'  => ArrayHelper::getValue($linkParams, 'target'),
                        'style'   => ArrayHelper::getValue($linkParams, 'style'),
                        'rel'     => ArrayHelper::getValue($linkParams, 'rel'),
                        'onclick' => ArrayHelper::getValue($linkParams, 'onclick')
                    ]
                ];
            } elseif ($level < $model->level) {
                array_unshift($rawItems, $model);
                $last = count($items) - 1;
                $items[$last]['items'] = $this->generateMenuArray($rawItems, $model->level);
            } else {
                array_unshift($rawItems, $model);
                return $items;
            }
        }

        return $items;
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
