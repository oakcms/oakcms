<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\gallery;

use Yii;
use app\modules\admin\rbac\Rbac;
use app\modules\gallery\models\PlaceHolder;
use app\modules\gallery\models\Image;

class Module extends \yii\base\Module
{
    public $imagesStoreUrl = '@web/uploads/store';
    public $imagesStorePath = '@webroot/uploads/store';
    public $imagesCachePath = '@webroot/uploads/imgCache';
    public $graphicsLibrary = 'GD';
    public $placeHolderPath;
    public $waterMark = false;
    public $adminRoles = [Rbac::PERMISSION_ADMIN_PANEL];
    public $settings = [];

    public function getImage($item, $dirtyAlias)
    {
        $params = $data = $this->parseImageAlias($dirtyAlias);

        $alias = $params['alias'];
        $size = $params['size'];

        $itemId = preg_replace('/[^0-9]+/', '', $item);
        $modelName = preg_replace('/[0-9]+/', '', $item);


        $image = Image::find()
            ->where([
                'modelName' => $modelName,
                'itemId' => $itemId,
                'urlAlias' => $alias
            ])
            ->one();

        if(!$image) {
            return $this->getPlaceHolder();
        }

        return $image;
    }

    public function getStorePath()
    {
        return Yii::getAlias($this->imagesStorePath);
    }

    public function getStoreUrl()
    {
        return Yii::getAlias($this->imagesStoreUrl);
    }


    public function getCachePath()
    {
        return Yii::getAlias($this->imagesCachePath);

    }

    public function getModelSubDir($model)
    {
        $modelName = $this->getShortClass($model);
        $modelDir = $modelName . 's/' . $modelName . $model->id;

        return $modelDir;
    }


    public function getShortClass($obj)
    {
        $className = get_class($obj);

        if (preg_match('@\\\\([\w]+)$@', $className, $matches)) {
            $className = $matches[1];
        }

        return $className;
    }

    public function parseSize($notParsedSize)
    {
        $sizeParts = explode('x', $notParsedSize);
        $part1 = (isset($sizeParts[0]) and $sizeParts[0] != '');
        $part2 = (isset($sizeParts[1]) and $sizeParts[1] != '');
        if ($part1 && $part2) {
            if (intval($sizeParts[0]) > 0
                &&
                intval($sizeParts[1]) > 0
            ) {
                $size = [
                    'width' => intval($sizeParts[0]),
                    'height' => intval($sizeParts[1])
                ];
            } else {
                $size = null;
            }
        } elseif ($part1 && !$part2) {
            $size = [
                'width' => intval($sizeParts[0]),
                'height' => null
            ];
        } elseif (!$part1 && $part2) {
            $size = [
                'width' => null,
                'height' => intval($sizeParts[1])
            ];
        } else {
            throw new \Exception('Something bad with size, sorry!');
        }

        return $size;
    }

    public function parseImageAlias($parameterized)
    {
        $params = explode('_', $parameterized);

        if (count($params) == 1) {
            $alias = $params[0];
            $size = null;
        } elseif (count($params) == 2) {
            $alias = $params[0];
            $size = $this->parseSize($params[1]);
            if (!$size) {
                $alias = null;
            }
        } else {
            $alias = null;
            $size = null;
        }


        return ['alias' => $alias, 'size' => $size];
    }


    public function init()
    {
        parent::init();
        $this->placeHolderPath = 'das';
        if (!$this->imagesStorePath
            or
            !$this->imagesCachePath
            or
            $this->imagesStorePath == '@app'
            or
            $this->imagesCachePath == '@app'
        )
            throw new \Exception('Setup imagesStorePath and imagesCachePath images module properties!!!');
    }

    public function getPlaceHolder(){

        if($this->placeHolderPath){
            return new PlaceHolder();
        }else{
            return null;
        }
    }
}
