<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 14.12.2016
 * Project: oakcms
 * File name: Request.php
 */

namespace app\components;

use app\modules\menu\models\MenuItem;
use Yii;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

class Request extends \yii\web\Request
{
    public function resolve()
    {
        $result = Yii::$app->getUrlManager()->parseRequest($this);

        if ($result !== false) {
            list ($route, $params) = $result;

            $menuManager    = Yii::$app->menuManager;
            $menuRoute      = MenuItem::toRoute($route, $params);
            $menuPath       = $menuManager->menuMap->getMenuPathByRoute($menuRoute);
            $menu           = $menuManager->menuMap->getMenuByRoute($menuRoute);
            $menuStatus     = $menu ? $menu->status : 0;
            $homeMenuItem   = $menuManager->menuMap->getMainMenu();

            list ($routeURL, $paramsURL) = Yii::$app->urlManager->parseRequest($this);


            $currentURL = [$routeURL];
            foreach ($paramsURL as $k=>$item) {
                $currentURL[$k] = $item;
            }
            $url = Yii::$app->getUrlManager()->createUrl($currentURL);
            $queryParams = $this->getQueryParams();
            if(isset($queryParams['q'])) {
                unset($queryParams['q']);
            }
            if(count($queryParams)) {
                $url = $url.'?'.http_build_query($queryParams);
            }

            //  OR urldecode($url) !== urldecode(Url::to())
            if(urldecode($url) !== urldecode(Url::to()) && $menuRoute != $homeMenuItem->link) {
                Yii::$app->getResponse()->redirect($url, 301);
            }


            //var_dump($menuRoute);
            /*var_dump(
                urldecode(Url::to()), urldecode(Url::home()),
                $menuStatus, $menuRoute, $homeMenuItem->link);exit;
*/
            if($menuPath) {
                if(
                    $menuPath != ltrim(urldecode(Url::to()), '/') &&
                    $menuRoute != $homeMenuItem->link &&
                    $menuStatus != 2
                ) {
                    Yii::$app->getResponse()->redirect(Url::home().$menuPath, 301);
                } elseif(
                    urldecode(Url::to()) != urldecode(Url::home()) &&
                    $menuRoute == $homeMenuItem->link &&
                    $menuStatus == 2
                ) {
                    Yii::$app->getResponse()->redirect(Url::home(), 301);
                }
            }

            if ($this->getQueryParams() === null) {
                $_GET = $params + $_GET; // preserve numeric keys
            } else {
                $p = $params + $this->getQueryParams();
                $this->setQueryParams($p);
            }

            return [$route, $this->getQueryParams()];
        } else {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
    }
}
