<?php
namespace app\modules\admin\widgets;

use Yii;
use yii\helpers\ArrayHelper;
use app\modules\admin\widgets\Html;
use yii\helpers\Url;

/**
 * Class Menu
 * @package backend\components\widget
 */
class Menu extends \yii\widgets\Menu
{
    /**
     * @var string
     */
    public $linkTemplate = "<a href=\"{url}\">\n{icon}\n{label}\n{right-icon}\n{badge}</a>";
    /**
     * @var string
     */
    public $labelTemplate = "{icon}\n{label}\n{badge}";

    /**
     * @var string
     */
    public $badgeTag = 'span';
    /**
     * @var string
     */
    public $badgeClass = 'label pull-right';
    /**
     * @var string
     */
    public $badgeBgClass;

    /**
     * @var string
     */
    public $parentRightIcon = '<i class="fa fa-angle-left pull-right"></i>';

    /**
     * @inheritdoc
     */
    protected function renderItem($item)
    {
        $item['badgeOptions'] = isset($item['badgeOptions']) ? $item['badgeOptions'] : [];

        if (!ArrayHelper::getValue($item, 'badgeOptions.class')) {
            $bg = isset($item['badgeBgClass']) ? $item['badgeBgClass'] : $this->badgeBgClass;
            $item['badgeOptions']['class'] = $this->badgeClass . ' ' . $bg;
        }

        if (isset($item['items']) && !isset($item['right-icon'])) {
            $item['right-icon'] = $this->parentRightIcon;
        }

        if (isset($item['url'])) {
            $template = ArrayHelper::getValue($item, 'template', $this->linkTemplate);

            return strtr($template, [
                '{badge}' => isset($item['badge']) && $item['badge'] > 0
                    ? Html::tag('small', $item['badge'], $item['badgeOptions'])
                    : '',
                '{icon}' => isset($item['icon']) ? $item['icon'] : '',
                '{right-icon}' => isset($item['right-icon']) ? $item['right-icon'] : '',
                '{url}' => Url::to($item['url']),
                '{label}' => $item['label'],
            ]);
        } else {
            $template = ArrayHelper::getValue($item, 'template', $this->labelTemplate);

            return strtr($template, [
                '{badge}' => isset($item['badge'])
                    ? Html::tag('small', $item['badge'], $item['badgeOptions'])
                    : '',
                '{icon}' => isset($item['icon']) ? $item['icon'] : '',
                '{right-icon}' => isset($item['right-icon']) ? $item['right-icon'] : '',
                '{label}' => $item['label'],
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    protected function isItemActive($item)
    {

        if (isset($item['url']) && is_string($item['url']) && $item['url'] === Url::current()) {
            return true;
        } elseif (isset($item['url'])) {
            if(is_array($item['url'])) $url = $item['url'][0];
            else $url = $item['url'];
            $url = str_replace("/index", "", $url);
            if(strpos('/'.Yii::$app->request->pathInfo, $url) !== false) return true;
            else return false;
        } else {
            return parent::isItemActive($item);
        }
    }
}
