<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\field;

use yii;

class Module extends \yii\base\Module
{
    public $types = ['select' => 'Селект', 'radio' => 'Радиобатон', 'checkbox' => 'Чекбокс', 'date' => 'Дата', 'numeric' => 'Число', 'text' => 'Текст', 'textarea' => 'Текстарея', 'image' => 'Картинка'];
    public $relationModels = [];
    public $adminRoles = ['superadmin', 'admin'];

    public function init()
    {
        parent::init();
    }
}
