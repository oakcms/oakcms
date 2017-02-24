<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 12.07.2016
 * Project: events.timhome.vn.loc
 * File name: MediaController.php
 */

namespace app\modules\admin\controllers;

use app\modules\content\models\ContentArticlesMedias;
use Imagine\Image\ManipulatorInterface;
use Yii;
use app\components\BackendController;
use app\modules\admin\models\Medias;
use yii\helpers\VarDumper;
use yii\imagine\Image;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class MediaController extends BackendController
{
    public function actionDelete($id, $type)
    {

        if($type == 'catalog') {

        } elseif($type == 'article') {
            if($model = ContentArticlesMedias::find()->where(['media_id'=>$id])->one())
                $model->delete();
        }
        $this->back();
    }

    public function actionFileTitle($id)
    {
        if(($model = Medias::findOne($id)))
        {
            if(Yii::$app->request->post('title'))
            {
                $model->file_title = Yii::$app->request->post('title');
                if(!$model->update()) {
                    $this->flash('danger', Yii::t('admin', 'Update error. {0}', $model->formatErrors()));
                }
            }
            else{
                $this->flash('danger', Yii::t('admin', 'Bad response'));
            }
        }
        else{
            $this->flash('danger', Yii::t('admin', 'Not found'));
        }
        return $this->formatResponse(Yii::t('admin', 'Photo color saved'));
    }

    public function actionImage($id)
    {
        $success = null;
        if(($photo = Medias::findOne($id)))
        {

            $photoUpload = UploadedFile::getInstance($photo, 'image');

            if($photoUpload && $photo->validate(['image'])) {

                $exp = explode(".", $photoUpload->name);
                $ext = end($exp);
                $photoName = uniqid($id.'_').'.'.$ext;

                $upload_path = Yii::getAlias('@webroot').'/uploads/media/'.$photoName;
                $upload_path_thumb = Yii::getAlias('@webroot').'/uploads/media/resized/'.$photoName;
                if($photoUpload->saveAs($upload_path) AND Image::thumbnail($upload_path, 300, 300, ManipulatorInterface::THUMBNAIL_INSET)->save($upload_path_thumb)) {
                    $photoOld = clone $photo;
                    $photo->file_url        = '/uploads/media/'.$photoName;
                    $photo->file_url_thumb  = '/uploads/media/resized/'.$photoName;
                    if($photo->save()){
                        @unlink($photoOld->bigImageUrl);
                        @unlink($photoOld->thumbImageUrl);
                        $success = [
                            'message' => Yii::t('backend', 'Photo uploaded'),
                            'photo' => [
                                'image'         => str_replace('//', '/', $photo->file_url),
                                'thumb'         => str_replace('//', '/', $photo->file_url_thumb),
                            ]
                        ];
                    }
                    else{
                        $this->error = Yii::t('admin', 'Update error. {0}', $photo->formatErrors());
                    }
                }
                else{
                    $this->error = Yii::t('admin', 'File upload error. Check uploads folder for write permissions');
                }
            }
            else{
                $this->error = Yii::t('admin', 'File is incorrect');
            }
        }
        else{
            $this->error =  Yii::t('admin', 'Not found');
        }
        return $this->formatResponse($success);
    }

    public function actionUpload($id, $type)
    {
        if($type == 'catalog' OR $type == 'article')
            $success = null;
        else
            throw new NotFoundHttpException('The requested page does not exist.');

        $photo = new Medias();

        if(Yii::$app->request->isAjax) {

            $photoUpload = UploadedFile::getInstance($photo, 'image');

            if($photoUpload && $photo->validate(['image'])) {

                $exp = explode(".", $photoUpload->name);
                $ext = end($exp);
                $photoName = uniqid($id.'_').'.'.$ext;

                $upload_path = Yii::getAlias('@webroot').'/uploads/media/'.$photoName;
                $upload_path_thumb = Yii::getAlias('@webroot').'/uploads/media/resized/'.$photoName;
                if($photoUpload->saveAs($upload_path) AND Image::thumbnail($upload_path, 460, 300, ManipulatorInterface::THUMBNAIL_INSET)->save($upload_path_thumb, ['quality' => 100])) {

                    $photo->file_title      = $photoName;
                    $photo->file_type       = $type;
                    $photo->file_url        = '/uploads/media/'.$photoName;
                    $photo->file_url_thumb  = '/uploads/media/resized/'.$photoName;

                    if($photo->save()) {


                        if($type == 'article')
                        {
                            $content_medias = new ContentArticlesMedias();
                            $content_medias->content_articles_id    = $id;
                            $content_medias->media_id               = $photo->media_id;
                            $content_medias->save();
                        }

                        $success = [
                            'message' => Yii::t('admin', 'Photo uploaded'),
                            'photo' => [
                                'id'            => $photo->media_id,
                                'image'         => $photo->file_url,
                                'thumb'         => $photo->file_url_thumb,
                                'description'   => ''
                            ]
                        ];
                    } else {
                        $this->flash('error', $photo->getErrors());
                    }

                }

            } else {
                $this->flash('error', Yii::t('admin', 'File is incorrect'));
            }

        } else {
            $this->flash('error', Yii::t('admin', 'Not ajax'));
        }
        return $this->formatResponse($success);
    }
}
