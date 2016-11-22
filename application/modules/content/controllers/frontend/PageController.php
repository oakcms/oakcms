<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 21.09.2016
 * Project: osnovasite
 * File name: PageController.php
 */

namespace app\modules\content\controllers\frontend;


use app\components\Controller;
use app\modules\content\models\ContentPages;
use app\modules\menu\api\Menu;
use app\modules\menu\models\MenuItems;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;

class PageController extends Controller
{

    public function actionView($slug, $slugMenu = null) {

        $model = $this->findModel($slug);

        if(($menu =  MenuItems::find()
            ->joinWith(['translations'])
            ->andWhere(['{{%menu_items}}.disable_breadcrumbs' => 0])
            ->andWhere([
                'or',
                [
                    '{{%menu_items_lang}}.url' => 'page/'.$slug
                ],
                [
                    '{{%menu_items_lang}}.url' => '/page/'.$slug
                ],
                [
                    '{{%menu_items_lang}}.url' => \yii\helpers\Url::to(['/system/default']).'page/'.$slug
                ]
            ])
            ->one())) {


            /*if(($mParent = $menu->parents(1)->one())) {
                $arr = explode('/', $mParent->url);
                var_dump($arr);
                if(end($arr) != $slugMenu)
                    throw new NotFoundHttpException('The requested page does not exist.');
            }*/

            $breadcrumbs = Menu::getBreadcrumbsById($menu->id);
        } else {
            $breadcrumbs = Menu::getBreadcrumbs('page/'.$slug);
        }

        return $this->render('view', ['breadcrumbs' => $breadcrumbs, 'model' => $model]);
    }

    protected function findModel($slug)
    {
        if (($model = ContentPages::find()->published()->joinWith(['translations'])->andWhere(['{{%content_pages_lang}}.slug'=>$slug])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

    }
}
