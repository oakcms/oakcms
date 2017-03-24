<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.5
 */

/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 30.05.2016
 * Project: oakcms
 * File name: ActiveRecord.php
 */

namespace app\components;

use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use app\helpers\StringHelper;

class ActiveRecord extends \yii\db\ActiveRecord
{
    const STATUS_PUBLISHED = 1;
    const STATUS_DRAFT = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        $className = static::class;
        $replace = [
            '_search',
        ];

        return '{{%' . static::getTablePrefix($className) . str_replace($replace, '', Inflector::camel2id(StringHelper::basename($className), '_')) . '}}';
    }

    /**
     * Формує префікс для імені таблиці
     *
     * @param string $className
     *
     * @return string
     */
    public static function getTablePrefix($className)
    {
        if (!preg_match('#modules\\\\(.*)\\\\models#', $className, $idModule)) {
            $return = '';
        } else {
            $return = ArrayHelper::getValue($idModule, 1);
        }

        return $return . '_';
    }

    public static function find()
    {
        return new ActiveQuery(get_called_class());
    }

    public function formatErrors()
    {
        $result = '';
        foreach($this->getErrors() as $attribute => $errors) {
            $result .= implode(" ", $errors)." ";
        }

        return $result;
    }
}
