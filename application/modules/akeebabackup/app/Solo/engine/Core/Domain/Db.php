<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2006-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 *
 */

namespace Akeeba\Engine\Core\Domain;

// Protection against direct access
defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Base\Part;
use Akeeba\Engine\Dump\Base as DumpBase;
use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;
use Psr\Log\LogLevel;

/**
 * Multiple database backup engine.
 */
class Db extends Part
{
	/** @var array A list of the databases to be packed */
	private $database_list = array();

	/** @var array The current database configuration data */
	private $database_config = null;

	/** @var DumpBase The current dumper engine used to backup tables */
	private $dump_engine = null;

	/** @var string The contents of the databases.ini file */
	private $databases_ini = '';

	/** @var array An array containing the database definitions of all dumped databases so far */
	private $dumpedDatabases = array();

	/** @var int Total number of databases left to be processed */
	private $total_databases = 0;

	/**
	 * Implements the constructor of the class
	 *
	 * @return  Db
	 */
	public function __construct()
	{
		parent::__construct();

		Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . " :: New instance");
	}

	/**
	 * Implements the _prepare abstract method
	 *
	 * @return  void
	 */
	protected function _prepare()
	{
		Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . " :: Preparing instance");

		// Populating the list of databases
		$this->populate_database_list();

		if ($this->getError())
		{
			return;
		}

		$this->total_databases = count($this->database_list);

		$this->setState('prepared');
	}

	/**
	 * Implements the _run() abstract method
	 *
	 * @return  void
	 */
	protected function _run()
	{
		if ($this->getState() == 'postrun')
		{
			Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . " :: Already finished");
			$this->setStep('');
			$this->setSubstep('');
		}
		else
		{
			$this->setState('running');
		}

		// Make sure we have a dumper instance loaded!
		if (is_null($this->dump_engine) && !empty($this->database_list))
		{
			Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . " :: Iterating next database");

			// Create a new instance
			$this->dump_engine = Factory::getDumpEngine(true);

			// Configure the dumper instance and pass on the volatile database root registry key
			$registry = Factory::getConfiguration();
			$rootkeys = array_keys($this->database_list);
			$root = array_shift($rootkeys);
			$registry->set('volatile.database.root', $root);

			$this->database_config = array_shift($this->database_list);
			$this->database_config['root'] = $root;
			$this->database_config['process_empty_prefix'] = ($root == '[SITEDB]') ? true : false;

			Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . " :: Now backing up $root ({$this->database_config['database']})");

			$this->dump_engine->setup($this->database_config);

			// Error propagation
			$this->propagateFromObject($this->dump_engine);

			if ($this->getError())
			{
				return;
			}
		}
		elseif (is_null($this->dump_engine) && empty($this->database_list))
		{
			$this->setError('Current dump engine died while resuming the step');

			return;
		}

		// Try to step the instance
		$retArray = $this->dump_engine->tick();

		// Error propagation
		$this->propagateFromObject($this->dump_engine);

		if ($this->getError())
		{
			return;
		}

		$this->setStep($retArray['Step']);
		$this->setSubstep($retArray['Substep']);

		// Check if the instance has finished
		if (!$retArray['HasRun'])
		{
			// Set the number of parts
			$this->database_config['parts'] = $this->dump_engine->partNumber + 1;

			// Push the definition
			array_push($this->dumpedDatabases, $this->database_config);

			// Go to the next entry in the list and dispose the old AkeebaDumperDefault instance
			$this->dump_engine = null;

			// Are we past the end of the list?
			if (empty($this->database_list))
			{
				Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . " :: No more databases left to iterate");
				$this->setState('postrun');
			}
		}
	}

	/**
	 * Implements the _finalize() abstract method
	 *
	 * @return  void
	 */
	protected function _finalize()
	{
		$this->setState('finished');

		// If we are in db backup mode, don't create a databases.ini
		$configuration = Factory::getConfiguration();

		if (!Factory::getEngineParamsProvider()->getScriptingParameter('db.databasesini', 1))
		{
			Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . " :: Skipping databases.ini");
		}
		// Create the databases.ini contents
		elseif ($this->installerSettings->databasesini)
		{
			$this->createDatabasesINI();

			Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . " :: Creating databases.ini");

			// Create a new string
			$databasesINI = $this->databases_ini;

			// BEGIN FIX 1.2 Stable -- databases.ini isn't written on disk
			Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . " :: Writing databases.ini contents");

			$archiver = Factory::getArchiverEngine();
			$virtualLocation = (Factory::getEngineParamsProvider()->getScriptingParameter('db.saveasname', 'normal') == 'short') ? '' : $this->installerSettings->sqlroot;
			$archiver->addVirtualFile('databases.ini', $virtualLocation, $databasesINI);

			// Error propagation
			$this->propagateFromObject($archiver);

			if ($this->getError())
			{
				return;
			}
		}

		// On alldb mode, we have to finalize the archive as well
		if (Factory::getEngineParamsProvider()->getScriptingParameter('db.finalizearchive', 0))
		{
			Factory::getLog()->log(LogLevel::INFO, "Finalizing database dump archive");

			$archiver = Factory::getArchiverEngine();
			$archiver->finalize();

			// Error propagation
			$this->propagateFromObject($archiver);

			if ($this->getError())
			{
				return;
			}
		}

		// In CLI mode we'll also close the database connection
		if (defined('AKEEBACLI'))
		{
			Factory::getLog()->log(LogLevel::INFO, "Closing the database connection to the main database");
			Factory::unsetDatabase();
		}

		return;
	}

	/**
	 * Populates database_list with the list of databases in the settings
	 *
	 * @return void
	 */
	protected function populate_database_list()
	{
		// Get database inclusion filters
		$filters = Factory::getFilters();
		$this->database_list = $filters->getInclusions('db');

		// Error propagation
		$this->propagateFromObject($filters);

		if ($this->getError())
		{
			return;
		}

		if (Factory::getEngineParamsProvider()->getScriptingParameter('db.skipextradb', 0))
		{
			// On database only backups we prune extra databases
			Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . " :: Adding only main database");

			if (count($this->database_list) > 1)
			{
				$this->database_list = array_slice($this->database_list, 0, 1);
			}
		}
	}

	protected function createDatabasesINI()
	{
		// caching databases.ini contents
		Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . "AkeebaCUBEDomainDBBackup :: Creating databases.ini data");

		// Create a new string
		$this->databases_ini = '';

		$blankOutPass = Factory::getConfiguration()->get('engine.dump.common.blankoutpass', 0);
		$siteRoot     = Factory::getConfiguration()->get('akeeba.platform.newroot', '');

		// Loop through databases list
		foreach ($this->dumpedDatabases as $definition)
		{
			$section = basename($definition['dumpFile']);

			$dboInstance = Factory::getDatabase($definition);
			$type = $dboInstance->name;
			$tech = $dboInstance->getDriverType();

			// If the database is a sqlite one, we have to process the database name which contains the path
			// At the moment we only handle the case where the db file is UNDER site root
			if ($tech == 'sqlite')
			{
				$definition['database'] = str_replace($siteRoot, '#SITEROOT#', $definition['database']);
			}

			if ($blankOutPass)
			{
				$this->databases_ini .= <<<ENDDEF
[$section]
dbtype = "$type"
dbtech = "$tech"
dbname = "{$definition['database']}"
sqlfile = "{$definition['dumpFile']}"
dbhost = "{$definition['host']}"
dbuser = ""
dbpass = ""
prefix = "{$definition['prefix']}"
parts = "{$definition['parts']}"

ENDDEF;

			}
			else
			{
				// We have to escape the password
				$escapedPassword = addcslashes($definition['password'], "\"\\\n\r");

				$this->databases_ini .= <<<ENDDEF
[$section]
dbtype = "$type"
dbtech = "$tech"
dbname = "{$definition['database']}"
sqlfile = "{$definition['dumpFile']}"
dbhost = "{$definition['host']}"
dbuser = "{$definition['username']}"
dbpass = "$escapedPassword"
prefix = "{$definition['prefix']}"
parts = "{$definition['parts']}"

ENDDEF;
			}
		}
	}

	/**
	 * Implements the getProgress() percentage calculation based on how many
	 * databases we have fully dumped and how much of the current database we
	 * have dumped.
	 *
	 * @return  float
	 */
	public function getProgress()
	{
		if ($this->total_databases)
		{
			return 0;
		}

		// Get the overall percentage (based on databases fully dumped so far)
		$remaining_steps = count($this->database_list);
		$remaining_steps++;
		$overall = 1 - ($remaining_steps / $this->total_databases);

		// How much is this step worth?
		$this_max = 1 / $this->total_databases;

		// Get the percentage done of the current database
		$local = $this->dump_engine->getProgress();

		$percentage = $overall + $local * $this_max;

		if ($percentage < 0)
		{
			$percentage = 0;
		}
		elseif ($percentage > 1)
		{
			$percentage = 1;
		}

		return $percentage;
	}
}