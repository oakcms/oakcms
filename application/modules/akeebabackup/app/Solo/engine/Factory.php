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

namespace Akeeba\Engine;

// Protection against direct access
defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Core\Database;
use Akeeba\Engine\Util\PushMessages;
use Psr\Log\LogLevel;

// Try to kill errors display
if (function_exists('ini_set') && !defined('AKEEBADEBUG'))
{
	ini_set('display_errors', false);
}

define('AKEEBA_CACERT_PEM', __DIR__ . '/cacert.pem');

// Make sure the class autoloader is loaded
require_once __DIR__ . '/Autoloader.php';

/**
 * The Akeeba Engine Factory class
 *
 * This class is responsible for instantiating all Akeeba Engine classes
 */
class Factory
{
	/**
	 * A list of instantiated objects which will persist after serialisation / unserialisation
	 *
	 * @var   array
	 */
	protected $objectList = array();

	/**
	 * A list of instantiated objects which will NOT persist after serialisation / unserialisation
	 *
	 * @var   array
	 */
	protected $temporaryObjectList = array();

	/**
	 * The absolute path to Akeeba Engine's installation
	 *
	 * @var  string
	 */
    private static $root;

	/**
	 * Private constructor makes sure we can't directly instantiate the class
	 */
	private function __construct()
	{
	}

	/**
	 * The magic __sleep method is called before serialising this object. We need to return an array with the properties
	 * which will be serialised.
	 *
	 * @return  array
	 */
	function __sleep()
	{
		return array('objectList');
	}

	/**
	 * Gets a single, internally used instance of the Factory
	 *
	 * @param   string  $serialized_data  [optional] Serialized data to spawn the instance from
	 *
	 * @return  Factory  A reference to the unique Factory object instance
	 */
	public static function &getInstance($serialized_data = null)
	{
		static $myInstance;

		if (!is_null($serialized_data))
		{
			$myInstance = unserialize($serialized_data);
		}

		if (!is_object($myInstance))
		{
			$myInstance = new self();
		}

		return $myInstance;
	}

	/**
	 * Internal function which instantiates an object of a class named $class_name.
	 *
	 * @param   string  $class_name
	 *
	 * @return  object
	 */
	protected static function &getObjectInstance($class_name)
	{
		$self = self::getInstance();

		if (!isset($self->objectList[$class_name]))
		{
			$self->objectList[$class_name] = false;

			if (class_exists($class_name, true))
			{
				$self->objectList[$class_name] = new $class_name;
			}
		}

		return $self->objectList[$class_name];
	}

	/**
	 * Internal function which removes the object of the class named $class_name
	 *
	 * @param  string $class_name
	 *
	 * @return void
	 */
	protected static function unsetObjectInstance($class_name)
	{
		$self = self::getInstance();

		if (isset($self->objectList[$class_name]))
		{
			$self->objectList[$class_name] = null;
			unset($self->objectList[$class_name]);
		}
	}

	/**
	 * Internal function which instantiates an object of a class named $class_name. This is a temporary instance which
	 * will not survive serialisation and subsequent unserialisation.
	 *
	 * @param   string  $class_name
	 *
	 * @return  object
	 */
	protected static function &getTempObjectInstance($class_name)
	{
		$self = self::getInstance();

		if (!isset($self->temporaryObjectList[$class_name]))
		{
			$self->temporaryObjectList[$class_name] = false;

			if (class_exists($class_name, true))
			{
				$self->temporaryObjectList[$class_name] = new $class_name;
			}
		}

		return $self->temporaryObjectList[$class_name];
	}

	/**
	 * Internal function which removes the object of the class named $class_name. This is a temporary instance which
	 * will not survive serialisation and subsequent unserialisation.
	 *
	 * @param   string  $class_name  The class name of the object to remove
	 *
	 * @return  void
	 */
	protected static function unsetTempObjectInstance($class_name)
	{
		$self = self::getInstance();

		if (isset($self->temporaryObjectList[$class_name]))
		{
			$self->temporaryObjectList[$class_name] = null;
			unset($self->temporaryObjectList[$class_name]);
		}
	}

