<?php

namespace app\modules\admin\models\query;

/**
 * This is the ActiveQuery class for [[\app\modules\admin\models\ModulesModules]].
 *
 * @see \app\modules\admin\models\ModulesModules
 */
class ModulesModulesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \app\modules\admin\models\ModulesModules[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\modules\admin\models\ModulesModules|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
