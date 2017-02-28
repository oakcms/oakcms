<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2006-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 *
 */

namespace Akeeba\Engine\Dump;

// Protection against direct access
defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Base\Part;
use Akeeba\Engine\Driver\Base as DriverBase;
use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;
use Psr\Log\LogLevel;

abstract class Base extends Part
{
	// **********************************************************************
	// Configuration parameters
	// **********************************************************************

	/** @var string Prefix to this database */
	protected $prefix = '';

	/** @var string MySQL database server host name or IP address */
	protected $host = '';

	/** @var string MySQL database server port (optional) */
	protected $port = '';

	/** @var string MySQL user name, for authentication */
	protected $username = '';

	/** @var string MySQL password, for authentication */
	protected $password = '';

	/** @var string MySQL database */
	protected $database = '';

	/** @var string The database driver to use */
	protected $driver = '';

	/** @var boolean Should I post process quoted values */
	protected $postProcessValues = false;

	// **********************************************************************
	// File handling fields
	// **********************************************************************

	/** @var string Absolute path to dump file; must be writable (optional; if left blank it is automatically calculated) */
	protected $dumpFile = '';

	/** @var string Data cache, used to cache data before being written to disk */
	protected $data_cache = '';

	/** @var int  */
	protected $largest_query = 0;

	/** @var int Size of the data cache, default 128Kb */
	protected $cache_size = 131072;

	/** @var bool Should I process empty prefixes when creating abstracted names? */
	protected $processEmptyPrefix = true;

	/** @var int Current dump file part number */
	public $partNumber = 0;

	/** @var resource Filepointer to the current dump part */
	private $fp = null;

	/** @var string Absolute path to the temp file */
	protected $tempFile = '';

	/** @var string Relative path of how the file should be saved in the archive */
	protected $saveAsName = '';

	// **********************************************************************
	// Protected fields (data handling)
	// **********************************************************************

	/** @var array Contains the sorted (by dependencies) list of tables/views to backup */
	protected $tables = array();

	/** @var array Contains the configuration data of the tables */
	protected $tables_data = array();

	/** @var array Maps database table names to their abstracted format */
	protected $table_name_map = array();

	/** @var array Contains the dependencies of tables and views (temporary) */
	protected $dependencies = array();

	/** @var string The next table to backup */
	protected $nextTable;

	/** @var integer The next row of the table to start backing up from */
	protected $nextRange;

	/** @var integer Current table's row count */
	protected $maxRange;

	/** @var bool Use extended INSERTs */
	protected $extendedInserts = false;

	/** @var integer Maximum packet size for extended INSERTs, in bytes */
	protected $packetSize = 0;

	/** @var string Extended INSERT query, while it's being constructed */
	protected $query = '';

	/** @var int Dump part's maximum size */
	protected $partSize = 0;

