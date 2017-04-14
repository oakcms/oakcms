<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\components;

use app\modules\menu\models\MenuItem;
use Yii;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

class Request extends \yii\web\Request
{

    protected function createUrlToRedirect($url)
    {
        $queryParams = $this->getQueryParams();

        if(count($queryParams)) {
            $url = $url.'?'.http_build_query($queryParams);
        }

        return rtrim($url, '=');
    }

    public function resolve()
    {
        $result = Yii::$app->getUrlManager()->parseRequest($this);
        if ($result !== false) {
            list ($route, $params) = $result;

            if(Yii::$app->hasModule('menu')) {
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

                if(
                    urldecode($this->createUrlToRedirect($url)) !== rtrim(urldecode(Url::to()), '=') &&
                    $menuRoute != $homeMenuItem->link
                ) {
                    Yii::$app->getResponse()->redirect($url, 301);
                }

                if($menuPath) {
                    $homeUrl = Url::to(['/']);
                    if(
                        urldecode($this->createUrlToRedirect(ltrim(trim($homeUrl, '/').'/'.$menuPath, '/'))) != ltrim(urldecode(Url::to()), '/') &&
                        $menuRoute != $homeMenuItem->link &&
                        $menuStatus != 2
                    ) {
                        Yii::$app->getResponse()->redirect(trim($homeUrl, '/').'/'.$menuPath, 301);
                    } elseif(
                        urldecode(Url::to()) != urldecode($this->createUrlToRedirect($homeUrl)) &&
                        $menuRoute == $homeMenuItem->link &&
                        $menuStatus == 2
                    ) {

                        Yii::$app->getResponse()->redirect($this->createUrlToRedirect($homeUrl), 301);
                    }
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