	// ========================================================================
	// Public factory interface
	// ========================================================================

	/**
	 * Gets a serialized snapshot of the Factory for safekeeping (hibernate)
	 *
	 * @return  string  The serialized snapshot of the Factory
	 */
	public static function serialize()
	{
		// Call _onSerialize in all classes known to the factory
		$self = self::getInstance();

		if (!empty($self->objectList))
		{
			foreach ($self->objectList as $class_name => $object)
			{
				$o = $self->objectList[$class_name];

				if (method_exists($o, '_onSerialize'))
				{
					call_user_func(array($o, '_onSerialize'));
				}
			}
		}

		// Serialize the factory
		return serialize(self::getInstance());
	}

	/**
	 * Regenerates the full Factory state from a serialized snapshot (resume)
	 *
	 * @param   string  $serialized_data  The serialized snapshot to resume from
	 *
	 * @return  void
	 */
	public static function unserialize($serialized_data)
	{
		self::getInstance($serialized_data);
	}

	/**
	 * Reset the internal factory state, freeing all previously created objects
	 *
	 * @return  void
	 */
	public static function nuke()
	{
		$self = self::getInstance();

		foreach ($self->objectList as $key => $object)
		{
			$self->objectList[$key] = null;
		}

		$self->objectList = array();
	}

	/**
	 * Saves the engine state to temporary storage
	 *
	 * @param   string  $tag       The backup origin to save. Leave empty to get from already loaded Kettenrad instance.
	 * @param   string  $backupId  The backup ID to save. Leave empty to get from already loaded Kettenrad instance.
	 *
	 * @return  void
	 *
	 * @throws  \RuntimeException  When the state save fails for any reason
	 */
	public static function saveState($tag = null, $backupId = null)
	{
		$kettenrad = self::getKettenrad();

		if (empty($tag))
		{
			$tag = $kettenrad->getTag();
		}

		if (empty($backupId))
		{
			$backupId = $kettenrad->getBackupId();
		}

		$saveTag = $tag . (empty($backupId) ? '' : ('.' . $backupId));

		$ret = $kettenrad->getStatusArray();

		if ($ret['HasRun'] == 1)
		{
			Factory::getLog()->log(LogLevel::DEBUG, "Will not save a finished Kettenrad instance");

			return;
		}

		Factory::getLog()->log(LogLevel::DEBUG, "Saving Kettenrad instance $tag");

		// Save a Factory snapshot
		$factoryStorage = self::getFactoryStorage();
		$engine = self::getConfiguration()->get('akeeba.core.usedbstorage', 0) ? 'db' : 'file';
		$factoryStorage->setStorageEngine($engine);

		$logger = self::getLog();

		$serializedFactoryData = self::serialize();
		$result = $factoryStorage->set($serializedFactoryData, $saveTag);

		if (!$result)
		{
			$saveKey = $factoryStorage->get_storage_filename($saveTag);
			$errorMessage = "Cannot save factory state in $engine storage, storage key $saveKey";
			$logger->error($errorMessage);

			throw new \RuntimeException($errorMessage);
		}
	}

