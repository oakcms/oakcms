<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 *
 * This class is adapted from the Joomla! Framework
 */

namespace Awf\Database\Driver;

use Awf\Database\Driver;
use Awf\Database\Query;

/**
 * Dummy driver class for flat-file CMS
 */
class None extends Driver
{
	/**
	 * The name of the database driver.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $name = 'none';

	public static $dbtech = 'none';

	/**
	 * Test to see if the PDO ODBC connector is available.
	 *
	 * @return  boolean  True on success, false otherwise.
	 *
	 * @since   1.0
	 */
	public static function isSupported()
	{
		return true;
	}

	/**
     * Get the current query object or a new Query object.
     * We have to override the parent method since it will always return a PDO query, while we have a
     * specialized class for SQLite
     *
     * @param   boolean  $new  False to return the current query object, True to return a new Query object.
     *
     * @return  Query  The current query object or a new object extending the Query class.
     *
     * @throws  \RuntimeException
     */
    public function getQuery($new = false)
    {
	    return $this->sql;
    }

	/**
	 * Connects to the database if needed.
	 *
	 * @return  void  Returns void if the database connected successfully.
	 *
	 * @throws  \RuntimeException
	 */
	public function connect()
	{
		return;
	}

	/**
	 * Determines if the connection to the server is active.
	 *
	 * @return  boolean  True if connected to the database engine.
	 */
	public function connected()
	{
		return true;
	}

	/**
	 * Disconnects the database.
	 *
	 * @return  void
	 */
	public function disconnect()
	{
		return;
	}

	/**
	 * Drops a table from the database.
	 *
	 * @param   string  $table    The name of the database table to drop.
	 * @param   boolean $ifExists Optionally specify that the table must exist before it is dropped.
	 *
	 * @return  Driver     Returns this object to support chaining.
	 *
	 * @throws  \RuntimeException
	 */
	public function dropTable($table, $ifExists = true)
	{
		return $this;
	}

	/**
	 * Method to escape a string for usage in an SQL statement.
	 *
	 * @param   string  $text  The string to be escaped.
	 * @param   boolean $extra Optional parameter to provide extra escaping.
	 *
	 * @return  string   The escaped string.
	 */
	public function escape($text, $extra = false)
	{
		return '';
	}

	/**
	 * Method to fetch a row from the result set cursor as an array.
	 *
	 * @param   mixed $cursor The optional result set cursor from which to fetch the row.
	 *
	 * @return  mixed  Either the next row from the result set or false if there are no more rows.
	 *
	 */
	protected function fetchArray($cursor = null)
	{
		return false;
	}

	/**
	 * Method to fetch a row from the result set cursor as an associative array.
	 *
	 * @param   mixed $cursor The optional result set cursor from which to fetch the row.
	 *
	 * @return  mixed  Either the next row from the result set or false if there are no more rows.
	 *
	 */
	protected function fetchAssoc($cursor = null)
	{
		return false;
	}

	/**
	 * Method to fetch a row from the result set cursor as an object.
	 *
	 * @param   mixed  $cursor The optional result set cursor from which to fetch the row.
	 * @param   string $class  The class name to use for the returned row object.
	 *
	 * @return  mixed   Either the next row from the result set or false if there are no more rows.
	 *
	 */
	protected function fetchObject($cursor = null, $class = 'stdClass')
	{
		return false;
	}

	/**
	 * Method to free up the memory used for the result set.
	 *
	 * @param   mixed $cursor The optional result set cursor from which to fetch the row.
	 *
	 * @return  void
	 *
	 */
	protected function freeResult($cursor = null)
	{
		return;
	}

	/**
	 * Get the number of affected rows for the previous executed SQL statement.
	 *
	 * @return  integer  The number of affected rows.
	 */
	public function getAffectedRows()
	{
		return 0;
	}

	/**
	 * Method to get the database collation in use by sampling a text field of a table in the database.
	 *
	 * @return  mixed  The collation in use by the database or boolean false if not supported.
	 *
	 */
	public function getCollation()
	{
		return false;
	}

