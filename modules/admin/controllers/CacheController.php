<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 14.06.2016
 * Project: oakcms
 * File name: CacheController.php
 */

namespace app\modules\admin\controllers;

use Yii;
use app\components\AdminController;

class CacheController extends AdminController
{

    public function actionIndex() {
        return $this->render('index');
    }

    public function actionFlushCache()
    {
        Yii::$app->cache->flush();
        $this->flash('success', Yii::t('admin', 'Cache flushed'));
        return $this->back();
    }

    public function actionClearAssets()
    {
        foreach(glob(Yii::$app->assetManager->basePath . DIRECTORY_SEPARATOR . '*') as $asset){
            if(is_link($asset)) {
                unlink($asset);
            } elseif(is_dir($asset)){
                $this->deleteDir($asset);
            } else {
                unlink($asset);
            }
        }
        $this->flash('success', Yii::t('admin', 'Assets cleared'));
        return $this->back();
    }

    private function deleteDir($directory)
    {
        $iterator = new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::CHILD_FIRST);
        foreach($files as $file) {
            if ($file->isDir()){
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        return rmdir($directory);
    }
}
