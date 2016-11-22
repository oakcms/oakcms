<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 30.05.2016
 * Project: oakcms
 * File name: ActiveRecord.php
 */

namespace app\components;

class ActiveRecord extends \yii\db\ActiveRecord
{
    const STATUS_PUBLISHED = 1;
    const STATUS_DRAFT = 0;

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
