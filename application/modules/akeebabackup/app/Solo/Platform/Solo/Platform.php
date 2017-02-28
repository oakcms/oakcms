<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 *
 */

namespace Akeeba\Engine\Platform;

use Akeeba\Engine\Platform;
use Akeeba\Engine\Factory;
use Akeeba\Engine\Finalization\TestExtract;
use Psr\Log\LogLevel;

// Protection against direct access
defined('AKEEBAENGINE') or die();

if (!defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR); // Still required by Joomla! :(
}

/**
 * Akeeba Solo platform class
 */
class Solo extends Base
{
	/** @var   integer  Platform class priority */
	public $priority = 53;

	/** @var   string  The platform name */
	public $platformName = 'solo';

	function __construct()
	{
		$configOverrides = array();

		$configOverrides['volatile.core.finalization.action_handlers'] = array(
			new TestExtract()
		);
		$configOverrides['volatile.core.finalization.action_queue_before'] = array(
			'test_extract',
		);

		// Apply the configuration overrides, please
		$this->configOverrides = $configOverrides;
	}

	/**
	 * Performs heuristics to determine if this platform object is the ideal
	 * candidate for the environment Akeeba Engine is running in.
	 *
	 * @return  boolean  True if this platform applies to this environment
	 */
	public function isThisPlatform()
	{
		if (!class_exists('\Awf\Application\Application'))
		{
			return false;
		}

		return true;
	}

	/**
	 * Returns an associative array of stock platform directories
	 *
	 * @return  array  Platform directories
	 */
	public function get_stock_directories()
	{
		static $stock_directories = array();

		if (empty($stock_directories))
		{
			$stock_directories['[SITEROOT]'] = $this->get_site_root();
			$stock_directories['[ROOTPARENT]'] = @realpath($this->get_site_root() . '/..');
			$stock_directories['[SITETMP]'] = APATH_BASE . '/tmp';
			$stock_directories['[DEFAULT_OUTPUT]'] = APATH_BASE . '/backups';
		}

		return $stock_directories;
	}

	/**
	 * Returns the absolute path to the site's root
	 *
	 * @return  string  The absolute path to our own directory
	 */
	public function get_site_root()
	{
		static $root = null;

		if (empty($root) || is_null($root))
		{
			$root = APATH_BASE;

			if (empty($root) || ($root == DIRECTORY_SEPARATOR) || ($root == '/'))
			{
				// Try to get the current root in a different way
				if (function_exists('getcwd'))
				{
					$root = getcwd();
				}

				if (empty($root) || ($root == DIRECTORY_SEPARATOR) || ($root == '/'))
				{
					$root = './';
				}
			}
		}

		return $root;
	}

	/**
	 * Returns the absolute path to the installer images directory
	 *
	 * @return  string  The absolute path to the installer images directory
	 */
	public function get_installer_images_path()
	{
		return \Awf\Application\Application::getInstance()->getContainer()->basePath . '/assets/installers';
	}

	/**
	 * Returns the active profile number
	 *
	 * @return  integer  The active profile number
	 */
	public function get_active_profile()
	{
		if (defined('AKEEBA_PROFILE'))
		{
			return AKEEBA_PROFILE;
		}
		else
		{
			$session = \Awf\Application\Application::getInstance()->getContainer()->segment;

			if (isset($session->profile))
			{
				$profile =  (int)$session->profile;

				if (empty($profile) || ($profile < 1))
				{
					$profile = 1;
				}

				return $profile;
			}
			else
			{
				return 1;
			}
		}
	}

