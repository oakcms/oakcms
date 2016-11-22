<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 30.08.2016
 * Project: falconcity
 * File name: ActiveQuery.php
 */

namespace app\components;


class ActiveQuery extends \yii\db\ActiveQuery
{
    public function published()
    {
        return $this->andWhere(['status' => ActiveRecord::STATUS_PUBLISHED]);
    }

    public function status($status) {
        return $this->andWhere(['status' => $status]);
    }
}
