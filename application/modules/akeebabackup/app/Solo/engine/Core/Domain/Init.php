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

namespace Akeeba\Engine\Core\Domain;

// Protection against direct access
defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Base\Part;
use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;
use Psr\Log\LogLevel;

/**
 * Backup initialization domain
 */
class Init extends Part
{
	/** @var   string  The backup description */
	private $description = '';

	/** @var   string  The backup comment */
	private $comment = '';

	/**
	 * Implements the constructor of the class
	 *
	 * @return  Init
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
		// Load parameters (description and comment)
		$jpskey = '';
		$angiekey = '';

		if (!empty($this->_parametersArray))
		{
			$params = $this->_parametersArray;

			if (isset($params['description']))
			{
				$this->description = $params['description'];
			}

			if (isset($params['comment']))
			{
				$this->comment = $params['comment'];
			}

			if (isset($params['jpskey']))
			{
				$jpskey = $params['jpskey'];
			}

			if (isset($params['angiekey']))
			{
				$angiekey = $params['angiekey'];
			}
		}

		// Load configuration -- No. This is already done by the model. Doing it again removes all overrides.
		// Platform::getInstance()->load_configuration();

		// Initialize counters
		$registry = Factory::getConfiguration();

		if (!empty($jpskey))
		{
			$registry->set('engine.archiver.jps.key', $jpskey);
		}

		if (!empty($angiekey))
		{
			$registry->set('engine.installer.angie.key', $angiekey);
		}

		// Initialize temporary storage
		Factory::getFactoryStorage()->reset();

		// Force load the tag -- do not delete!
		$kettenrad = Factory::getKettenrad();
		$tag = $kettenrad->getTag(); // Yes, this is an unused variable by we MUST run this method. DO NOT DELETE.

		// Push the comment and description in temp vars for use in the installer phase
		$registry->set('volatile.core.description', $this->description);
		$registry->set('volatile.core.comment', $this->comment);

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

			return;
		}
		else
		{
			$this->setState('running');
		}

		// Initialise the extra notes variable, used by platform classes to return warnings and errors
		$extraNotes = null;

		// Load the version defines
		Platform::getInstance()->load_version_defines();

		$registry = Factory::getConfiguration();

		// Write log file's header
		$version = defined('AKEEBABACKUP_VERSION') ? AKEEBABACKUP_VERSION : AKEEBA_VERSION;
		$date    = defined('AKEEBABACKUP_DATE') ? AKEEBABACKUP_DATE : AKEEBA_DATE;

		Factory::getLog()->log(LogLevel::INFO, "--------------------------------------------------------------------------------");
		Factory::getLog()->log(LogLevel::INFO, "Akeeba Backup " . $version . ' (' . $date . ')');
		Factory::getLog()->log(LogLevel::INFO, "Got backup?");
		Factory::getLog()->log(LogLevel::INFO, "--------------------------------------------------------------------------------");

		// PHP configuration variables are tried to be logged only for debug and info log levels
		if ($registry->get('akeeba.basic.log_level') >= 2)
		{
			Factory::getLog()->log(LogLevel::INFO, "--- System Information ---");
			Factory::getLog()->log(LogLevel::INFO, "PHP Version        :" . PHP_VERSION);
			Factory::getLog()->log(LogLevel::INFO, "PHP OS             :" . PHP_OS);
			Factory::getLog()->log(LogLevel::INFO, "PHP SAPI           :" . PHP_SAPI);

			if (function_exists('php_uname'))
			{
				Factory::getLog()->log(LogLevel::INFO, "OS Version         :" . php_uname('s'));
			}

			$db = Factory::getDatabase();
			Factory::getLog()->log(LogLevel::INFO, "DB Version         :" . $db->getVersion());

			if (isset($_SERVER['SERVER_SOFTWARE']))
			{
				$server = $_SERVER['SERVER_SOFTWARE'];
			}
			elseif (($sf = getenv('SERVER_SOFTWARE')))
			{
				$server = $sf;
			}
			else
			{
				$server = 'n/a';
			}

			Factory::getLog()->log(LogLevel::INFO, "Web Server         :" . $server);

			$platform = 'Unknown platform';
			$version = '(unknown version)';
			$platformData = Platform::getInstance()->getPlatformVersion();
			Factory::getLog()->log(LogLevel::INFO, $platformData['name'] . " version    :" . $platformData['version']);

			if (isset($_SERVER['HTTP_USER_AGENT']))
			{
				Factory::getLog()->log(LogLevel::INFO, "User agent         :" . phpversion() <= "4.2.1" ? getenv("HTTP_USER_AGENT") : $_SERVER['HTTP_USER_AGENT']);
			}

			Factory::getLog()->log(LogLevel::INFO, "Safe mode          :" . ini_get("safe_mode"));
			Factory::getLog()->log(LogLevel::INFO, "Display errors     :" . ini_get("display_errors"));
			Factory::getLog()->log(LogLevel::INFO, "Error reporting    :" . self::error2string());
			Factory::getLog()->log(LogLevel::INFO, "Error display      :" . self::errordisplay());
			Factory::getLog()->log(LogLevel::INFO, "Disabled functions :" . ini_get("disable_functions"));
			Factory::getLog()->log(LogLevel::INFO, "open_basedir restr.:" . ini_get('open_basedir'));
			Factory::getLog()->log(LogLevel::INFO, "Max. exec. time    :" . ini_get("max_execution_time"));
			Factory::getLog()->log(LogLevel::INFO, "Memory limit       :" . ini_get("memory_limit"));

			if (function_exists("memory_get_usage"))
			{
				Factory::getLog()->log(LogLevel::INFO, "Current mem. usage :" . memory_get_usage());
			}

			if (function_exists("gzcompress"))
			{
				Factory::getLog()->log(LogLevel::INFO, "GZIP Compression   : available (good)");
			}
			else
			{
				Factory::getLog()->log(LogLevel::INFO, "GZIP Compression   : n/a (no compression)");
			}

			$extraNotes = Platform::getInstance()->log_platform_special_directories();

			if (!empty($extraNotes) && is_array($extraNotes))
			{
				if (isset($extraNotes['warnings']) && is_array($extraNotes['warnings']))
				{
					foreach ($extraNotes['warnings'] as $warning)
					{
						$this->setWarning($warning);
					}
				}

				if (isset($extraNotes['errors']) && is_array($extraNotes['errors']))
				{
					foreach ($extraNotes['errors'] as $error)
					{
						$this->setError($error);
					}
				}
			}

			Factory::getLog()->log(LogLevel::INFO, "Output directory   :" . $registry->get('akeeba.basic.output_directory'));
			Factory::getLog()->log(LogLevel::INFO, "Part size (bytes)  :" . $registry->get('engine.archiver.common.part_size', 0));
			Factory::getLog()->log(LogLevel::INFO, "--------------------------------------------------------------------------------");
		}

		// Quirks reporting
		$quirks = Factory::getConfigurationChecks()->getDetailedStatus(true);

		if (!empty($quirks))
		{
			Factory::getLog()->log(LogLevel::INFO, "Akeeba Backup has detected the following potential problems:");

			foreach ($quirks as $q)
			{
				Factory::getLog()->log(LogLevel::INFO, '- ' . $q['code'] . ' ' . $q['description'] . ' (' . $q['severity'] . ')');
			}

			Factory::getLog()->log(LogLevel::INFO, "You probably do not have to worry about them, but you should be aware of them.");
			Factory::getLog()->log(LogLevel::INFO, "--------------------------------------------------------------------------------");
		}

		if (!version_compare(PHP_VERSION, '5.4.0', 'ge'))
		{
			Factory::getLog()->log(LogLevel::WARNING, "You are using an outdated version of PHP. Akeeba Engine may not work properly. Please upgrade to PHP 5.4.0 or later.");
		}

		// Report profile ID
		$profile_id = Platform::getInstance()->get_active_profile();
		Factory::getLog()->log(LogLevel::INFO, "Loaded profile #$profile_id");

		// Get archive name
		list($relativeArchiveName, $absoluteArchiveName) = $this->getArchiveName();

		// ==== Stats initialisation ===
		$origin = Platform::getInstance()->get_backup_origin(); // Get backup origin
		$profile_id = Platform::getInstance()->get_active_profile(); // Get active profile

		$registry = Factory::getConfiguration();
		$backupType = $registry->get('akeeba.basic.backup_type');
		Factory::getLog()->log(LogLevel::DEBUG, "Backup type is now set to '" . $backupType . "'");

		// Substitute "variables" in the archive name
		$fsUtils = Factory::getFilesystemTools();
		$description = $fsUtils->replace_archive_name_variables($this->description);
		$comment = $fsUtils->replace_archive_name_variables($this->comment);

		if ($registry->get('volatile.writer.store_on_server', true))
		{
			// Archive files are stored on our server
			$stat_relativeArchiveName = $relativeArchiveName;
			$stat_absoluteArchiveName = $absoluteArchiveName;
		}
		else
		{
			// Archive files are not stored on our server (FTP backup, cloud backup, sent by email, etc)
			$stat_relativeArchiveName = '';
			$stat_absoluteArchiveName = '';
		}

		$kettenrad = Factory::getKettenrad();

		$temp = array(
			'description'   => $description,
			'comment'       => $comment,
			'backupstart'   => Platform::getInstance()->get_timestamp_database(),
			'status'        => 'run',
			'origin'        => $origin,
			'type'          => $backupType,
			'profile_id'    => $profile_id,
			'archivename'   => $stat_relativeArchiveName,
			'absolute_path' => $stat_absoluteArchiveName,
			'multipart'     => 0,
			'filesexist'    => 1,
			'tag'           => $kettenrad->getTag(),
			'backupid'      => $kettenrad->getBackupId(),
		);

		// Save the entry
		$statistics = Factory::getStatistics();
		$statistics->setStatistics($temp);

		if ($statistics->getError())
		{
			$this->setError($statistics->getError());

			return;
		}

		$statistics->release_multipart_lock();

		// Initialize the archive.
		if (Factory::getEngineParamsProvider()->getScriptingParameter('core.createarchive', true))
		{
			Factory::getLog()->log(LogLevel::DEBUG, "Expanded archive file name: " . $absoluteArchiveName);

			Factory::getLog()->log(LogLevel::DEBUG, "Initializing archiver engine");
			$archiver = Factory::getArchiverEngine();
			$archiver->initialize($absoluteArchiveName);
			$archiver->setComment($comment); // Add the comment to the archive itself.
			$archiver->propagateToObject($this);

			if ($this->getError())
			{
				return;
			}
		}

		$this->setState('postrun');
	}

	/**
	 * Implements the abstract _finalize method
	 *
	 * @return  void
	 */
	protected function _finalize()
	{
		$this->setState('finished');
	}