	/**
	 * Returns the selected profile's name. If no ID is specified, the current
	 * profile's name is returned.
	 *
	 * @param   integer  $id  The profile number for which to get the name
	 *
	 * @return  string  The profile's name
	 */
	public function get_profile_name($id = null)
	{
		if (empty($id))
		{
			$id = $this->get_active_profile();
		}

		$id = (int)$id;

		if (empty($id))
		{
			$id = 1;
		}

		$db = \Awf\Application\Application::getInstance()->getContainer()->db;
		$query = $db->getQuery(true)
			->select($db->qn('description'))
			->from($db->qn('#__ak_profiles'))
			->where($db->qn('id') . ' = ' . $db->q($id));
		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * Returns the backup origin as set in the AKEEBA_BACKUP_ORIGIN constant, otherwise "backend"
	 *
	 * @return   string  The backup origin
	 */
	public function get_backup_origin()
	{
		if (defined('AKEEBA_BACKUP_ORIGIN'))
		{
			return AKEEBA_BACKUP_ORIGIN;
		}

		return 'backend';
	}

	/**
	 * Returns a MySQL-formatted timestamp out of the current date
	 *
	 * @param   string  $date  [optional] The timestamp to use. Omit to use current timestamp.
	 *
	 * @return  string
	 */
	public function get_timestamp_database($date = 'now')
	{
		$date = new \Awf\Date\Date($date);

		return $date->toSql();
	}

	/**
	 * Returns the current timestamp, taking into account any TZ information,
	 * in the format specified by $format.
	 *
	 * @param   string  $format  Timestamp format string (standard PHP format string)
	 *
	 * @return  string
	 */
	public function get_local_timestamp($format = 'Y-m-d H:i:s')
	{
		$timezone = \Awf\Application\Application::getInstance()->getContainer()->appConfig->get('timezone', 'UTC');

		$dateNow = new \Awf\Date\Date('now', $timezone);

		return $dateNow->format($format, true);
	}

	/**
	 * Returns the current host name
	 *
	 * @return  string
	 */
	public function get_host()
	{
		$overrideURL = Factory::getConfiguration()->get('akeeba.platform.site_url', '');
		$overrideURL = trim($overrideURL);

		if (!empty($overrideURL))
		{
			// An override URL is already specified; use it
			$oURI = new \Awf\Uri\Uri($overrideURL);
		}
		elseif (!array_key_exists('REQUEST_METHOD', $_SERVER))
		{
			// Running under CLI or a broken server
			$url = Platform::getInstance()->get_platform_configuration_option('siteurl', '');
			$oURI = new \Awf\Uri\Uri($url);
		}
		else
		{
			// Running under the web server
			$oURI = \Awf\Uri\Uri::getInstance();
		}

		return $oURI->getHost();
	}

	/**
	 * Returns the site's name. This doesn't apply for Akeeba Solo so we have to fake it.
	 *
	 * @return  string
	 */
	public function get_site_name()
	{
		return "Akeeba Solo";
	}

	/**
	 * Gets the best matching database driver class. $use_platform is ignored in Akeeba Solo.
	 *
	 * @param   boolean $use_platform If set to false, it will forcibly try to assign one of the primitive types
	 *                                (Mysql/Mysqli) and NEVER tell you to use an platform driver class
	 *
	 * @return  string
	 */
	public function get_default_database_driver($use_platform = true)
	{
		$config = \Awf\Application\Application::getInstance()->getContainer()->appConfig;
		$driver = $config->get('dbdriver', 'mysqli');

		return '\\Akeeba\\Engine\\Driver\\' . ucfirst($driver);
	}

	/**
	 * Returns a set of options to connect to the default database
	 *
	 * @return  array  Database connection options
	 */
	public function get_platform_database_options()
	{
		static $options;

		if (empty($options))
		{
			$conf = \Awf\Application\Application::getInstance()->getContainer()->appConfig;

			$options = array(
				'host'     => $conf->get('dbhost'),
				'user'     => $conf->get('dbuser'),
				'password' => $conf->get('dbpass'),
				'database' => $conf->get('dbname'),
				'prefix'   => $conf->get('prefix')
			);

		}

		return $options;
	}

	/**
	 * Provides a platform-specific translation function
	 *
	 * @param   string  $key  The translation key
	 *
	 * @return  string  The translated string
	 */
	public function translate($key)
	{
		return \Awf\Text\Text::_($key);
	}

	/**
	 * Populates global constants holding the Akeeba application version
	 */
	public function load_version_defines()
	{
		$fileName = APATH_BASE . '/version.php';

		if (file_exists($fileName))
		{
			require_once $fileName;
		}

		if (!defined('AKEEBABACKUP_VERSION'))
		{
			define("AKEEBABACKUP_VERSION", "dev");
		}

		if (!defined('AKEEBABACKUP_PRO'))
		{
			define('AKEEBABACKUP_PRO', false);
		}

		if (!defined('AKEEBABACKUP_DATE'))
		{
			$date = new \Awf\Date\Date();
			define("AKEEBABACKUP_DATE", $date->format('Y-m-d'));
		}
	}

	/**
	 * Returns the platform name and version
	 *
	 * @return  array  An array containing the platform name and version, in this order
	 */
	public function getPlatformVersion()
	{
		return array(
			'name'    => 'Akeeba Solo',
			'version' => AKEEBABACKUP_VERSION
		);
	}

	/**
	 * Logs platform-specific directories with INFO log level
	 *
	 * @return  array|null
	 */
	public function log_platform_special_directories()
	{
		$site_root = $this->get_site_root();

		Factory::getLog()->log(LogLevel::INFO, "APATH_BASE         :" . APATH_BASE);
		Factory::getLog()->log(LogLevel::INFO, "Application Path   :" . \Awf\Application\Application::getInstance()->getContainer()->basePath);
		Factory::getLog()->log(LogLevel::INFO, "Installation root  :" . $this->get_site_root());

		// If the release is older than 3 months, issue a warning
		if (defined('AKEEBABACKUP_DATE'))
		{
			$releaseDate = new \Awf\Date\Date(AKEEBABACKUP_DATE);

			if (time() - $releaseDate->toUnix() > 7776000)
			{
				if (!isset($ret['warnings']))
				{
					$ret['warnings'] = array();
					$ret['warnings'] = array_merge($ret['warnings'], array(
						'Your version of Akeeba Solo is more than 90 days old and most likely already out of date. Please check if a newer version is published and install it.'
					));
				}
			}

		}

		// Detect UNC paths and warn the user
		if(DIRECTORY_SEPARATOR == '\\') {
			if( (substr(APATH_ROOT, 0, 2) == '\\\\') || (substr(APATH_ROOT, 0, 2) == '//') ) {
				if (!isset($ret['warnings']))
				{
					$ret['warnings'] = array();
				}

				$ret['warnings'] = array_merge($ret['warnings'], array(
					'Your site\'s root is using a UNC path (e.g. \\SERVER\path\to\root). PHP has known bugs which may',
					'prevent it from working properly on a site like this. Please take a look at',
					'https://bugs.php.net/bug.php?id=40163 and https://bugs.php.net/bug.php?id=52376. As a result your',
					'backup may fail.'
				));
			}
		}

		if (empty($ret))
		{
			$ret = null;
		}

		return $ret;
	}

	/**
	 * Loads a platform-specific software configuration option
	 *
	 * @param   string  $key      The configuration option's key
	 * @param   mixed   $default  The default value to use
	 *
	 * @return  mixed
	 */
	public function get_platform_configuration_option($key, $default)
	{
		$config = \Awf\Application\Application::getInstance()->getContainer()->appConfig;

		return $config->get('options.' . $key, $default);
	}

	/**
	 * Returns a list of emails to the Super Administrators
	 *
	 * @return  array
	 */
	public function get_administrator_emails()
	{
		$ret = array();

		$emails = $this->get_platform_configuration_option('frontend_email_address', '');

		if (!empty($emails))
		{
			$emails = explode(',', $emails);
			foreach ($emails as $email)
			{
				$ret[] = trim($email);
			}
		}

		return $ret;
	}

	/**
	 * Sends a very simple email using the platform's mailer facility
	 *
	 * @param   string $to         The recipient's email address
	 * @param   string $subject    The subject of the email
	 * @param   string $body       The body of the email
	 * @param   string $attachFile The file to attach (null to not attach any files)
	 *
	 * @return  boolean
	 */
	public function send_email($to, $subject, $body, $attachFile = null)
	{
		Factory::getLog()->log(LogLevel::DEBUG, "-- Fetching mailer object");

		$app = \Awf\Application\Application::getInstance();
		$mailer = $app->getContainer()->mailer;

		if (!is_object($mailer))
		{
			Factory::getLog()->log(LogLevel::WARNING, "Could not send email to $to - Reason: Mailer object is not an object; please check your system settings");

			return false;
		}

		Factory::getLog()->log(LogLevel::DEBUG, "-- Creating email message");

		$recipient = array($to);
		$mailer->addRecipient($recipient);
		$mailer->setSubject($subject);
		$mailer->setBody($body);

		if (!empty($attachFile))
		{
			Factory::getLog()->log(LogLevel::WARNING, "-- Attaching $attachFile");

			if (!file_exists($attachFile) || !(is_file($attachFile) || is_link($attachFile)))
			{
				Factory::getLog()->log(LogLevel::WARNING, "The file does not exist, or it's not a file; no email sent");

				return false;
			}

			if (!is_readable($attachFile))
			{
				Factory::getLog()->log(LogLevel::WARNING, "The file is not readable; no email sent");

				return false;
			}

			$fileSize = @filesize($attachFile);

			if ($fileSize)
			{
				// Check that we have AT LEAST 2.5 times free RAM as the filesize (that's how much we'll need)
				if (!function_exists('ini_get'))
				{
					// Assume 8Mb of PHP memory limit (worst case scenario)
					$totalRAM = 8388608;
				}
				else
				{
					$totalRAM = ini_get('memory_limit');
					if (strstr($totalRAM, 'M'))
					{
						$totalRAM = (int)$totalRAM * 1048576;
					}
					elseif (strstr($totalRAM, 'K'))
					{
						$totalRAM = (int)$totalRAM * 1024;
					}
					elseif (strstr($totalRAM, 'G'))
					{
						$totalRAM = (int)$totalRAM * 1073741824;
					}
					else
					{
						$totalRAM = (int)$totalRAM;
					}
					if ($totalRAM <= 0)
					{
						// No memory limit? Cool! Assume 1Gb of available RAM (which is absurdly abundant as of March 2011...)
						$totalRAM = 1086373952;
					}
				}
				if (!function_exists('memory_get_usage'))
				{
					$usedRAM = 8388608;
				}
				else
				{
					$usedRAM = memory_get_usage();
				}

				$availableRAM = $totalRAM - $usedRAM;

				if ($availableRAM < 2.5 * $fileSize)
				{
					Factory::getLog()->log(LogLevel::WARNING, "The file is too big to be sent by email. Please use a smaller Part Size for Split Archives setting.");
					Factory::getLog()->log(LogLevel::DEBUG, "Memory limit $totalRAM bytes -- Used memory $usedRAM bytes -- File size $fileSize -- Attachment requires approx. " . (2.5 * $fileSize) . " bytes");

					return false;
				}
			}
			else
			{
				Factory::getLog()->log(LogLevel::WARNING, "Your server fails to report the file size of $attachFile. If the backup crashes, please use a smaller Part Size for Split Archives setting");
			}

			$mailer->addAttachment($attachFile);
		}

		Factory::getLog()->log(LogLevel::DEBUG, "-- Sending message");

		try
		{
			$result = $mailer->Send();
		}
		catch (\Exception $e)
		{
			Factory::getLog()->log(LogLevel::WARNING, "Could not email $to:");
			Factory::getLog()->log(LogLevel::WARNING, $e->getMessage());
			$ret = $e->getMessage();
			unset($result);
			unset($mailer);

			return $ret;
		}

		Factory::getLog()->log(LogLevel::DEBUG, "-- Email sent");

		return true;
	}

	/**
	 * Deletes a file from the local server using direct file access or FTP
	 *
	 * @param   string  $file  File path to delete
	 *
	 * @return  boolean  True on success
	 */
	public function unlink($file)
	{
		try
		{
			$fs = \Awf\Application\Application::getInstance()->getContainer()->fileSystem;
			$result = $fs->delete($file);
		}
		catch (\RuntimeException $e)
		{
			$result = false;
		}

		if (is_null($result))
		{
			$result = true;
		}

		return $result;
	}

	/**
	 * Moves a file around within the local server using direct file access or FTP
	 *
	 * @param   string  $from  File path to copy from
	 * @param   string  $to    File path to copy to
	 *
	 * @return  boolean  True on success
	 */
	public function move($from, $to)
	{
		try
		{
			$fs = \Awf\Application\Application::getInstance()->getContainer()->fileSystem;
			$result = $fs->move($from, $to);
		}
		catch (\RuntimeException $e)
		{
			$result = false;
		}

		if (is_null($result))
		{
			$result = true;
		}

		return $result;
	}

	/**
	 * Stores a flash (temporary) variable in the session.
	 *
	 * @param   string $name  The name of the variable to store
	 * @param   string $value The value of the variable to store
	 *
	 * @return  void
	 */
	public function set_flash_variable($name, $value)
	{
		$session = \Awf\Application\Application::getInstance()->getContainer()->segment;

		$session->setFlash($name, $value);
	}

	/**
	 * Return the value of a flash (temporary) variable from the session and
	 * immediately removes it.
	 *
	 * @param   string $name    The name of the flash variable
	 * @param   mixed  $default Default value, if the variable is not defined
	 *
	 * @return  mixed  The value of the variable or $default if it's not set
	 */
	public function get_flash_variable($name, $default = null)
	{
		$session = \Awf\Application\Application::getInstance()->getContainer()->segment;

		$value = $session->getFlash($name);

		if (is_null($value))
		{
			$value = $default;
		}

		return $value;
	}

	/**
	 * Perform an immediate redirection to the defined URL
	 *
	 * @param   string  $url  The URL to redirect to
	 *
	 * @return  void
	 */
	public function redirect($url)
	{
		\Awf\Application\Application::getInstance()->redirect($url);
	}

	/**
	 * Applies the quirk definitions for Akeeba Solo
	 *
	 * @return  void
	 */
	public function apply_quirk_definitions()
	{
		Factory::getConfigurationChecks()->clearConfigurationCheckDefinitions();
		// Output directory unwritable
		Factory::getConfigurationChecks()->addConfigurationCheckDefinition('001', 'critical');
		// Free memory too low
		Factory::getConfigurationChecks()->addConfigurationCheckDefinition('004', 'critical');
		// Missing root definition for backup
		Factory::getConfigurationChecks()->addConfigurationCheckDefinition('011', 'critical', 'SOLO_AEQ_011', array('\\Akeeba\\Engine\\Platform\\Solo', 'quirk_011'));
		// Missing database connection information for backup
		Factory::getConfigurationChecks()->addConfigurationCheckDefinition('012', 'critical', 'SOLO_AEQ_012', array('\\Akeeba\\Engine\\Platform\\Solo', 'quirk_012'));
		// Output folder within component folder (except the default one)
		Factory::getConfigurationChecks()->addConfigurationCheckDefinition('013', 'critical', 'COM_AKEEBA_CPANEL_WARNING_Q013', array('\\Akeeba\\Engine\\Platform\\Solo', 'quirk_013'));
		// open_basedir on output directory
		Factory::getConfigurationChecks()->addConfigurationCheckDefinition('101', 'high');
		// Less than 10" of max_execution_time with PHP Safe Mode enabled
		Factory::getConfigurationChecks()->addConfigurationCheckDefinition('103', 'high');
		// CRC problems with hash extension not present
		Factory::getConfigurationChecks()->addConfigurationCheckDefinition('202', 'medium');
		// Default output directory in use
		Factory::getConfigurationChecks()->addConfigurationCheckDefinition('203', 'low');
		// Disabled functions may affect operation
		Factory::getConfigurationChecks()->addConfigurationCheckDefinition('204', 'medium');
		// ZIP format selected
		Factory::getConfigurationChecks()->addConfigurationCheckDefinition('401', 'low');
	}

	/**
	 * Akeeba Engine quirk Q011 – Missing root definition for backup
	 *
	 * @return  boolean
	 */
	public static function quirk_011()
	{
		$config = Factory::getConfiguration();

		$scriptingChain = Factory::getEngineParamsProvider()->getDomainChain();

		$found = false;

		foreach ($scriptingChain as $step)
		{
			if ($step['domain'] == 'Packing')
			{
				$found = true;
			}
		}

		if (!$found)
		{
			return false;
		}

		$root = $config->get('akeeba.platform.newroot', '');

		return empty($root);
	}

	/**
	 * Akeeba Engine quirk Q012 – Missing database connection information for backup
	 *
	 * @return  boolean
	 */
	public static function quirk_012()
	{
		$config = Factory::getConfiguration();

		$scriptingChain = Factory::getEngineParamsProvider()->getDomainChain();

		$found = false;

		foreach ($scriptingChain as $step)
		{
			if ($step['domain'] == 'PackDB')
			{
				$found = true;
			}
		}

		if (!$found)
		{
			return false;
		}

		$dbDriver = $config->get('akeeba.platform.dbdriver', '');
		$dbHost = $config->get('akeeba.platform.dbhost', '');
		$dbUsername = $config->get('akeeba.platform.dbusername', '');
		$dbName = $config->get('akeeba.platform.dbname', '');

		// I'm using a dummy db driver, everything is always ok
		if (strtolower($dbDriver) == 'none')
		{
			return false;
		}

		// If I'm using the SQLite driver I don't need any further info, I'll simply pack
		// the whole database file inside the backup archive
		if ($dbDriver == 'sqlite')
		{
			return false;
		}

		return empty($dbDriver)
			|| empty($dbHost)
			|| empty($dbUsername)
			|| empty($dbName);
	}

	public static function quirk_013()
	{
		$stock_dirs  = Platform::getInstance()->get_stock_directories();
		$default_out = @realpath($stock_dirs['[DEFAULT_OUTPUT]']);

		$registry = Factory::getConfiguration();
		$outdir = $registry->get('akeeba.basic.output_directory');

		foreach ($stock_dirs as $macro => $replacement)
		{
			$outdir = str_replace($macro, $replacement, $outdir);
		}

		$outdir_real = @realpath($outdir);

		// If the output folder is the default one (or any subdir), we are safe
		if (strpos($outdir_real, $default_out) !== false)
		{
			return false;
		}

		// The same applies to the TMP dir (we force such value if the user is doing something wrong)
		if (strpos($outdir_real, @realpath($stock_dirs['[SITETMP]'])) !== false)
		{
			return false;
		}

		$component_path = @realpath(APATH_BASE);

		if (strpos($outdir_real, $component_path) !== false)
		{
			return true;
		}

		return false;
	}
}