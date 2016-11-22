<?php

namespace YOOtheme\Framework\Joomla;

use YOOtheme\Framework\Database\Database as BaseDatabase;

class Database extends BaseDatabase
{
    protected $db;

    /**
     * Constructor.
     *
     * @param \JDatabaseInterface $db
     */
    public function __construct($db)
    {
        $this->db = $db;
        $this->prefix = $db->getPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAll($statement, array $params = array())
    {
        return $this->db->setQuery($this->prepareQuery($statement, $params))->loadAssocList();
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAssoc($statement, array $params = array())
    {
        return $this->db->setQuery($this->prepareQuery($statement, $params))->loadAssoc();
    }

    /**
     * {@inheritdoc}
     */
    public function fetchArray($statement, array $params = array())
    {
        return $this->db->setQuery($this->prepareQuery($statement, $params))->loadRow();
    }

    /**
     * {@inheritdoc}
     */
    public function executeQuery($query, array $params = array())
    {
        $result = $this->db->setQuery($this->prepareQuery($query, $params))->execute();

        if (is_bool($result)) {
            return $result;
        }

        return $this->db->getNumRows();
    }

    /**
     * {@inheritdoc}
     */
    public function insert($table, array $data)
    {
        $fields = implode(', ', array_keys($data));
        $values = implode(', ', array_map(function($field) { return ":$field"; }, array_keys($data)));

        return $this->executeQuery("INSERT INTO $table ($fields) VALUES ($values)", $data);
    }

    /**
     * {@inheritdoc}
     */
    public function update($table, array $data, array $identifier)
    {
        $fields = implode(', ', array_map(function($field) { return "$field = :$field"; }, array_keys($data)));
        $where  = implode(' AND ', array_map(function($id) { return "$id = :$id"; }, array_keys($identifier)));

        return $this->executeQuery("UPDATE $table SET $fields WHERE $where", array_merge($data, $identifier));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($table, array $identifier)
    {
        $where = implode(' AND ', array_map(function($id) { return "$id = :$id"; }, array_keys($identifier)));

        return $this->executeQuery("DELETE FROM $table WHERE $where", $identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function escape($text)
    {
        return $this->db->escape($text);
    }

    /**
     * {@inheritdoc}
     */
    public function lastInsertId()
    {
        return $this->db->insertid();
    }
}
