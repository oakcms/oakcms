<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\components;

use Yii;
use app\modules\admin\components\behaviors\SortableModel;
use yii\widgets\ActiveForm;
use yii\web\UploadedFile;

/**
 * Category controller component
 * @package yii\content\components
 */
class CategoryController extends BackendController
{
    /** @var string */
    public $categoryClass;

    /** @var  string */
    public $moduleName;

    /** @var string  */
    public $viewRoute = '/items';

    /** @var string  */
    public $returnUrl = '/article';

    /**
     * Categories list
     *
     * @return string
     */
    public function actionIndex()
    {
        $class = $this->categoryClass;
        return $this->render('@app/views/category/index', [
            'cats' => $class::cats()
        ]);
    }

    /**
     * Create form
     *
     * @param null $parent
     * @return array|string|\yii\web\Response
     * @throws \yii\web\HttpException
     */
    public function actionCreate($parent = null)
    {
        $lang = $this->getDefaultLanguage();
        $class = $this->categoryClass;
        $model = new $class;
        $model->language = $lang->language_id;

        if ($model->load(Yii::$app->request->post())) {
            if(Yii::$app->request->isAjax)
            {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            else
            {
                $model->status = $class::STATUS_ON;

                $parent = (int)Yii::$app->request->post('parent', null);
                if($parent > 0 && ($parentCategory = $class::findOne($parent))){
                    $model->order = $parentCategory->order;
                    $model->appendTo($parentCategory);
                } else {
                    $model->attachBehavior('sortable', SortableModel::className());
                    $model->makeRoot();
                }

                if(!$model->hasErrors()){
                    $this->flash('success', Yii::t('admin', 'Category created'));
                    return $this->redirect(['/admin/'.$this->moduleName.$this->returnUrl, 'id' => $model->primaryKey]);
                }
                else {
                    $this->flash('error', Yii::t('admin', 'Create error. {0}', $model->formatErrors()));
                    return $this->refresh();
                }
            }
        }
        else {
            return $this->render('@app/views/category/create', [
                'model' => $model,
                'parent' => $parent,
                'lang'  => $lang
            ]);
        }
    }

    /**
     * Edit form
     *
     * @param $id
     * @return array|string|\yii\web\Response
     * @throws \yii\web\HttpException
     */
    public function actionUpdate($id, $language)
    {
        $lang = $this->getDefaultLanguage($language);
        $class = $this->categoryClass;

        if(!($model = $class::findOne($id))) {
            return $this->redirect(['/admin/' . $this->moduleName]);
        }
        $model->language = $lang->language_id;

        if ($model->load(Yii::$app->request->post())) {

            if(Yii::$app->request->isAjax){
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            } else {
                if($model->save()) {
                    $this->flash('success', Yii::t('admin', 'Category updated'));
                }
                else{
                    $this->flash('error', Yii::t('admin', 'Update error. {0}', $model->formatErrors()));
                }
                return $this->refresh();
            }
        }
        else {
            return $this->render('@app/views/category/edit', [
                'model' => $model,
                'lang' => $lang,
            ]);
        }
    }

    /**
     * Remove category image
     *
     * @param $id
     * @return \yii\web\Response
     */
    public function actionClearImage($id)
    {
        $class = $this->categoryClass;
        $model = $class::findOne($id);

        if($model === null){
            $this->flash('error', Yii::t('content', 'Not found'));
        }
        elseif($model->image){
            $model->image = '';
            if($model->update()){
                $this->flash('success', Yii::t('content', 'Image cleared'));
            } else {
                $this->flash('error', Yii::t('content', 'Update error. {0}', $model->formatErrors()));
            }
        }
        return $this->back();
    }

    /**
     * Delete the category by ID
     *
     * @param $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $class = $this->categoryClass;
        if(($model = $class::findOne($id))){
            $children = $model->children()->all();
            $model->deleteWithChildren();
            foreach($children as $child) {
                $child->afterDelete();
            }
        } else {
            $this->error = Yii::t('admin', 'Not found');
        }
        return $this->formatResponse(Yii::t('admin', 'Category deleted'));
    }

    /**
     * Move category one level up up
     *
     * @param $id
     * @return \yii\web\Response
     */
    public function actionUp($id)
    {
        return $this->move($id, 'up');
    }

    /**
     * Move category one level down
     *
     * @param $id
     * @return \yii\web\Response
     */
    public function actionDown($id)
    {
        return $this->move($id, 'down');
    }

    /**
     * Activate category action
     *
     * @param $id
     * @return mixed
     */
    public function actionOn($id)
    {
        $class = $this->categoryClass;
        return $this->changeStatus($id, $class::STATUS_ON);
    }

    /**
     * Activate category action
     *
     * @param $id
     * @return mixed
     */
    public function actionOff($id)
    {
        $class = $this->categoryClass;
        return $this->changeStatus($id, $class::STATUS_OFF);
    }

    /**
     * Move category up/down
     *
     * @param $id
     * @param $direction
     * @return \yii\web\Response
     * @throws \Exception
     */
    private function move($id, $direction)
    {
        $modelClass = $this->categoryClass;

        if(($model = $modelClass::findOne($id)))
        {
            $up = $direction == 'up';
            $orderDir = $up ? SORT_ASC : SORT_DESC;

            if($model->depth == 0){

                $swapCat = $modelClass::find()->where([$up ? '>' : '<', 'order', $model->order])->orderBy(['order' => $orderDir])->one();
                if($swapCat)
                {
                    $modelClass::updateAll(['order' => '-1'], ['order' => $swapCat->order]);
                    $modelClass::updateAll(['order' => $swapCat->order], ['order' => $model->order]);
                    $modelClass::updateAll(['order' => $model->order], ['order' => '-1']);
                    Yii::$app->cache->flush();
                }
            } else {
                $where = [
                    'and',
                    ['tree' => $model->tree],
                    ['depth' => $model->depth],
                    [($up ? '<' : '>'), 'lft', $model->lft]
                ];

                $swapCat = $modelClass::find()->where($where)->orderBy(['lft' => ($up ? SORT_DESC : SORT_ASC)])->one();
                if($swapCat)
                {
                    if($up) {
                        $model->insertBefore($swapCat);
                    } else {
                        $model->insertAfter($swapCat);
                    }

                    $swapCat->update();
                    $model->update();
                }
            }
        }
        else {
            $this->flash('error', Yii::t('content', 'Not found'));
        }
        return $this->back();
    }

    /**
     * Change category status
     *
     * @param $id
     * @param $status
     * @return mixed
     */
    public function changeStatus($id, $status)
    {
        $modelClass = $this->categoryClass;
        $ids = [];

        if(($model = $modelClass::findOne($id))){
            $ids[] = $model->primaryKey;
            foreach($model->children()->all() as $child){
                $ids[] = $child->primaryKey;
            }
            $modelClass::updateAll(['status' => $status], ['in', 'id', $ids]);
            Yii::$app->cache->flush();
        }
        else{
            $this->error = Yii::t('admin', 'Not found');
        }

        return $this->formatResponse(Yii::t('admin', 'Status successfully changed'));
    }
}
