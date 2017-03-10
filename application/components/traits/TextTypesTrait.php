<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2017. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

namespace app\components\traits;

trait TextTypesTrait
{
    /**
     * @return \yii\db\Connection the database connection to be used for schema building.
     */
    protected abstract function getDb();

    /**
     * Creates a medium text column.
     * @return \yii\db\ColumnSchemaBuilder the column instance which can be further customized.
     */
    public function mediumText()
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder('mediumtext');
    }

    /**
     * Creates a long text column.
     * @return \yii\db\ColumnSchemaBuilder the column instance which can be further customized.
     */
    public function longText()
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext');
    }

    /**
     * Creates a tiny text column.
     * @return \yii\db\ColumnSchemaBuilder the column instance which can be further customized.
     */
    public function tinyText()
    {
        return $this->getDb()->getSchema()->createColumnSchemaBuilder('tinytext');
    }
}
