<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 *
 * @copyright Copyright (c)2006-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 *
 */

namespace Akeeba\Engine\Dump\Reverse;

// Protection against direct access
defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Dump\Base;
use Akeeba\Engine\Factory;
use Psr\Log\LogLevel;

/**
 * Dump class for the "None" database driver (ie no database used by the application)
 *
 */
class None extends Base
{
	/**
	 * Return the current database name by querying the database connection object (e.g. SELECT DATABASE() in MySQL)
	 *
	 * @return  string
	 */
	protected function getDatabaseNameFromConnection()
	{
		return '';
	}

	/**
	 * Implements the constructor of the class
	 *
	 * @return  None
	 */
	public function __construct()
	{
		Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . " :: New instance");
	}

	/**
	 * Performs one more step of dumping database data
	 *
	 * @return  void
	 */
	protected function stepDatabaseDump()
	{
		// We do not create any dump file
		Factory::getLog()->log(LogLevel::INFO, "No database is used, no SQL dump files will be created.");
		$this->setState('postrun');
		$this->setStep('');
		$this->setSubstep('');
	}

	/**
	 * Scans the database for tables to be backed up and sorts them according to
	 * their dependencies on one another. Updates $this->dependencies.
	 *
	 * @return  void
	 */
	protected function getTablesToBackup()
	{
		// No tables will be included in the backup
		return;
	}
}