	/**
	 * Converts a PHP error to a string
	 *
	 * @return  string
	 */
	public static function error2string()
	{
		if (function_exists('error_reporting'))
		{
			$value = error_reporting();
		}
		else
		{
			return "Not applicable; host too restrictive";
		}

		$level_names = array(
			E_ERROR         => 'E_ERROR', E_WARNING => 'E_WARNING',
			E_PARSE         => 'E_PARSE', E_NOTICE => 'E_NOTICE',
			E_CORE_ERROR    => 'E_CORE_ERROR', E_CORE_WARNING => 'E_CORE_WARNING',
			E_COMPILE_ERROR => 'E_COMPILE_ERROR', E_COMPILE_WARNING => 'E_COMPILE_WARNING',
			E_USER_ERROR    => 'E_USER_ERROR', E_USER_WARNING => 'E_USER_WARNING',
			E_USER_NOTICE   => 'E_USER_NOTICE'
		);

		if (defined('E_STRICT'))
		{
			$level_names[E_STRICT] = 'E_STRICT';
		}

		$levels = array();

		if (($value & E_ALL) == E_ALL)
		{
			$levels[] = 'E_ALL';
			$value &= ~E_ALL;
		}

		foreach ($level_names as $level => $name)
		{
			if (($value & $level) == $level)
			{
				$levels[] = $name;
			}
		}

		return implode(' | ', $levels);
	}