	/**
	 * Loads the engine state from the storage (if it exists).
	 *
	 * When failIfMissing is true (default) an exception will be thrown if the memory file / database record is no
	 * longer there. This is a clear indication of an issue with the storage engine, e.g. the host deleting the memory
	 * files in the middle of the backup step. Therefore we'll switch the storage engine type before throwing the
	 * exception.
	 *
	 * When failIfMissing is false we do NOT throw an exception. Instead, we do a hard reset of the backup factory. This
	 * is required by the resetState method when we ask it to reset multiple origins at once.
	 *
	 * @param   string  $tag            The backup origin to load
	 * @param   string  $backupId       The backup ID to load
	 * @param   bool    $failIfMissing  Throw an exception if the memory data is no longer there
	 *
	 * @return  void
	 */
	public static function loadState($tag = null, $backupId = null, $failIfMissing = true)
	{
		if (is_null($tag) && defined('AKEEBA_BACKUP_ORIGIN'))
		{
			$tag = AKEEBA_BACKUP_ORIGIN;
		}

		if (is_null($backupId) && defined('AKEEBA_BACKUP_ID'))
		{
			$tag = AKEEBA_BACKUP_ID;
		}

		$loadTag = $tag . (empty($backupId) ? '' : ('.' . $backupId));

		// In order to load anything, we need to have the correct profile loaded. Let's assume
		// that the latest backup record in this tag has the correct profile number set.
		$config = self::getConfiguration();

		if (empty($config->activeProfile))
		{
			$profile = Platform::getInstance()->get_active_profile();

			if (empty($profile) || ($profile <= 1))
			{
				// Only bother loading a configuration if none has been already loaded
				$statList = Platform::getInstance()->get_statistics_list(array(
						'filters'  => array(
							array('field' => 'tag', 'value' => $tag)
						), 'order' => array(
							'by' => 'id', 'order' => 'DESC'
						)
					)
				);

				if (is_array($statList))
				{
					$stat = array_pop($statList);
					$profile = $stat['profile_id'];
				}
			}

			Platform::getInstance()->load_configuration($profile);
		}

		$profile = $config->activeProfile;

		Factory::getLog()->open($loadTag);
		Factory::getLog()->log(LogLevel::DEBUG, "Kettenrad :: Attempting to load from database ($tag) [$loadTag]");

		$serialized_factory = self::getFactoryStorage()->get($loadTag);

		if ($serialized_factory === false)
		{
			if ($failIfMissing)
			{
				// Find the new storage engine we need to use
				$previousEngine = self::getFactoryStorage()->getStorageEngine();
				$newEngine = ($previousEngine == 'file') ? 'db' : 'file';

				Factory::getLog()->log(LogLevel::DEBUG, "Failed loading temporary data using $previousEngine storage engine. Switching to $newEngine. The backup has failed and MUST be restarted.");

				// Switch the engine
				Factory::getConfiguration()->reset();
				Platform::getInstance()->load_configuration($profile);
				$config = Factory::getConfiguration();
				$config->set('akeeba.core.usedbstorage', ($newEngine == 'db'));
				Platform::getInstance()->save_configuration();

				throw new \RuntimeException("Akeeba Engine detected a problem while saving temporary data. Please restart your backup.", 500);
			}

			// There is no serialized factory. Nuke the in-memory factory.
			Factory::getLog()->log(LogLevel::DEBUG, " -- Stored Akeeba Factory ($tag) [$loadTag] not found - hard reset");
			self::nuke();
			Platform::getInstance()->load_configuration($profile);
		}

		Factory::getLog()->log(LogLevel::DEBUG, " -- Loaded stored Akeeba Factory ($tag) [$loadTag]");
		self::unserialize($serialized_factory);

		unset($serialized_factory);
	}