	/**
	 * Get the number of returned rows for the previous executed SQL statement.
	 *
	 * @param   resource $cursor An optional database cursor resource to extract the row count from.
	 *
	 * @return  integer   The number of returned rows.
	 *
	 */
	public function getNumRows($cursor = null)
	{
		return 0;
	}

	/**
	 * Retrieves field information about the given tables.
	 *
	 * @param   string  $table    The name of the database table.
	 * @param   boolean $typeOnly True (default) to only return field types.
	 *
	 * @return  array  An array of fields by table.
	 *
	 * @throws  \RuntimeException
	 */
	public function getTableColumns($table, $typeOnly = true)
	{
		return array();
	}

	/**
	 * Shows the table CREATE statement that creates the given tables.
	 *
	 * @param   mixed $tables A table name or a list of table names.
	 *
	 * @return  array  A list of the create SQL for the tables.
	 *
	 * @throws  \RuntimeException
	 */
	public function getTableCreate($tables)
	{
		return array();
	}

	/**
	 * Retrieves field information about the given tables.
	 *
	 * @param   mixed $tables A table name or a list of table names.
	 *
	 * @return  array  An array of keys for the table(s).
	 *
	 * @throws  \RuntimeException
	 */
	public function getTableKeys($tables)
	{
		return array();
	}

	/**
	 * Method to get an array of all tables in the database.
	 *
	 * @return  array  An array of all the tables in the database.
	 *
	 * @throws  \RuntimeException
	 */
	public function getTableList()
	{
		return array();
	}

	/**
	 * Get the version of the database connector
	 *
	 * @return  string  The database connector version.
	 */
	public function getVersion()
	{
		return '0.0.0';
	}

	/**
	 * Method to get the auto-incremented value from the last INSERT statement.
	 *
	 * @return  integer  The value of the auto-increment field from the last inserted row.
	 */
	public function insertid()
	{
		return 0;
	}

	/**
	 * Locks a table in the database.
	 *
	 * @param   string $tableName The name of the table to unlock.
	 *
	 * @return  Driver     Returns this object to support chaining.
	 *
	 * @throws  \RuntimeException
	 */
	public function lockTable($tableName)
	{
		return $this;
	}

	/**
	 * Renames a table in the database.
	 *
	 * @param   string $oldTable The name of the table to be renamed
	 * @param   string $newTable The new name for the table.
	 * @param   string $backup   Table prefix
	 * @param   string $prefix   For the table - used to rename constraints in non-mysql databases
	 *
	 * @return  Driver    Returns this object to support chaining.
	 *
	 * @throws  \RuntimeException
	 */
	public function renameTable($oldTable, $newTable, $backup = null, $prefix = null)
	{
		return $this;
	}

	/**
	 * Select a database for use.
	 *
	 * @param   string $database The name of the database to select for use.
	 *
	 * @return  boolean  True if the database was successfully selected.
	 *
	 * @throws  \RuntimeException
	 */
	public function select($database)
	{
		return true;
	}

	/**
	 * Set the connection to use UTF-8 character encoding.
	 *
	 * @return  boolean  True on success.
	 *
	 */
	public function setUTF()
	{
		return true;
	}

	/**
	 * Method to commit a transaction.
	 *
	 * @return  void
	 *
	 * @throws  \RuntimeException
	 */
	public function transactionCommit()
	{
		return;
	}

	/**
	 * Method to roll back a transaction.
	 *
	 * @return  void
	 *
	 * @throws  \RuntimeException
	 */
	public function transactionRollback()
	{
		return;
	}

	/**
	 * Method to initialize a transaction.
	 *
	 * @return  void
	 *
	 * @throws  \RuntimeException
	 */
	public function transactionStart()
	{
		return;
	}

	/**
	 * Execute the SQL statement.
	 *
	 * @return  mixed  A database cursor resource on success, boolean false on failure.
	 *
	 * @throws  \RuntimeException
	 */
	public function execute()
	{
		return false;
	}

	/**
	 * Unlocks tables in the database.
	 *
	 * @return  Driver  Returns this object to support chaining.
	 *
	 * @throws  \RuntimeException
	 */
	public function unlockTables()
	{
		return $this;
	}
}
