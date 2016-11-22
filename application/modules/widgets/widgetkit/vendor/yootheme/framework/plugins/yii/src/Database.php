<?php

namespace YOOtheme\Framework\Yii;

use app\modules\admin\widgets\Html;
use YOOtheme\Framework\Database\Database as BaseDatabase;

class Database extends BaseDatabase
{
    protected $db;

    /**
     * Constructor.
     *
     * @param \Yii\db\Connection $db
     */
    public function __construct($db)
    {
        $this->db = $db;
        $this->prefix = $db->tablePrefix;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAll($statement, array $params = array())
    {
        return $this->db->createCommand($this->prepareQuery($statement, $params))->queryAll();
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAssoc($statement, array $params = array())
    {
        return $this->db->createCommand($this->prepareQuery($statement, $params))->queryOne();
    }

    /**
     * {@inheritdoc}
     */
    public function fetchArray($statement, array $params = array())
    {
        return $this->db->createCommand($this->prepareQuery($statement, $params))->queryOne();
    }

    /**
     * {@inheritdoc}
     */
    public function executeQuery($query, array $params = array())
    {
        return $this->db->createCommand($this->prepareQuery($query, $params))->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function insert($table, array $data)
    {
        return $this->db->createCommand()->insert($this->replacePrefix($table), $data)->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function update($table, array $data, array $identifier)
    {
        $fields = implode(', ', array_map(function($field) { return "$field = :$field"; }, array_keys($data)));
        $where  = implode(' AND ', array_map(function($id) { return "$id = :$id"; }, array_keys($identifier)));

        return $this->db->createCommand("UPDATE ".$this->replacePrefix($table)." SET $fields WHERE $where", array_merge($data, $identifier))->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function delete($table, array $identifier)
    {
        $where = implode(' AND ', array_map(function($id) { return "$id = :$id"; }, array_keys($identifier)));
        return $this->db->createCommand()->delete($this->replacePrefix($table), $where, $identifier)->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function escape($text)
    {
        return Html::encode($text);
    }

    /**
     * {@inheritdoc}
     */
    public function lastInsertId()
    {
        return $this->db->getLastInsertID();
    }
}