	/**
	 * Resets the engine state, wiping out any pending backups and/or stale temporary data. The configuration parameters
	 * are:
	 * global  bool  True to reset all origins, false to only reset the current origin (default: true)
	 * log     bool  True to log our actions (default: false)
	 * maxrun  int   Only backup records older than this number of seconds will be reset (default: 180)
	 *
	 * @param   array  $config  Configuration parameters for the reset operation
	 *
	 * @return  void
	 */
	public static function resetState($config = array())
	{
		$default_config = array(
			'global' => true, // Reset all origins when true
			'log'    => false, // Log our actions
			'maxrun' => 180, // Consider "pending" backups as failed after this many seconds
		);

		$config = (object)array_merge($default_config, $config);

		// Pause logging if so desired
		if (!$config->log)
		{
			Factory::getLog()->pause();
		}

		$originTag = null;

		if (!$config->global)
		{
			// If we're not resetting globally, get a list of running backups per tag
			$originTag = Platform::getInstance()->get_backup_origin();
		}

		// Cache the factory before proceeding
		$factory = self::serialize();

		$runningList = Platform::getInstance()->get_running_backups($originTag);

		// Origins we have to clean
		$origins = array(
			Platform::getInstance()->get_backup_origin()
		);

		// 1. Detect failed backups
		if (is_array($runningList) && !empty($runningList))
		{
			// The current timestamp
			$now = time();

			// Mark running backups as failed
			foreach ($runningList as $running)
			{
				if (empty($originTag))
				{
					// Check the timestamp of the log file to decide if it's stuck,
					// but only if a tag is not set
					$tstamp = Factory::getLog()->getLastTimestamp($running['origin']);

					if (!is_null($tstamp))
					{
						// We can only check the timestamp if it's returned. If not, we assume the backup is stale
						$difference = abs($now - $tstamp);

						// Backups less than maxrun seconds old are not considered stale (default: 3 minutes)
						if ($difference < $config->maxrun)
						{
							continue;
						}
					}
				}

				$filenames = Factory::getStatistics()->get_all_filenames($running, false);
				$totalSize = 0;

				// Process if there are files to delete...
				if (!is_null($filenames))
				{
					// Delete the failed backup's archive, if exists
					foreach ($filenames as $failedArchive)
					{
						if (file_exists($failedArchive))
						{
							$totalSize += (int)@filesize($failedArchive);
							Platform::getInstance()->unlink($failedArchive);
						}
					}
				}

				// Mark the backup failed
				if (!$running['total_size'])
				{
					$running['total_size'] = $totalSize;
				}

				$running['status'] = 'fail';
				$running['multipart'] = 0;
				$dummy = null;
				Platform::getInstance()->set_or_update_statistics($running['id'], $running, $dummy);

				$backupId = isset($running['backupid']) ? ('.' . $running['backupid']) : '';

				$origins[] = $running['origin'] . $backupId;
			}
		}

		if (!empty($origins))
		{
			$origins = array_unique($origins);

			foreach ($origins as $originTag)
			{
				self::loadState($originTag, null, false);
				// Remove temporary files
				Factory::getTempFiles()->deleteTempFiles();
				// Delete any stale temporary data
				self::getFactoryStorage()->reset($originTag);
			}
		}

		// Reload the factory
		self::unserialize($factory);
		unset($factory);

		// Unpause logging if it was previously paused
		if (!$config->log)
		{
			Factory::getLog()->unpause();
		}
	}

	// ========================================================================
	// Core objects which are part of the engine state
	// ========================================================================

	/**
	 * Returns an Akeeba Configuration object
	 *
	 * @return  \Akeeba\Engine\Configuration  The Akeeba Configuration object
	 */
	public static function &getConfiguration()
	{
		return self::getObjectInstance('\\Akeeba\\Engine\\Configuration');
	}

	/**
	 * Returns a statistics object, used to track current backup's progress
	 *
	 * @return  \Akeeba\Engine\Util\Statistics
	 */
	public static function &getStatistics()
	{
		return self::getObjectInstance('\\Akeeba\\Engine\\Util\\Statistics');
	}

	/**
	 * Returns the currently configured archiver engine
	 *
	 * @param   bool  $reset  Should I try to forcible create a new instance?
	 *
	 * @return  \Akeeba\Engine\Archiver\Base
	 */
	public static function &getArchiverEngine($reset = false)
	{
		static $class_name;

		if ($reset)
		{
			$class_name = null;
		}

		if (empty($class_name))
		{
			$registry = self::getConfiguration();
			$engine = $registry->get('akeeba.advanced.archiver_engine');
			$class_name = '\\Akeeba\\Engine\\Archiver\\' . ucfirst($engine);

			if (!class_exists($class_name, true))
			{
				$class_name = '\\Akeeba\\Engine\\Archiver\\Jpa';
			}
		}

		if ($reset)
		{
			self::unsetObjectInstance($class_name);
		}

		return self::getObjectInstance($class_name);
	}

