<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\system\components;

use yii\helpers\VarDumper;
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
              View::EVENT_BEGIN_PAGE => 'beginPage'
        ];
    }

    public function init()
    {
        $model = SeoItems::find()
            ->where(['link' => $_SERVER['REQUEST_URI'], 'status' => SeoItems::STATUS_PUBLISHED])
            ->one();

        $this->_seo_data = [];

        if($model !== null) {
            $this->_seo_data = [
                'title' => $model->title,
                'desc' => $model->description,
                'keys' => $model->keywords,
                'canonical' => $model->canonical,
            ];
        }
    }

    /**
     * Установка meta параметров страницы
     *
     * @param mixed $title 1) массив:
     * array("title"=>"Page Title", "desc"=>"Page Descriptions", "keys"=>"Page, Keywords", "canonical" => "Canonical Link")
     * 2) SeoModelBehavior
     * 3) Строка для title страницы
     * @param string $desc Meta description
     * @param mixed $keys  Meta keywords, строка либо массив ключевиков
     *
     * @return static
     */
    public function setSeoData($title, $desc = '', $keys = '', $canonical = '')
    {
        if(is_array($title)) {
            $data = [
                'title' => isset($title['title']) ? $title['title'] : '',
                'desc' => isset($title['desc']) ? $title['desc'] : '',
                'keys' => isset($title['keys']) ? $title['keys'] : '',
                'canonical' => isset($title['canonical']) ? $title['canonical'] : '',
            ];
        } elseif (is_string($title)) {
            $data = array(
                'title' => $title,
                'desc' => $desc,
                'keys' => !is_array($keys) ? $keys : implode(', ', $keys),
                'canonical' => $canonical
            );
        } else {
            $data = [];
        }

        if (isset($data['title']) && $data['title'] != '') {
            $this->_page_title = $this->normalizeStr($data['title']);
        }
        if (isset($data['desc']) && $data['desc'] != '') {
            $this->_meta_description = $this->normalizeStr($data['desc']);
        }
        if (isset($data['keys']) && $data['keys'] != '') {
            $this->_meta_keywords = $this->normalizeStr($data['keys']);
        }
        if (isset($data['canonical']) && $data['canonical'] != '') {
            $this->_meta_canonical = $this->normalizeStr($data['canonical']);
        }
        return $this;
    }

    public function renderMetaTags()
    {
        /* @var $view View */
        $view = $this->owner;
        $title = $this->_page_title;
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

    /**
     *
     */
    public function beginPage() {
        if(isset($this->_seo_data) && count($this->_seo_data)) {
            $this->setSeoData($this->_seo_data);
        }
    }
}
