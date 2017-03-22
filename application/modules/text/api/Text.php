<?php
namespace app\modules\text\api;

use Yii;
use app\components\API;
use app\helpers\Data;
use yii\caching\Cache;
use yii\caching\DbDependency;
use yii\di\Instance;
use yii\helpers\Url;
use app\modules\text\models\Text as TextModel;
use yii\helpers\Html;
use yii\helpers\VarDumper;

/**
 * Text module API
 * @package yii\easyii\modules\text\api
 * @property $_text array
 * @property $cache array
 * @property $cacheDependency
 * @property $cacheDuration
 *
 * @method static get(mixed $id_slug, int $id = null) Get text block by id or slug
 */
class Text extends API
{
    private $_texts = [];

    public $cache = 'cache';

    public $cacheDependency;

    public $cacheDuration;

    public function init()
    {
        parent::init();

        if (count($this->_texts) == 0) {
            $this->cache = Instance::ensure($this->cache, Cache::className());

            if ($this->cache) {
                if (($this->_texts = $this->cache->get(TextModel::CACHE_KEY)) === false) {
                    $models = TextModel::find()->where([ 'status' => TextModel::STATUS_PUBLISHED])->orderBy(['order' => SORT_ASC])->all();
                    $return = [];
                    foreach ($models as $k=>$model) {
                        $return[$model->slug.'_'.\Yii::$app->language][$model->id] = $model;
                    }
                    $this->_texts = $return;
                    $this->cacheDependency = \Yii::createObject([
                        'class' => 'yii\caching\DbDependency',
                        'sql' => 'SELECT MAX(updated_at) FROM '.TextModel::tableName(),
                    ]);

                    $this->cache->set(TextModel::CACHE_KEY, $this->_texts, $this->cacheDuration, $this->cacheDependency);
                }
            } else {
                $this->_texts = TextModel::find()->where([ 'status' => TextModel::STATUS_PUBLISHED])->all();
            }
        }
    }
    public function api_get($id_slug, $id = null)
    {
        if(($texts = $this->findText($id_slug, $id)) === null) {
            return $this->notFound($id_slug);
        }

        $return = '';
        $blocks = '';
        foreach ($texts as $text) {
            if(isset($text->where_to_place)) {
                switch ($text->where_to_place) {
                    case '0':
                        $return = true;
                        break;
                    case '-':
                        $return = false;
                        break;
                    case '1':
                        if(
                            isset(Yii::$app->menuManager->activeMenu) &&
                            ($activeMenu = Yii::$app->menuManager->activeMenu->id) &&
                            in_array(Yii::$app->menuManager->activeMenu->id, $text->links)
                        ) {
                            $return = true;
                        } else {
                            $return = false;
                        }
                        break;
                    case '-1':
                        if(
                            isset(Yii::$app->menuManager->activeMenu) &&
                            ($activeMenu = Yii::$app->menuManager->activeMenu->id) &&
                            !in_array(Yii::$app->menuManager->activeMenu->id, $text->links)
                        ) {
                            $return = true;
                        } else {
                            $return = false;
                        }
                        break;
                    default:
                        $return = false;
                        break;
                }

                if($return) {
                    if(is_file($file = Yii::getAlias('@frontendTemplate/modules/text/layouts/'.$text->layout.'/plugin.php')))
                        $params = require $file;
                    else
                        $params = require Yii::getAlias('@app/modules/text/views/frontend/layouts/'.$text->layout.'/plugin.php');

                    $text['output'] = Yii::$app->view->renderFile($params['viewFile'], ['model' => $text], true);
                    $blocks .= LIVE_EDIT ? API::liveEdit($text['output'], Url::to(['/admin/text/default/update/', 'id' => $text['id']]), 'div') : $text['output'];
                } else {
                    $blocks = '';
                }
            }
        }
        return $blocks;
    }

    private function findText($id_slug, $id)
    {
        if($id) {
            $return = \app\modules\text\models\Text::find()->where(['status'=>1, 'id'=>$id_slug])->all();
        } else {
            $return = (isset($this->_texts[$id_slug.'_'.\Yii::$app->language])) ? $this->_texts[$id_slug.'_'.\Yii::$app->language] : null;
        }
        return $return;
    }

    private function notFound($id_slug)
    {
        $text = '';
        if(!Yii::$app->user->isGuest && LIVE_EDIT) {
            $text = Html::tag('div', Html::a(Yii::t('text', 'Create text'), ['/admin/text/default/create', 'slug' => $id_slug], ['target' => '_blank']));
        }
        return $text;
    }
}
