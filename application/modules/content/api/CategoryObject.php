<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 26.08.2016
 * Project: falconcity
 * File name: CategoryObject.php
 */


use Yii;
use yii\data\ActiveDataProvider;
use app\components\API;
use app\modules\content\models\ContentArticles;
use yii\helpers\Url;
use yii\widgets\LinkPager;

class CategoryObject extends \app\components\ApiObject
{
    public $slug;
    public $image;
    public $tree;
    public $depth;

    private $_adp;
    private $_items;

    public function getTitle(){
        return LIVE_EDIT ? API::liveEdit($this->model->title, $this->editLink) : $this->model->title;
    }

    public function pages($options = []){
        return $this->_adp ? LinkPager::widget(array_merge($options, ['pagination' => $this->_adp->pagination])) : '';
    }

    public function pagination(){
        return $this->_adp ? $this->_adp->pagination : null;
    }

    public function items($options = [])
    {
        if(!$this->_items){
            $this->_items = [];

            $query = ContentArticles::find()
                ->with('translations')
                ->where(['category_id' => $this->id])
                ->status(ContentArticles::STATUS_PUBLISHED)
                ->sortDate();

            $this->_adp = new ActiveDataProvider([
                'query' => $query,
                'pagination' => !empty($options['pagination']) ? $options['pagination'] : []
            ]);

            foreach($this->_adp->models as $model){
                $this->_items[] = new ArticleObject($model);
            }
        }
        return $this->_items;
    }

    public function getEditLink(){
        return Url::to(['/admin/article/a/edit/', 'id' => $this->id]);
    }
}
