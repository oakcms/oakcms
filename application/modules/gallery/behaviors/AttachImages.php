<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\gallery\behaviors;

use app\modules\gallery\models\Image;
use app\modules\gallery\models\PlaceHolder;
use app\modules\gallery\ModuleTrait;
use yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\BaseFileHelper;
use yii\web\UploadedFile;

class AttachImages extends Behavior
{
    use ModuleTrait;

    public $createAliasMethod = false;
    public $modelClass = null;
    public $uploadsPath = '';
    public $mode = 'gallery';
    public $webUploadsPath = '/uploads';
    public $allowExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    public $inputName = 'galleryFiles';
    private $doResetImages = true;

    public function init()
    {
        if (empty($this->uploadsPath) && !Yii::$app->request->isConsoleRequest) {
            $this->uploadsPath = Yii::getAlias(
                yii\helpers\ArrayHelper::getValue(
                    Yii::$app->getModule('admin')->getModule('gallery'),
                    'imagesStorePath',
                    '@webroot/uploads/store'
                )
            );
        }
    }

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_UPDATE => 'setImages',
            ActiveRecord::EVENT_AFTER_INSERT  => 'setImages',
            ActiveRecord::EVENT_AFTER_DELETE  => 'removeImages'
        ];
    }

    public function clearImagesCache()
    {
        $cachePath = $this->getModule()->getCachePath();
        $subdir = $this->getModule()->getModelSubDir($this->owner);
        $dirToRemove = $cachePath . '/' . $subdir;

        if (preg_match('/' . preg_quote($cachePath, '/') . '/', $dirToRemove)) {
            BaseFileHelper::removeDirectory($dirToRemove);

            return true;
        } else {
            return false;
        }
    }

    public function getImages()
    {
        $finder = $this->getImagesFinder();
        $imageQuery = Image::find()->where($finder);
        $imageQuery->orderBy(['isMain' => SORT_DESC, 'sort' => SORT_DESC, 'id' => SORT_ASC]);
        $imageRecords = $imageQuery->all();

        return $imageRecords;
    }

    private function getImagesFinder($additionWhere = false)
    {
        $base = [
            'itemId'    => $this->owner->id,
            'modelName' => $this->getModule()->getShortClass($this->owner),
        ];

        if ($additionWhere) {
            $base = \yii\helpers\BaseArrayHelper::merge($base, $additionWhere);
        }

        return $base;
    }

    public function getImageByName($name)
    {
        if ($this->getModule()->className === null) {
            $imageQuery = Image::find();
        } else {
            $class = $this->getModule()->className;
            $imageQuery = $class::find();
        }

        $finder = $this->getImagesFinder(['name' => $name]);
        $imageQuery->where($finder);
        $imageQuery->orderBy(['isMain' => SORT_DESC, 'id' => SORT_ASC]);
        $img = $imageQuery->one();

        if (!$img) {
            return $this->getModule()->getPlaceHolder();
        }

        return $img;
    }

    public function removeImages()
    {
        $images = $this->owner->getImages();

        if (count($images) < 1) {
            return true;
        } else {
            foreach ($images as $image) {
                $this->owner->removeImage($image);
            }
        }
    }

    public function removeImage(Image $img)
    {
        $img->clearCache();

        $storePath = $this->getModule()->getStorePath();
        $fileToRemove = $storePath . DIRECTORY_SEPARATOR . $img->filePath;

        if (preg_match('@\.@', $fileToRemove) and is_file($fileToRemove)) {
            $countImages = Image::find()->where(['filePath' => $img->filePath])->count();
            if($countImages < 2) {
                $dir = dirname($fileToRemove);
                unlink($fileToRemove);
                @rmdir($dir);
            }
        }

        $img->delete();
    }

    public function getGalleryMode()
    {
        return $this->mode;
    }

    public function setImages($event)
    {
        if ($this->doResetImages) {
            $userImages = UploadedFile::getInstancesByName($this->getInputName());

            if ($userImages) {
                foreach ($userImages as $file) {
                    if (in_array(strtolower($file->extension), $this->allowExtensions)) {

                        if (!file_exists($this->uploadsPath)) {
                            mkdir($this->uploadsPath, 0777, true);
                        }

                        $file->saveAs("{$this->uploadsPath}/{$file->baseName}.{$file->extension}");

                        if ($this->owner->getGalleryMode() == 'single') {
                            foreach ($this->owner->getImages() as $image) {
                                $image->delete();
                            }
                        }

                        $this->attachImage("{$this->uploadsPath}/{$file->baseName}.{$file->extension}");
                    }
                }

                $this->doResetImages = false;
                $this->owner->save(false);
            }
        }

        return $this;
    }

    public function getInputName()
    {
        return $this->inputName;
    }

    /**
     * @param string $absolutePath
     * @param bool   $isMain
     *
     * @return Image|bool
     * @throws \Exception
     */
    public function attachImage($absolutePath, $isMain = false)
    {
        if (!preg_match('#http#', $absolutePath)) {
            if (!file_exists($absolutePath)) {
                throw new \Exception('File not exist! :' . $absolutePath);
            }
        }

        if (!$this->owner->id) {
            throw new \Exception('Owner must have id when you attach image!');
        }

        $pictureFileName =
            substr(md5(microtime(true)
                . $absolutePath), 4, 6)
            . '.'
            . pathinfo($absolutePath, PATHINFO_EXTENSION);

        $pictureSubDir = $this->getModule()->getModelSubDir($this->owner);
        $storePath = $this->getModule()->getStorePath($this->owner);

        $newAbsolutePath = $storePath
            . DIRECTORY_SEPARATOR
            . $pictureSubDir
            . DIRECTORY_SEPARATOR
            . $pictureFileName;

        BaseFileHelper::createDirectory($storePath . DIRECTORY_SEPARATOR . $pictureSubDir, 0775, true);

        copy($absolutePath, $newAbsolutePath);

        if ($this->modelClass === null) {
            $image = new Image();
        } else {
            $image = new ${$this->modelClass}();
        }

        $image->itemId = $this->owner->id;
        $image->filePath = $pictureSubDir . '/' . $pictureFileName;
        $image->modelName = $this->getModule()->getShortClass($this->owner);
        $image->urlAlias = $this->getAlias($image);

        if (!$image->save()) {
            return false;
        }

        if (count($image->getErrors()) > 0) {
            $ar = array_shift($image->getErrors());

            unlink($newAbsolutePath);
            throw new \Exception(array_shift($ar));
        }
        $img = $this->owner->getImage();

        if (is_object($img) && get_class($img) == 'app\modules\gallery\models\PlaceHolder' or $img == null or $isMain) {
            $this->setMainImage($image);
        }

        return $image;
    }

    private function getAlias()
    {
        $aliasWords = $this->getAliasString();
        $imagesCount = count($this->owner->getImages());

        return $aliasWords . '-' . intval($imagesCount + 1);
    }

    private function getAliasString()
    {
        if ($this->createAliasMethod) {
            $string = $this->owner->{$this->createAliasMethod}();
            if (!is_string($string)) {
                throw new \Exception("Image's url must be string!");
            } else {
                return $string;
            }

        } else {
            return substr(md5(microtime()), 0, 10);
        }
    }

    public function setMainImage($img)
    {
        if ($this->owner->id != $img->itemId) {
            throw new \Exception('Image must belong to this model');
        }

        $counter = 1;
        $img->setMain(true);
        $img->urlAlias = $this->getAliasString() . '-' . $counter;
        $img->save();

        $images = $this->owner->getImages();

        foreach ($images as $allImg) {
            if ($allImg->id == $img->id) {
                continue;
            } else {
                $counter++;
            }

            $allImg->setMain(false);
            $allImg->urlAlias = $this->getAliasString() . '-' . $counter;
            $allImg->save();
        }

        $this->owner->clearImagesCache();
    }

    public function hasImage()
    {
        return ($this->getImage() instanceof PlaceHolder) ? false : true;
    }

    public function getImage()
    {
        $finder = $this->getImagesFinder();
        $imageQuery = Image::find()->where($finder);
        $imageQuery->orderBy(['isMain' => SORT_DESC, 'sort' => SORT_DESC, 'id' => SORT_ASC]);
        $image = $imageQuery->one();

        if ($image === null) {
            return $this->getModule()->getPlaceHolder();
        }

        return $image;
    }
}