	/**
	 * Returns the currently configured dump engine
	 *
	 * @param   boolean  $reset  Should I try to forcible create a new instance?
	 *
	 * @return  \Akeeba\Engine\Dump\Base
	 */
	public static function &getDumpEngine($reset = false)
	{
		static $class_name;

		if (empty($class_name))
		{
			$registry = self::getConfiguration();
			$engine = $registry->get('akeeba.advanced.dump_engine');
			$class_name = '\\Akeeba\\Engine\\Dump\\' . ucfirst($engine);

			if (!class_exists($class_name, true))
			{
				$class_name = '\\Akeeba\\Engine\\Dump\\Native';
			}
		}

		if ($reset)
		{
			self::unsetObjectInstance($class_name);
		}

		return self::getObjectInstance($class_name);
	}

	/**
	 * Returns the filesystem scanner engine instance
	 *
	 * @param   bool  $reset  Should I try to forcible create a new instance?
	 *
	 * @return  \Akeeba\Engine\Scan\Base  The scanner engine
	 */
	public static function &getScanEngine($reset = false)
	{
		static $class_name;

		if ($reset)
		{
			$class_name = null;
		}

		if (empty($class_name))
		{
			$registry = self::getConfiguration();
			$engine = $registry->get('akeeba.advanced.scan_engine');
			$class_name = '\\Akeeba\\Engine\\Scan\\' . ucfirst($engine);

			if (!class_exists($class_name, true))
			{
				$class_name = '\\Akeeba\\Engine\\Scan\\Large';
			}
		}

		if ($reset)
		{
			self::unsetObjectInstance($class_name);
		}

		return self::getObjectInstance($class_name);
	}

	/**
	 * Returns the current post-processing engine. If no class is specified we
	 * return the post-processing engine configured in akeeba.advanced.postproc_engine
	 *
	 * @param   string  $engine  The name of the post-processing class to forcibly return
	 *
	 * @return  \Akeeba\Engine\Postproc\Base
	 */
	public static function &getPostprocEngine($engine = null)
	{
		$class_name = '\\Akeeba\\Engine\\Postproc\\Fake';

		if (!is_null($engine))
		{
			$class_name = '\\Akeeba\\Engine\\Postproc\\' . ucfirst($engine);
		}

		if (is_null($engine) || !class_exists($class_name, true))
		{
			$registry = self::getConfiguration();
			$engine = $registry->get('akeeba.advanced.postproc_engine');
			$class_name = '\\Akeeba\\Engine\\Postproc\\' . ucfirst($engine);

			if (!class_exists($class_name, true))
			{
				$class_name = '\\Akeeba\\Engine\\Postproc\\None';
			}
		}

		return self::getObjectInstance($class_name);
	}

	/**
	 * Returns an instance of the Filters feature class
	 *
	 * @return  \Akeeba\Engine\Core\Filters  The Filters feature class' object instance
	 */
	public static function &getFilters()
	{
		return self::getObjectInstance('\\Akeeba\\Engine\\Core\\Filters');
	}

	/**
	 * Returns an instance of the specified filter group class. Do note that it does not
	 * work with platform filter classes. They are handled internally by AECoreFilters.
	 *
	 * @param   string  $filter_name  The filter class to load, without AEFilter prefix
	 *
	 * @return  \Akeeba\Engine\Filter\Base  The filter class' object instance
	 */
	public static function &getFilterObject($filter_name)
	{
		return self::getObjectInstance('\\Akeeba\\Engine\\Filter\\' . ucfirst($filter_name));
	}

	/**
	 * Loads an engine domain class and returns its associated object
	 *
	 * @param   string  $domain_name  The name of the domain, e.g. installer for AECoreDomainInstaller
	 *
	 * @return  \Akeeba\Engine\Base\Part
	 */
	public static function &getDomainObject($domain_name)
	{
		return self::getObjectInstance('\\Akeeba\\Engine\\Core\\Domain\\' . ucfirst($domain_name));
	}

