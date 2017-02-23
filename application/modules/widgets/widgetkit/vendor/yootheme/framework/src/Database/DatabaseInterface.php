<?php

namespace YOOtheme\Framework\Database;

interface DatabaseInterface
{
    /**
     * Fetches all rows of the result as an associative array.
     *
     * @param  string $statement
     * @param  array  $params
     * @return array
     */
    public function fetchAll($statement, array $params = array());

    /**
     * Fetches the first row of the result as an associative array.
     *
     * @param  string $statement
     * @param  array  $params
     * @return array
     */
    public function fetchAssoc($statement, array $params = array());

    /**
     * Fetches the first row of the result as a numerically indexed array.
     *
     * @param  string $statement
     * @param  array  $params
     * @return array
     */
    public function fetchArray($statement, array $params = array());

    /**
     * Prepares and executes an SQL query and returns the first row of the result as an object.
     *
     * @param  string $statement
     * @param  array  $params
     * @param  string $class
     * @param  array  $args
     * @return mixed
     */
    public function fetchObject($statement, array $params = array(), $class = 'stdClass', $args = array());

    /**
     * Prepares and executes an SQL query and returns the result as an array of objects.
     *
     * @param  string $statement
     * @param  array  $params
     * @param  string $class
     * @param  array  $args
     * @return array
     */
    public function fetchAllObjects($statement, array $params = array(), $class = 'stdClass', $args = array());

    /**
     * Executes an, optionally parametrized, SQL query.
     *
     * @param string $query
     * @param array  $params
     *
     * @return int|false
     */
    public function executeQuery($query, array $params = array());

    /**
     * Inserts a table row with specified data.
     *
     * @param  string $table
     * @param  array  $data
     * @return int
     */
    public function insert($table, array $data);

    /**
     * Updates a table row with specified data.
     *
     * @param  string $table
     * @param  array  $data
     * @param  array  $identifier
     * @return int
     */
    public function update($table, array $data, array $identifier);

    /**
     * Deletes a table row.
     *
     * @param  string $table
     * @param  array  $identifier
     * @return int
     */
    public function delete($table, array $identifier);

    /**
     * Escapes a string for usage in an SQL statement.
     *
     * @param  string $text
     * @return string
     */
    public function escape($text);

    /**
     * Retrieves the last inserted id.
     *
     * @return int
     */
    public function lastInsertId();
}