	/**
	 * Reports whether the error display (output to HTML) is enabled or not
	 *
	 * @return string
	 */
	public static function errordisplay()
	{
		if (!function_exists('ini_get'))
		{
			return "Not applicable; host too restrictive";
		}

		return ini_get('display_errors') ? 'on' : 'off';
	}

	/**
	 * Returns the relative and absolute path to the archive
	 */
	protected function getArchiveName()
	{
		$registry = Factory::getConfiguration();

		// Import volatile scripting keys to the registry
		Factory::getEngineParamsProvider()->importScriptingToRegistry();

		// Determine the extension
		$force_extension = Factory::getEngineParamsProvider()->getScriptingParameter('core.forceextension', null);

		if (is_null($force_extension))
		{
			$archiver = Factory::getArchiverEngine();
			$extension = $archiver->getExtension();
		}
		else
		{
			$extension = $force_extension;
		}

		// Get the template name
		$templateName = $registry->get('akeeba.basic.archive_name');
		Factory::getLog()->log(LogLevel::DEBUG, "Archive template name: $templateName");

		// Parse all tags
		$fsUtils = Factory::getFilesystemTools();
		$templateName = $fsUtils->replace_archive_name_variables($templateName);

		Factory::getLog()->log(LogLevel::DEBUG, "Expanded template name: $templateName");

		$ds = DIRECTORY_SEPARATOR;
		$relative_path = $templateName . $extension;
		$absolute_path = $fsUtils->TranslateWinPath($registry->get('akeeba.basic.output_directory') . $ds . $relative_path);

		return array($relative_path, $absolute_path);
	}
}