<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 15.05.2016
 * Project: oakcms
 * File name: SeoViewBehavior.php
 *
 * Поведение для работы с SEO параметрами во view
 */


namespace app\modules\system\components;


use Yii;
use yii\web\View;
use yii\helpers\Html;
use yii\base\Behavior;
use app\modules\seo\models\SeoItems;

/**
 * Управление установкой SEO-параметров для страницы
 *
 * @package app\modules\system\components
 */
class SeoViewBehavior extends Behavior
{
    private $_seo_data = '';
    private $_page_title = '';
    private $_meta_description = '';
    private $_meta_keywords = '';
    private $_meta_canonical = '';
    private $_noIndex = false;
    public function events()
    {
        return [
//            ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
//            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
//            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
//            ActiveRecord::EVENT_BEFORE_DELETE => 'beforeDelete',
//            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
        ];
    }

    public function init()
    {
        $this->_seo_data = SeoItems::find()->where(['link' => $_SERVER['REQUEST_URI'], 'status' => SeoItems::STATUS_PUBLISHED])->one();
        $this->setSeoData($this->_seo_data);
    }

    /**
     * Установка meta параметров страницы
     *
     * @param mixed $title 1) массив:
     * array("title"=>"Page Title", "desc"=>"Page Descriptions", "keys"=>"Page, Keywords")
     * 2) SeoModelBehavior
     * 3) Строка для title страницы
     * @param string $desc Meta description
     * @param mixed $keys  Meta keywords, строка либо массив ключевиков
     *
     * @return static
     */
    public function setSeoData($title, $desc = '', $keys = '', $canonical = '')
    {
        if (isset($this->_seo_data)) {
            // Вытаскиваем данные из модельки, в которой есть SeoModelBehavior
            $meta = $this->_seo_data;
            $data = [
                'title' => $meta->title,
                'desc' => $meta->description,
                'keys' => $meta->keywords,
                'canonical' => $meta->canonical,
            ];
        } elseif (is_string($title)) {
            $data = array(
                'title' => $title,
                'desc' => $desc,
                'keys' => !is_array($keys) ? $keys : implode(', ', $keys),
                'canonical' => $canonical
            );
        }
        if (isset($data['title'])) {
            $this->_page_title = $this->normalizeStr($data['title']);
        }
        if (isset($data['desc'])) {
            $this->_meta_description = $this->normalizeStr($data['desc']);
        }
        if (isset($data['keys'])) {
            $this->_meta_keywords = $this->normalizeStr($data['keys']);
        }
        if (isset($data['canonical']) and $data['canonical'] != '') {
            $this->_meta_canonical = $this->normalizeStr($data['canonical']);
        }
        return $this;
    }
    public function renderMetaTags()
    {
        /* @var $view View */
        $view = $this->owner;
        $title = !empty($this->_page_title) ? $this->_page_title . ' - ' . Yii::$app->name : Yii::$app->name;
        echo '<title>' . Html::encode($this->normalizeStr($title)) . '</title>' . PHP_EOL;
        if (!empty($this->_meta_description)) {
            $view->registerMetaTag(['name' => 'description', 'content' => Html::encode($this->normalizeStr($this->_meta_description))]);
        }
        if (!empty($this->_meta_keywords)) {
            $view->registerMetaTag(['name' => 'keywords', 'content' => Html::encode($this->normalizeStr($this->_meta_keywords))]);
        }
        if (!empty($this->_meta_canonical)) {
            $view->registerLinkTag(['rel' => 'canonical', 'href' => $this->_meta_canonical], 'canonical');
        }
        if (!empty($this->_noIndex)) {
            $view->registerMetaTag(['name' => 'robots', 'content' => $this->_noIndex]);
        }
    }
    /**
     * Нормализует строку, подготоваливает её для отображения
     *
     * @param string $str
     *
     * @return string
     */
    private function normalizeStr($str)
    {
        // Удаляем теги из текста
        $str = strip_tags($str);
        // Заменяем все пробелы, переносы строк и табы на один пробел
        $str = trim(preg_replace('/[\s]+/is', ' ', $str));
        return $str;
    }
    /**
     * Установить meta-тег noindex для текущей страницы
     *
     * @param boolean $follow Разрешить поисковикам следовать по ссылкам? Если FALSE,
     * то в мета-тег будет добавлено nofollow
     */
    public function noIndex($follow = true)
    {
        $content = 'noindex, ' . ($follow ? 'follow' : 'nofollow');
        $this->_noIndex = $content;
    }
}
