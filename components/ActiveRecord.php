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
    
    public function formatErrors()
    {
        $result = '';
        foreach($this->getErrors() as $attribute => $errors) {
            $result .= implode(" ", $errors)." ";
        }
        return $result;
    }
}
