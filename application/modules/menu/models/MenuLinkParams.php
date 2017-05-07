<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-beta.0.1
 */

namespace app\modules\menu\models;

/**
 * Class MenuLinkParams
 */
class MenuLinkParams extends \yii\base\Model
{
    public $title;
    public $class;
    public $style;
    public $target;
    public $onclick;
    public $rel;

    public function rules()
    {
        return [
            [['title', 'class', 'style', 'target', 'onclick', 'rel'], 'string']
        ];
    }
}