	/**
	 * Find where to store the backup files
	 *
	 * @param $partNumber int The SQL part number, default is 0 (.sql)
	 */
	protected function getBackupFilePaths($partNumber = 0)
	{
		Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . " :: Getting temporary file");
		$this->tempFile = Factory::getTempFiles()->registerTempFile(dechex(crc32(microtime())) . '.sql');
		Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . " :: Temporary file is {$this->tempFile}");
		// Get the base name of the dump file
		$partNumber = intval($partNumber);
		$baseName = $this->dumpFile;
		if ($partNumber > 0)
		{
			// The file names are in the format dbname.sql, dbname.s01, dbname.s02, etc
			if (strtolower(substr($baseName, -4)) == '.sql')
			{
				$baseName = substr($baseName, 0, -4) . '.s' . sprintf('%02u', $partNumber);
			}
			else
			{
				$baseName = $baseName . '.s' . sprintf('%02u', $partNumber);
			}
		}

		if (empty($this->installerSettings))
		{
			// Fetch the installer settings
			$this->installerSettings = (object)array(
				'installerroot' => 'installation',
				'sqlroot'       => 'installation/sql',
				'databasesini'  => 1,
				'readme'        => 1,
				'extrainfo'     => 1
			);
			$config = Factory::getConfiguration();
			$installerKey = $config->get('akeeba.advanced.embedded_installer');
			$installerDescriptors = Factory::getEngineParamsProvider()->getInstallerList();
			if (array_key_exists($installerKey, $installerDescriptors))
			{
				// The selected installer exists, use it
				$this->installerSettings = (object)$installerDescriptors[$installerKey];
			}
			elseif (array_key_exists('angie', $installerDescriptors))
			{
				// The selected installer doesn't exist, but ANGIE exists; use that instead
				$this->installerSettings = (object)$installerDescriptors['angie'];
			}
		}

		switch (Factory::getEngineParamsProvider()->getScriptingParameter('db.saveasname', 'normal'))
		{
			case 'output':
				// The SQL file will be stored uncompressed in the output directory
				$statistics = Factory::getStatistics();
				$statRecord = $statistics->getRecord();
				$this->saveAsName = $statRecord['absolute_path'];
				break;

			case 'normal':
				// The SQL file will be stored in the SQL root of the archive, as
				// specified by the particular embedded installer's settings
				$this->saveAsName = $this->installerSettings->sqlroot . '/' . $baseName;
				break;

			case 'short':
				// The SQL file will be stored on archive's root
				$this->saveAsName = $baseName;
				break;
		}

		if ($partNumber > 0)
		{
			Factory::getLog()->log(LogLevel::DEBUG, "AkeebaDomainDBBackup :: Creating new SQL dump part #$partNumber");
		}
		Factory::getLog()->log(LogLevel::DEBUG, "AkeebaDomainDBBackup :: SQL temp file is " . $this->tempFile);
		Factory::getLog()->log(LogLevel::DEBUG, "AkeebaDomainDBBackup :: SQL file location in archive is " . $this->saveAsName);
	}

	/**
	 * Deletes any leftover files from previous backup attempts
	 *
	 */
	protected function removeOldFiles()
	{
		Factory::getLog()->log(LogLevel::DEBUG, "AkeebaDomainDBBackup :: Deleting leftover files, if any");
		if (file_exists($this->tempFile))
		{
			@unlink($this->tempFile);
		}
	}

	/**
	 * Populates the table arrays with the information for the db entities to backup
	 *
	 * @return null
	 */
	protected abstract function getTablesToBackup();

	/**
	 * Runs a step of the database dump
	 *
	 * @return null
	 */
	protected abstract function stepDatabaseDump();

	/**
	 * Implements the _prepare abstract method
	 *
	 */
	protected function _prepare()
	{
		$this->setStep('Initialization');
		$this->setSubstep('');

		// Process parameters, passed to us using the setup() public method
		Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . " :: Processing parameters");
		if (is_array($this->_parametersArray))
		{
			$this->driver = array_key_exists('driver', $this->_parametersArray) ? $this->_parametersArray['driver'] : $this->driver;
			$this->host = array_key_exists('host', $this->_parametersArray) ? $this->_parametersArray['host'] : $this->host;
			$this->port = array_key_exists('port', $this->_parametersArray) ? $this->_parametersArray['port'] : $this->port;
			$this->username = array_key_exists('username', $this->_parametersArray) ? $this->_parametersArray['username'] : $this->username;
			$this->username = array_key_exists('user', $this->_parametersArray) ? $this->_parametersArray['user'] : $this->username;
			$this->password = array_key_exists('password', $this->_parametersArray) ? $this->_parametersArray['password'] : $this->password;
			$this->database = array_key_exists('database', $this->_parametersArray) ? $this->_parametersArray['database'] : $this->database;
			$this->prefix = array_key_exists('prefix', $this->_parametersArray) ? $this->_parametersArray['prefix'] : $this->prefix;
			$this->dumpFile = array_key_exists('dumpFile', $this->_parametersArray) ? $this->_parametersArray['dumpFile'] : $this->dumpFile;
			$this->processEmptyPrefix = array_key_exists('process_empty_prefix', $this->_parametersArray) ? $this->_parametersArray['process_empty_prefix'] : $this->processEmptyPrefix;
		}

		// Make sure we have self-assigned the first part
		$this->partNumber = 0;

		// Get DB backup only mode
		$configuration = Factory::getConfiguration();

		// Find tables to be included and put them in the $_tables variable
		$this->getTablesToBackup();
		if ($this->getError())
		{
			return;
		}

		// Find where to store the database backup files
		$this->getBackupFilePaths($this->partNumber);

		// Remove any leftovers
		$this->removeOldFiles();

		// Initialize the extended INSERTs feature
		$this->extendedInserts = ($configuration->get('engine.dump.common.extended_inserts', 0) != 0);
		$this->packetSize = $configuration->get('engine.dump.common.packet_size', 0);
		if ($this->packetSize == 0)
		{
			$this->extendedInserts = false;
		}

		// Initialize the split dump feature
		$this->partSize = $configuration->get('engine.dump.common.splitsize', 1048576);
		if (Factory::getEngineParamsProvider()->getScriptingParameter('db.saveasname', 'normal') == 'output')
		{
			$this->partSize = 0;
		}
		if (($this->partSize != 0) && ($this->packetSize != 0) && ($this->packetSize > $this->partSize))
		{
			$this->packetSize = $this->partSize / 2;
		}

		// Initialize the algorithm
		Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . " :: Initializing algorithm for first run");
		$this->nextTable = array_shift($this->tables);

		// If there is no table to back up we are done with the database backup
		if (empty($this->nextTable))
		{
			$this->setState('postrun');
			return;
		}

		$this->nextRange = 0;
		$this->query = '';

		// FIX 2.2: First table of extra databases was not being written to disk.
		// This deserved a place in the Bug Fix Hall Of Fame. In subsequent calls to _init, the $fp in
		// _writeline() was not nullified. Therefore, the first dump chunk (that is, the first table's
		// definition and first chunk of its data) were not written to disk. This call causes $fp to be
		// nullified, causing it to be recreated, pointing to the correct file.
		$null = null;
		$this->writeline($null);

		// Finally, mark ourselves "prepared".
		$this->setState('prepared');
	}

	/**
	 * Implements the _run() abstract method
	 */
	protected function _run()
	{
		// Check if we are already done
		if ($this->getState() == 'postrun')
		{
			Factory::getLog()->log(LogLevel::DEBUG, __CLASS__ . " :: Already finished");
			$this->setStep("");
			$this->setSubstep("");

			return;
		}

		// Mark ourselves as still running (we will test if we actually do towards the end ;) )
		$this->setState('running');

		// Check if we are still adding a database dump part to the archive, or if
		// we have to post-process a part
		if (Factory::getEngineParamsProvider()->getScriptingParameter('db.saveasname', 'normal') != 'output')
		{
			$archiver = Factory::getArchiverEngine();
			$configuration = Factory::getConfiguration();

			if ($configuration->get('engine.postproc.common.after_part', 0))
			{
				if (!empty($archiver->finishedPart))
				{
					$filename = array_shift($archiver->finishedPart);
					Factory::getLog()->log(LogLevel::INFO, 'Preparing to post process ' . basename($filename));

                    $timer     = Factory::getTimer();
                    $startTime = $timer->getRunningTime();
					$post_proc = Factory::getPostprocEngine();
					$result    = $post_proc->processPart($filename);
					$this->propagateFromObject($post_proc);

					if ($result === false)
					{
                        Factory::getLog()->log(LogLevel::WARNING, 'Failed to process file ' . $filename);
                        Factory::getLog()->log(LogLevel::WARNING, 'Error received from the post-processing engine:');
                        Factory::getLog()->log(LogLevel::WARNING, implode("\n", array_merge($this->getWarnings(), $this->getErrors())));
						$this->setWarning('Failed to process file ' . basename($filename));
					}
					elseif($result === true)
					{
						// Add this part's size to the volatile storage
						$volatileTotalSize = $configuration->get('volatile.engine.archiver.totalsize', 0);
						$volatileTotalSize += (int)@filesize($filename);
						$configuration->set('volatile.engine.archiver.totalsize', $volatileTotalSize);

						Factory::getLog()->log(LogLevel::INFO, 'Successfully processed file ' . basename($filename));
					}
                    else
                    {
                        // More work required
                        Factory::getLog()->log(LogLevel::INFO, 'More post-processing steps required for file ' . $filename);
                        $configuration->set('volatile.postproc.filename', $filename);

                        // Let's push back the file into the archiver stack
                        array_unshift($archiver->finishedPart, $filename);

                        // Do we need to break the step?
                        $endTime  = $timer->getRunningTime();
                        $stepTime = $endTime - $startTime;
                        $timeLeft = $timer->getTimeLeft();

                        if ($timeLeft < $stepTime)
                        {
                            // We predict that running yet another step would cause a timeout
                            $configuration->set('volatile.breakflag', true);
                        }
                        else
                        {
                            // We have enough time to run yet another step
                            $configuration->set('volatile.breakflag', false);
                        }
                    }

					// Should we delete the file afterwards?
					if (
						$configuration->get('engine.postproc.common.delete_after', false)
						&& $post_proc->allow_deletes
						&& ($result === true)
					)
					{
						Factory::getLog()->log(LogLevel::DEBUG, 'Deleting already processed file ' . basename($filename));
						Platform::getInstance()->unlink($filename);
					}

					if ($post_proc->break_after)
					{
						$configuration->set('volatile.breakflag', true);

						return;
					}
				}
			}

			if ($configuration->get('volatile.engine.archiver.processingfile', false))
			{
				// We had already started archiving the db file, but it needs more time
				$finished = true;
				Factory::getLog()->log(LogLevel::DEBUG, "Continuing adding the SQL dump part to the archive");
				$archiver->addFile(null, null, null);
				$this->propagateFromObject($archiver);
				if ($this->getError())
				{
					return;
				}
				$finished = !$configuration->get('volatile.engine.archiver.processingfile', false);
				if ($finished)
				{
					$this->getNextDumpPart();
				}
				else
				{
					return;
				}
			}
		}

		$this->stepDatabaseDump();

		$null = null;
		$this->writeline($null);
	}

	/**
	 * Implements the _finalize() abstract method
	 *
	 */
	protected function _finalize()
	{
        Factory::getLog()->log(LogLevel::DEBUG, "Adding any extra SQL statements imposed by the filters");
        $filters = Factory::getFilters();
        $this->writeline($filters->getExtraSQL($this->databaseRoot));

		// Close the file pointer (otherwise the SQL file is left behind)
		$this->closeFile();

		// If we are not just doing a main db only backup, add the SQL file to the archive
		$finished = true;
		$configuration = Factory::getConfiguration();
		if (Factory::getEngineParamsProvider()->getScriptingParameter('db.saveasname', 'normal') != 'output')
		{
			$archiver = Factory::getArchiverEngine();
			$configuration = Factory::getConfiguration();

			if ($configuration->get('volatile.engine.archiver.processingfile', false))
			{
				// We had already started archiving the db file, but it needs more time
				Factory::getLog()->log(LogLevel::DEBUG, "Continuing adding the SQL dump to the archive");
				$archiver->addFile(null, null, null);

                $this->propagateFromObject($archiver);

				if ($this->getError())
				{
					return;
				}

				$finished = !$configuration->get('volatile.engine.archiver.processingfile', false);
			}
			else
			{
				// We have to add the dump file to the archive
				Factory::getLog()->log(LogLevel::DEBUG, "Adding the final SQL dump to the archive");
				$archiver->addFileRenamed($this->tempFile, $this->saveAsName);

                $this->propagateFromObject($archiver);

				if ($this->getError())
				{
					return;
				}

				$finished = !$configuration->get('volatile.engine.archiver.processingfile', false);
			}
		}
		else
		{
			// We just have to move the dump file to its final destination
			Factory::getLog()->log(LogLevel::DEBUG, "Moving the SQL dump to its final location");
			$result = Platform::getInstance()->move($this->tempFile, $this->saveAsName);

			if (!$result)
			{
				$this->setError('Could not move the SQL dump to its final location');
			}
		}

		// Make sure that if the archiver needs more time to process the file we can supply it
		if ($finished)
		{
			Factory::getLog()->log(LogLevel::DEBUG, "Removing temporary file of final SQL dump");
			Factory::getTempFiles()->unregisterAndDeleteTempFile($this->tempFile, true);

			if ($this->getError())
			{
				return;
			}

			$this->setState('finished');
		}
	}

	/**
	 * Creates a new dump part
	 */
	protected function getNextDumpPart()
	{

		// On database dump only mode we mustn't create part files!
		if (Factory::getEngineParamsProvider()->getScriptingParameter('db.saveasname', 'normal') == 'output')
		{
			return false;
		}

		// If the archiver is still processing, quit
		$finished = true;
		$configuration = Factory::getConfiguration();
		$archiver = Factory::getArchiverEngine();
		if ($configuration->get('volatile.engine.archiver.processingfile', false))
		{
			return false;
		}

		// We have to add the dump file to the archive
		$this->closeFile();
		Factory::getLog()->log(LogLevel::DEBUG, "Adding the SQL dump part to the archive");
		$archiver->addFileRenamed($this->tempFile, $this->saveAsName);
		if ($this->getError())
		{
			return false;
		}
		$finished = !$configuration->get('volatile.engine.archiver.processingfile', false);
		if (!$finished)
		{
			return false;
		} // Return if the file didn't finish getting added to the archive

		// Remove the old file
		Factory::getLog()->log(LogLevel::DEBUG, "Removing dump part's temporary file");
		Factory::getTempFiles()->unregisterAndDeleteTempFile($this->tempFile, true);

		// Create the new dump part
		$this->partNumber++;
		$this->getBackupFilePaths($this->partNumber);
		$null = null;
		$this->writeline($null);

		return true;
	}

	/**
	 * Creates a new dump part, but only if required to do so
	 *
	 * @return bool
	 */
	protected function createNewPartIfRequired()
	{
		if ($this->partSize == 0)
		{
			return true;
		}
		$filesize = 0;
		if (@file_exists($this->tempFile))
		{
			$filesize = @filesize($this->tempFile);
		}
		if ($this->extendedInserts)
		{
			$projectedSize = $filesize + $this->packetSize;
		}
		else
		{
			$projectedSize = $filesize + strlen($this->query);
		}
		if ($projectedSize > $this->partSize)
		{
			return $this->getNextDumpPart();
		}

		return true;
	}

	/**
	 * Returns a table's abstract name (replacing the prefix with the magic #__ string)
	 *
	 * @param string $tableName The canonical name, e.g. 'jos_content'
	 *
	 * @return string The abstract name, e.g. '#__content'
	 */
	protected function getAbstract($tableName)
	{
		// Don't return abstract names for non-CMS tables
		if (is_null($this->prefix))
		{
			return $tableName;
		}

		switch ($this->prefix)
		{
			case '':
				if ($this->processEmptyPrefix)
				{
					// This is more of a hack; it assumes all tables are core CMS tables if the prefix is empty.
					return '#__' . $tableName;
				}
				else
				{
					// If $this->processEmptyPrefix (the process_empty_prefix config flag) is false, we don't
					// assume anything.
					return $tableName;
				}
				break;

			default:
				// Normal behaviour for 99% of sites
				// Fix 2.4 : Abstracting the prefix only if it's found in the beginning of the table name
				$tableAbstract = $tableName;
				if (!empty($this->prefix))
				{
					if (substr($tableName, 0, strlen($this->prefix)) == $this->prefix)
					{
						$tableAbstract = '#__' . substr($tableName, strlen($this->prefix));
					}
					else
					{
						// FIX 2.4: If there is no prefix, it's a non-core table.
						$tableAbstract = $tableName;
					}
				}

				return $tableAbstract;
				break;
		}
	}

	/**
	 * Writes the SQL dump into the output files. If it fails, it sets the error
	 *
	 * @param string $data Data to write to the dump file. Pass NULL to force flushing to file.
	 *
	 * @return boolean TRUE on successful write, FALSE otherwise
	 */
	protected function writeDump(&$data)
	{
		if (!empty($data))
		{
			$this->data_cache .= $data;

			if (strlen($data) > $this->largest_query)
			{
				$this->largest_query = strlen($data);
				Factory::getConfiguration()->set('volatile.database.largest_query', $this->largest_query);
			}

		}
		if ((strlen($this->data_cache) >= $this->cache_size) || (is_null($data) && (!empty($this->data_cache))))
		{
			Factory::getLog()->log(LogLevel::DEBUG, "Writing " . strlen($this->data_cache) . " bytes to the dump file");
			$result = $this->writeline($this->data_cache);
			if (!$result)
			{
				$errorMessage = 'Couldn\'t write to the SQL dump file ' . $this->tempFile . '; check the temporary directory permissions and make sure you have enough disk space available.';
				$this->setError($errorMessage);

				return false;
			}
			$this->data_cache = '';
		}

		return true;
	}

	/**
	 * Saves the string in $fileData to the file $backupfile. Returns TRUE. If saving
	 * failed, return value is FALSE.
	 *
	 * @param string $fileData Data to write. Set to null to close the file handle.
	 *
	 * @return boolean TRUE is saving to the file succeeded
	 */
	protected function writeline(&$fileData)
	{
		if (!is_resource($this->fp))
		{
			$this->fp = @fopen($this->tempFile, 'a');
			if ($this->fp === false)
			{
				$this->setError('Could not open ' . $this->tempFile . ' for append, in DB dump.');

				return;
			}
		}

		if (is_null($fileData))
		{
			if (is_resource($this->fp))
			{
				@fclose($this->fp);
			}
			$this->fp = null;

			return true;
		}
		else
		{
			if ($this->fp)
			{
				$ret = fwrite($this->fp, $fileData);
				@clearstatcache();

				// Make sure that all data was written to disk
				return ($ret == strlen($fileData));
			}
			else
			{
				return false;
			}
		}
	}

	function _onSerialize()
	{
		$this->closeFile();
	}

	function __destruct()
	{
		$this->closeFile();
	}

	public function closeFile()
	{
		if (is_resource($this->fp))
		{
			Factory::getLog()->log(LogLevel::DEBUG, "Closing SQL dump file.");

			@fclose($this->fp);
			$this->fp = null;
		}
	}

	/**
	 * Return an instance of DriverBase
	 *
	 * @return DriverBase
	 */
	protected function &getDB()
	{
		$host = $this->host . ($this->port != '' ? ':' . $this->port : '');
		$user = $this->username;
		$password = $this->password;
		$driver = $this->driver;
		$database = $this->database;
		$prefix = is_null($this->prefix) ? '' : $this->prefix;
		$options = array('driver' => $driver, 'host' => $host, 'user' => $user, 'password' => $password, 'database' => $database, 'prefix' => $prefix);

		$db = Factory::getDatabase($options);

		if ($error = $db->getError())
		{
			$this->setError(__CLASS__ . ' :: Database Error: ' . $error);
			$false = false;

			return $false;
		}

		if ($db->getErrorNum() > 0)
		{
			$error = $db->getErrorMsg();
			$this->setError(__CLASS__ . ' :: Database Error: ' . $error);
			$false = false;

			return $false;
		}

		return $db;
	}

	/**
	 * Return the current database name by querying the database connection object (e.g. SELECT DATABASE() in MySQL)
	 *
	 * @return  string
	 */
	abstract protected function getDatabaseNameFromConnection();

	/**
	 * Returns the database name. If the name was not declared when the object was created we will go through the
	 * getDatabaseNameFromConnection method to populate it.
	 *
	 * @return  string
	 */
	protected function getDatabaseName()
	{
		if (empty($this->database))
		{
			$this->database = $this->getDatabaseNameFromConnection();
		}

		return $this->database;
	}

	public function callStage($stage)
	{
		switch ($stage)
		{
			case '_prepare':
				return $this->_prepare();
				break;

			case '_run':
				return $this->_run();
				break;

			case '_finalize':
				return $this->_finalize();
				break;
		}
	}

	/**
	 * Post process a quoted value before it's written to the database dump.
	 * So far it's only required for SQL Server which has a problem escaping
	 * newline characters...
	 *
	 * @param   string $value The quoted value to post-process
	 *
	 * @return  string
	 */
	protected function postProcessQuotedValue($value)
	{
		return $value;
	}

	/**
	 * Returns a preamble for the data dump portion of the SQL backup. This is
	 * used to output commands before the first INSERT INTO statement for a
	 * table when outputting a plain SQL file.
	 *
	 * Practical use: the SET IDENTITY_INSERT sometable ON required for SQL Server
	 *
	 * @param   string  $tableAbstract Abstract name of the table, e.g. #__foobar
	 * @param   string  $tableName     Real name of the table, e.g. abc_foobar
	 * @param   integer $maxRange      Row count on this table
	 *
	 * @return  string   The SQL commands you want to be written in the dump file
	 */
	protected function getDataDumpPreamble($tableAbstract, $tableName, $maxRange)
	{
		return '';
	}

	/**
	 * Returns an epilogue for the data dump portion of the SQL backup. This is
	 * used to output commands after the last INSERT INTO statement for a
	 * table when outputting a plain SQL file.
	 *
	 * Practical use: the SET IDENTITY_INSERT sometable OFF required for SQL Server
	 *
	 * @param   string  $tableAbstract Abstract name of the table, e.g. #__foobar
	 * @param   string  $tableName     Real name of the table, e.g. abc_foobar
	 * @param   integer $maxRange      Row count on this table
	 *
	 * @return  string   The SQL commands you want to be written in the dump file
	 */
	protected function getDataDumpEpilogue($tableAbstract, $tableName, $maxRange)
	{
		return '';
	}

	/**
	 * Return a list of field names for the INSERT INTO statements. This is only
	 * required for Microsoft SQL Server because without it the SET IDENTITY_INSERT
	 * has no effect.
	 *
	 * @param   array   $fieldNames  A list of field names in array format
	 * @param   integer $numOfFields The number of fields we should be dumping
	 *
	 * @return  string
	 */
	protected function getFieldListSQL($fieldNames, $numOfFields)
	{
		return '';
	}
}