	/**
	 * Returns a database connection object. It's an alias of AECoreDatabase::getDatabase()
	 *
	 * @param   array  $options  Options to use when instantiating the database connection
	 *
	 * @return  \Akeeba\Engine\Driver\Base
	 */
	public static function &getDatabase($options = null)
	{
		if (is_null($options))
		{
			$options = Platform::getInstance()->get_platform_database_options();
		}

		if (isset($options['username']) && !isset($options['user']))
		{
			$options['user'] = $options['username'];
		}

		return Database::getDatabase($options);
	}

	/**
	 * Returns a database connection object. It's an alias of AECoreDatabase::getDatabase()
	 *
	 * @param   array  $options  Options to use when instantiating the database connection
	 *
	 * @return  \Akeeba\Engine\Driver\Base
	 */
	public static function unsetDatabase($options = null)
	{
		if (is_null($options))
		{
			$options = Platform::getInstance()->get_platform_database_options();
		}

		$db = Database::getDatabase($options);
		$db->close();

		Database::unsetDatabase($options);
	}

	/**
	 * Get the a reference to the Akeeba Engine's timer
	 *
	 * @return  \Akeeba\Engine\Core\Timer
	 */
	public static function &getTimer()
	{
		return self::getObjectInstance('\\Akeeba\\Engine\\Core\\Timer');
	}

	/**
	 * Get a reference to Akeeba Engine's main controller called Kettenrad
	 *
	 * @return  \Akeeba\Engine\Core\Kettenrad
	 */
	public static function &getKettenrad()
	{
		return self::getObjectInstance('\\Akeeba\\Engine\\Core\\Kettenrad');
	}

	// ========================================================================
	// Temporary objects which are not part of the engine state
	// ========================================================================

	/**
	 * Returns an instance of the factory storage class (formerly Tempvars)
	 *
	 * @return  \Akeeba\Engine\Util\FactoryStorage
	 */
	public static function &getFactoryStorage()
	{
		return self::getTempObjectInstance('\\Akeeba\\Engine\\Util\\FactoryStorage');
	}

	/**
	 * Returns an instance of the encryption class
	 *
	 * @return  \Akeeba\Engine\Util\Encrypt
	 */
	public static function &getEncryption()
	{
		return self::getTempObjectInstance('\\Akeeba\\Engine\\Util\\Encrypt');
	}

	/**
	 * Returns an instance of the CRC32 calculations class
	 *
	 * @return  \Akeeba\Engine\Util\CRC32
	 */
	public static function &getCRC32Calculator()
	{
		return self::getTempObjectInstance('\\Akeeba\\Engine\\Util\\CRC32');
	}

	/**
	 * Returns an instance of the crypto-safe random value generator class
	 *
	 * @return  \Akeeba\Engine\Util\RandomValue
	 */
	public static function &getRandval()
	{
		return self::getTempObjectInstance('\\Akeeba\\Engine\\Util\\RandomValue');
	}

	/**
	 * Returns an instance of the filesystem tools class
	 *
	 * @return  \Akeeba\Engine\Util\FileSystem
	 */
	public static function &getFilesystemTools()
	{
		return self::getTempObjectInstance('\\Akeeba\\Engine\\Util\\FileSystem');
	}

	/**
	 * Returns an instance of the filesystem tools class
	 *
	 * @return  \Akeeba\Engine\Util\FileLister
	 */
	public static function &getFileLister()
	{
		return self::getTempObjectInstance('\\Akeeba\\Engine\\Util\\FileLister');
	}

	/**
	 * Returns an instance of the engine parameters provider which provides information on scripting, GUI configuration
	 * elements and engine parts
	 *
	 * @return  \Akeeba\Engine\Util\EngineParameters
	 */
	public static function &getEngineParamsProvider()
	{
		return self::getTempObjectInstance('\\Akeeba\\Engine\\Util\\EngineParameters');
	}

