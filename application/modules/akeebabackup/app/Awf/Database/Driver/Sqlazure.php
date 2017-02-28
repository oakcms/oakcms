<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 *
 * This class is adapted from the Joomla! Framework
 */

namespace Awf\Database\Driver;

/**
 * SQL Server database driver
 */
class Sqlazure extends Sqlsrv
{
	/**
	 * The name of the database driver.
	 *
	 * @var    string
	 */
	public $name = 'sqlzure';

	/**
	 * @var    string  The database technology family supported, e.g. mysql, mssql
	 */
	public static $dbtech = 'mssql';

}