	/**
	 * Returns an instance of the log object
	 *
	 * @return  \Akeeba\Engine\Util\Logger
	 */
	public static function &getLog()
	{
		return self::getTempObjectInstance('\\Akeeba\\Engine\\Util\\Logger');
	}

	/**
	 * Returns an instance of the configuration checks object
	 *
	 * @return  \Akeeba\Engine\Util\ConfigurationCheck
	 */
	public static function &getConfigurationChecks()
	{
		return self::getTempObjectInstance('\\Akeeba\\Engine\\Util\\ConfigurationCheck');
	}

	/**
	 * Returns an instance of the secure settings handling object
	 *
	 * @return  \Akeeba\Engine\Util\SecureSettings
	 */
	public static function &getSecureSettings()
	{
		return self::getTempObjectInstance('\\Akeeba\\Engine\\Util\\SecureSettings');
	}

	/**
	 * Returns an instance of the secure settings handling object
	 *
	 * @return  \Akeeba\Engine\Util\TemporaryFiles
	 */
	public static function &getTempFiles()
	{
		return self::getTempObjectInstance('\\Akeeba\\Engine\\Util\\TemporaryFiles');
	}

	/**
	 * Get the connector object for push messages
	 *
	 * @return  PushMessages
	 */
	public static function &getPush()
	{
		return self::getObjectInstance('Akeeba\\Engine\\Util\\PushMessages');
	}

	// ========================================================================
	// Handy functions
	// ========================================================================

	/**
	 * Returns the absolute path to Akeeba Engine's installation
	 *
	 * @return  string
	 */
	public static function getAkeebaRoot()
	{
		if (empty(self::$root))
		{
			self::$root = __DIR__;
		}

		return self::$root;
	}

	// ========================================================================
	// Used in unit testing
	// ========================================================================

	/**
	 * Force an object instance which will survive serialisation. This is supposed to be used only in Unit Tests.
	 *
	 * @param   string       $class_name  The class name used internally by the Factory
	 * @param   object|null  $object      The object to push. Use null to unset the object
	 *
	 * @return  void
	 *
	 * @throws  \Exception  when you try using it outside of Unit Tests
	 */
	public static function forceObjectInstance($class_name, $object)
	{
		if (!interface_exists('PHPUnit_Exception', false))
		{
			$method = __METHOD__;

			throw new \Exception("You can only use $method in Unit Tests", 500);
		}

		$self = self::getInstance();

		if (is_null($object) && isset($self->objectList[$class_name]))
		{
			unset($self->objectList[$class_name]);

			return;
		}

		$self->objectList[$class_name] = $object;
	}

	/**
	 * Force an object instance which will not survive serialisation. This is supposed to be used only in Unit Tests.
	 *
	 * @param   string       $class_name  The class name used internally by the Factory
	 * @param   object|null  $object      The object to push. Use null to unset the object
	 *
	 * @return  void
	 *
	 * @throws  \Exception  when you try using it outside of Unit Tests
	 */
	public static function forceTempObjectInstance($class_name, $object)
	{
		if (!interface_exists('PHPUnit_Exception', false))
		{
			$method = __METHOD__;

			throw new \Exception("You can only use $method in Unit Tests", 500);
		}

		$self = self::getInstance();

		if (is_null($object) && isset($self->temporaryObjectList[ $class_name ]))
		{
			unset($self->temporaryObjectList[ $class_name ]);

			return;
		}

		$self->temporaryObjectList[ $class_name ] = $object;
	}
}

/**
 * Timeout handler. It is registered as a global PHP shutdown function.
 *
 * If a PHP reports a timeout we will log this before letting PHP kill us.
 */
function AkeebaTimeoutTrap()
{
	if (connection_status() >= 2)
	{
		Factory::getLog()->log(LogLevel::ERROR, 'Akeeba Engine has timed out');
	}
}

register_shutdown_function("\\Akeeba\\Engine\\AkeebaTimeoutTrap");