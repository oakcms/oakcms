<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 *
 * Command line interface (CLI) backup script for use with CRON scheduling
 */

namespace Solo;

use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;
use Awf\Autoloader\Autoloader;
use Awf\Mvc\Model;
use Awf\Text\Text;
use Psr\Log\LogLevel;
use Solo\Model\Backup;

// Should I enable debug?
// define('AKEEBADEBUG', 1);

if (defined('AKEEBADEBUG'))
{
	error_reporting(E_ALL | E_NOTICE | E_DEPRECATED);
	ini_set('display_errors', 1);
}

$minphp = '5.3.3';
if (version_compare(PHP_VERSION, $minphp, 'lt'))
{
	$curversion = PHP_VERSION;
	$bindir = PHP_BINDIR;
	echo <<< ENDWARNING
================================================================================
WARNING! Incompatible PHP version $curversion
================================================================================

This CRON script must be run using PHP version $minphp or later. Your server is
currently using a much older version which would cause this script to crash. As
a result we have aborted execution of the script. Please contact your host and
ask them for the correct path to the PHP CLI binary for PHP $minphp or later, then
edit your CRON job and replace your current path to PHP with the one your host
gave you.

For your information, the current PHP version information is as follows.

PATH:    $bindir
VERSION: $curversion

Further clarifications:

1. There is absolutely no possible way that you are receiving this warning in
   error. We are using the PHP_VERSION constant to detect the PHP version you
   are currently using. This is what PHP itself reports as its own version.

2. Even though your *site* may be running in a higher PHP version that the one
   reported above, your CRON scripts will most likely not be running under it.
   This has to do with the fact that your site DOES NOT run under the command
   line and there are different executable files (binaries) for the web and
   command line versions of PHP.

3. Please note that you should not ask us for support about this error. We
   cannot possibly know the correct path to the PHP CLI binary as we have not
   set up your server. Your host does know and can give you that information.

4. The latest published versions of PHP can be found at http://www.php.net/
   Any older version is considered insecure and must not be used on a live
   server. If your server uses a much older version of PHP than that please
   notify your host that their servers are in need of an update.

The execution of this command line script is aborted.

ENDWARNING;
	exit(255);
}

// Timezone fix; avoids errors printed out by PHP 5.3.3+ (thanks Yannick!)
if (function_exists('date_default_timezone_get') && function_exists('date_default_timezone_set'))
{
	if (function_exists('error_reporting'))
	{
		$oldLevel = error_reporting(0);
	}
	$serverTimezone = @date_default_timezone_get();
	if (empty($serverTimezone) || !is_string($serverTimezone))
	{
		$serverTimezone = 'UTC';
	}
	if (function_exists('error_reporting'))
	{
		error_reporting($oldLevel);
	}
	@date_default_timezone_set($serverTimezone);
}

// Include the autoloader
if (false == include __DIR__ . '/../Awf/Autoloader/Autoloader.php')
{
	echo 'ERROR: Autoloader not found' . PHP_EOL;

	exit(1);
}

// Load the integration script
define('AKEEBASOLO', 1);
$dirParts = explode(DIRECTORY_SEPARATOR, $argv[0]);

if (count($dirParts) > 3)
{
	$dirParts = array_splice($dirParts, 0, -3);
	$myDir = implode(DIRECTORY_SEPARATOR, $dirParts);
}

if (@file_exists(__DIR__ . '/../../helpers/integration.php'))
{
	require_once __DIR__ . '/../../helpers/integration.php';
}
elseif (@file_exists('../../helpers/integration.php'))
{
	require_once '../../helpers/integration.php';
}
elseif (@file_exists($myDir . '/helpers/integration.php'))
{
	require_once $myDir . '/helpers/integration.php';
}

// Load the platform defines
if (!defined('APATH_BASE'))
{
	require_once __DIR__ . '/../defines.php';
}

// Load the version file
if (@file_exists(__DIR__ . '/../version.php'))
{
	require_once __DIR__ . '/../version.php';
}

// Add our app to the autoloader, if it's not already set
$prefixes = Autoloader::getInstance()->getPrefixes();
if (!array_key_exists('Solo\\', $prefixes))
{
	Autoloader::getInstance()->addMap('Solo\\', APATH_BASE . '/Solo');
}

// Include the Akeeba Engine factory
if (!defined('AKEEBAENGINE'))
{
	define('AKEEBAENGINE', 1);
	$factoryPath = __DIR__ . '/../Solo/engine/Factory.php';

	// Load the engine
	if (!file_exists($factoryPath))
	{
		echo "ERROR!\n";
		echo "Could not load the backup engine; file does not exist. Technical information:\n";
		echo "Path to " . basename(__FILE__) . ": " . __DIR__ . "\n";
		echo "Path to factory file: $factoryPath\n";
		die("\n");
	}
	else
	{
		try
		{
			require_once $factoryPath;
		}
		catch (\Exception $e)
		{
			echo "ERROR!\n";
			echo "Backup engine returned an error. Technical information:\n";
			echo "Error message:\n\n";
			echo $e->getMessage() . "\n\n";
			echo "Path to " . basename(__FILE__) . ":" . __DIR__ . "\n";
			echo "Path to factory file: $factoryPath\n";
			die("\n");
		}
	}

	Platform::addPlatform('Solo', __DIR__ . '/../Solo/Platform/Solo');
	Platform::getInstance()->load_version_defines();
	Platform::getInstance()->apply_quirk_definitions();
}

class BackupApplication extends \Awf\Application\Cli
{
	const secretKeyRelativePath = '/engine/secretkey.php';

	public function __construct(\Awf\Container\Container $container = null)
	{
		parent::__construct($container);

		if (empty($this->container->basePath))
		{
			$this->container->basePath = APATH_BASE . '/Solo';
		}
	}

	public function initialise()
	{
		// Load the extra language files
		Text::loadLanguage('en-GB', 'akeeba', '.com_akeeba.ini', true, $this->container->languagePath);
		Text::loadLanguage('en-GB', 'akeebabackup', '.com_akeebabackup.ini', true, $this->container->languagePath);
		Text::loadLanguage(null, 'akeeba', '.com_akeeba.ini', true, $this->container->languagePath);
		Text::loadLanguage(null, 'akeebabackup', '.com_akeebabackup.ini', true, $this->container->languagePath);

		// Halt if the configuration does not exist yet
		$configPath = $this->getContainer()->appConfig->getDefaultPath();

		if (!@file_exists($configPath))
		{
			$this->out('Configuration not found; aborting');
			$this->close(254);
		}

		// Load the configuration if it's present
		if (@file_exists($configPath))
		{
			// Load the application's configuration
			$this->container->appConfig->loadConfiguration($configPath);

			// Load Akeeba Engine's settings encryption preferences
			$secretKeyFile = $this->getContainer()->basePath . static::secretKeyRelativePath;

			if (@file_exists($secretKeyFile))
			{
				require_once $secretKeyFile;
			}

			// Load Akeeba Engine's configuration
			Platform::getInstance()->load_configuration();
		}

		return $this;
	}

	/**
	 * Language file processing callback. It converts _QQ_ to " and replaces the product name in the legacy INI files
	 * imported from Akeeba Backup for Joomla!.
	 *
	 * @param   string $filename The full path to the file being loaded
	 * @param   array  $strings  The key/value array of the translations
	 *
	 * @return  boolean|array  False to prevent loading the file, or array of processed language string, or true to
	 *                         ignore this processing callback.
	 */
	public function processLanguageIniFile($filename, $strings)
	{
		foreach ($strings as $k => $v)
		{
			$v = str_replace('_QQ_', '"', $v);
			$v = str_replace('Akeeba Backup', 'Akeeba Solo', $v);
			$strings[$k] = $v;
		}

		return $strings;
	}

	/**
	 * Method to run the application routines.  Most likely you will want to instantiate a controller
	 * and execute it, or perform some sort of task directly.
	 *
	 * @return  void
	 */
	protected function doExecute()
	{
		// Get the backup profile and description
		$profile	 = $this->getContainer()->input->get('profile', 1, 'int');

        if($profile <= 0)
        {
            $profile = 1;
        }

		$description = $this->getContainer()->input->get('description', 'Command-line backup', 'string');
		$overrides	 = $this->getOption('override', array(), false);

		if (!empty($overrides))
		{
			$override_message = "\nConfiguration variables overriden in the command line:\n";
			$override_message .= implode(', ', array_keys($overrides));
			$override_message .= "\n";
		}
		else
		{
			$override_message = "";
		}

		$debugmessage = '';

		if ($this->getContainer()->input->get('debug', -1, 'int') != -1)
		{
			if (!defined('AKEEBADEBUG'))
			{
				define('AKEEBADEBUG', 1);
			}

			$debugmessage = "*** DEBUG MODE ENABLED ***\n";
		}

		$version		 = AKEEBABACKUP_VERSION;
		$date			 = AKEEBABACKUP_DATE;
		$start_backup	 = time();
		$memusage		 = $this->memUsage();

		$phpversion		 = PHP_VERSION;
		$phpenvironment	 = PHP_SAPI;

		$verboseMode = $this->getContainer()->input->get('quiet', -1, 'int') == -1;

		if ($verboseMode)
		{
			$year = gmdate('Y');
			echo <<<ENDBLOCK
Akeeba Solo CLI $version ($date)
Copyright (C) 2014-$year Nicholas K. Dionysopoulos / Akeeba Ltd
-------------------------------------------------------------------------------
Akeeba Solo is Free Software, distributed under the terms of the GNU General
Public License version 3 or, at your option, any later version.
This program comes with ABSOLUTELY NO WARRANTY as per sections 15 & 16 of the
license. See http://www.gnu.org/licenses/gpl-3.0.html for details.
-------------------------------------------------------------------------------
You are using PHP $phpversion ($phpenvironment)
$debugmessage
Starting a new backup with the following parameters:
Profile ID  $profile
Description "$description"
$override_message
Current memory usage: $memusage


ENDBLOCK;
		}

		// Attempt to use an infinite time limit, in case you are using the PHP CGI binary instead
		// of the PHP CLI binary. This will not work with Safe Mode, though.
		$safe_mode = true;

		if (function_exists('ini_get'))
		{
			$safe_mode = ini_get('safe_mode');
		}

		if (!$safe_mode && function_exists('set_time_limit'))
		{
			if ($verboseMode)
			{
				echo "Unsetting time limit restrictions.\n";
			}

			@set_time_limit(0);
		}
		elseif (!$safe_mode)
		{
			if ($verboseMode)
			{
				echo "Could not unset time limit restrictions; you may get a timeout error\n";
			}
		}
		else
		{
			if ($verboseMode)
			{
				echo "You are using PHP's Safe Mode; you may get a timeout error\n";
			}
		}

		if ($verboseMode)
		{
			echo "\n";
		}

		// Log some paths
		if ($verboseMode)
		{
			echo "Site paths determined by this script:\n";
			echo "APATH_BASE : " . APATH_BASE . "\n";
		}

		// Forced CLI mode settings
		define('AKEEBA_PROFILE', $profile);
		define('AKEEBA_BACKUP_ORIGIN', 'cli');

		// Check is encrypted settings can be decrypted
		$this->checkSettingsDecryption($profile);

		// Dummy array so that the loop iterates once
		$array = array(
			'HasRun' => 0,
			'Error'	 => '',
			'cli_firstrun' => 1
		);

		$warnings_flag = false;

		/** @var Backup $model */
		$model = Model::getInstance($this->container->application_name, 'Backup', $this->container);

		while (($array['HasRun'] != 1) && (empty($array['Error'])))
		{
			if (isset($array['cli_firstrun']))
			{
				$overrides = array_merge(array(
					'akeeba.tuning.min_exec_time'           => 0,
					'akeeba.tuning.nobreak.beforelargefile' => 1,
					'akeeba.tuning.nobreak.afterlargefile'  => 1,
					'akeeba.tuning.nobreak.proactive'       => 1,
					'akeeba.tuning.nobreak.finalization'    => 1,
					'akeeba.tuning.settimelimit'            => 0,
					'akeeba.tuning.nobreak.domains'         => 0,
				), $overrides);
			}

			$array = isset($array['cli_firstrun']) ? $model->startBackup($overrides) : $model->stepBackup();

			$time		 = date('Y-m-d H:i:s \G\M\TO (T)');
			$memusage	 = $this->memUsage();

			$warnings		 = "no warnings issued (good)";
			$stepWarnings	 = false;

			if (!empty($array['Warnings']))
			{
				$warnings_flag	 = true;
				$warnings		 = "POTENTIAL PROBLEMS DETECTED; " . count($array['Warnings']) . " warnings issued (see below).\n";

				foreach ($array['Warnings'] as $line)
				{
					$warnings .= "\t$line\n";
				}

				$stepWarnings = true;
			}

			if (($verboseMode) || $stepWarnings)
				echo <<<ENDSTEPINFO
Last Tick   : $time
Domain      : {$array['Domain']}
Step        : {$array['Step']}
Substep     : {$array['Substep']}
Memory used : $memusage
Warnings    : $warnings


ENDSTEPINFO;

			// Recycle the database connection to minimise problems with database timeouts
			$db = Factory::getDatabase();
			$db->close();
			$db->open();

			// Reset the backup timer
			Factory::getTimer()->resetTime();
		}

		// Get the correct message and exit code
		$exitCode = 0;

		if ($warnings_flag)
		{
			$exitCode = 1;
		}

		if (!empty($array['Error']))
		{
			echo "An error has occurred:\n{$array['Error']}\n\n";

			$exitCode = 2;
		}

		if (empty($array['Error']) && $verboseMode)
		{
			echo "Backup job finished successfully after approximately " . $this->timeago($start_backup, time(), '', false) . "\n";
		}

		if ($warnings_flag && $verboseMode)
		{
			echo "\n" . str_repeat('=', 79) . "\n";
			echo "!!!!!  W A R N I N G  !!!!!\n\n";
			echo "Akeeba Backup issued warnings during the backup process. You have to review them\n";
			echo "and make sure that your backup has completed successfully. Always test a backup with\n";
			echo "warnings to make sure that it is working properly, by restoring it to a local server.\n";
			echo "DO NOT IGNORE THIS MESSAGE! AN UNTESTED BACKUP IS AS GOOD AS NO BACKUP AT ALL.\n";
			echo "\n" . str_repeat('=', 79) . "\n";
		}

		if ($verboseMode)
		{
			echo "Peak memory usage: " . $this->peakMemUsage() . "\n\n";
		}

		$this->close($exitCode);
	}

	/**
	 * Returns a fancy formatted time lapse code
	 *
	 * @param   integer        $referenceDateTime Timestamp of the reference date/time
	 * @param   string|integer $currentDateTime   Timestamp of the current date/time
	 * @param   string         $measureBy         One of s, m, h, d, or y (time unit)
	 * @param   boolean        $autoText          Append text automatically?
	 *
	 * @return  string
	 */
	private function timeago($referenceDateTime = 0, $currentDateTime = '', $measureBy = '', $autoText = true)
	{
		if ($currentDateTime == '')
		{
			$currentDateTime = time();
		}

		// Raw time difference
		$Raw = $currentDateTime - $referenceDateTime;
		$Clean = abs($Raw);

		$calcNum = array(
			array('s', 60),
			array('m', 60 * 60),
			array('h', 60 * 60 * 60),
			array('d', 60 * 60 * 60 * 24),
			array('y', 60 * 60 * 60 * 24 * 365)
		);

		$calc = array(
			's' => array(1, 'second'),
			'm' => array(60, 'minute'),
			'h' => array(60 * 60, 'hour'),
			'd' => array(60 * 60 * 24, 'day'),
			'y' => array(60 * 60 * 24 * 365, 'year')
		);

		if ($measureBy == '')
		{
			$usemeasure = 's';

			for ($i = 0; $i < count($calcNum); $i++)
			{
				if ($Clean <= $calcNum[$i][1])
				{
					$usemeasure = $calcNum[$i][0];
					$i = count($calcNum);
				}
			}
		}
		else
		{
			$usemeasure = $measureBy;
		}

		$datedifference = floor($Clean / $calc[$usemeasure][0]);

		if ($autoText == true && ($currentDateTime == time()))
		{
			if ($Raw < 0)
			{
				$prospect = ' from now';
			}
			else
			{
				$prospect = ' ago';
			}
		}
		else
		{
			$prospect = '';
		}

		if ($referenceDateTime != 0)
		{
			if ($datedifference == 1)
			{
				return $datedifference . ' ' . $calc[$usemeasure][1] . ' ' . $prospect;
			}
			else
			{
				return $datedifference . ' ' . $calc[$usemeasure][1] . 's ' . $prospect;
			}
		}
		else
		{
			return 'No input time referenced.';
		}
	}

	/**
	 * Returns the current memory usage
	 *
	 * @return string
	 */
	private function memUsage()
	{
		if (function_exists('memory_get_usage'))
		{
			$size = memory_get_usage();
			$unit = array('b', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb');

			return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
		}
		else
		{
			return "(unknown)";
		}
	}

	/**
	 * Returns the peak memory usage
	 *
	 * @return string
	 */
	private function peakMemUsage()
	{
		if (function_exists('memory_get_peak_usage'))
		{
			$size = memory_get_peak_usage();
			$unit = array('b', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb');

			return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
		}
		else
		{
			return "(unknown)";
		}
	}

	/**
	 * Parses POSIX command line options and returns them as an associative array. Each array item contains
	 * a single dimensional array of values. Arguments without a dash are silently ignored.
	 *
	 * @return array
	 */
	private function parseOptions()
	{
		global $argc, $argv;

		// Workaround for PHP-CGI
		if (!isset($argc) && !isset($argv))
		{
			$query = "";

			if (!empty($_GET))
			{
				foreach ($_GET as $k => $v)
				{
					$query .= " $k";

					if ($v != "")
					{
						$query .= "=$v";
					}
				}
			}
			$query = ltrim($query);
			$argv = explode(' ', $query);
			$argc = count($argv);
		}

		$currentName = "";
		$options = array();

		for ($i = 1; $i < $argc; $i++)
		{
			$argument = $argv[$i];

			if (strpos($argument, "-") === 0)
			{
				$argument = ltrim($argument, '-');

				if (strstr($argument, '='))
				{
					list($name, $value) = explode('=', $argument, 2);
				}
				else
				{
					$name = $argument;
					$value = null;
				}

				$currentName = $name;

				if (!isset($options[$currentName]) || ($options[$currentName] == null))
				{
					$options[$currentName] = array();
				}
			}
			else
			{
				$value = $argument;
			}
			if ((!is_null($value)) && (!is_null($currentName)))
			{
				if (strstr($value, '='))
				{
					$parts = explode('=', $value, 2);
					$key = $parts[0];
					$value = $parts[1];
				}
				else
				{
					$key = null;
				}

				$values = $options[$currentName];

				if (is_null($values))
				{
					$values = array();
				}

				if (is_null($key))
				{
					array_push($values, $value);
				}
				else
				{
					$values[$key] = $value;
				}

				$options[$currentName] = $values;
			}
		}

		return $options;
	}

	/**
	 * Returns the value of a command line option
	 *
	 * @param   string  $key             The full name of the option, e.g. "foobar"
	 * @param   mixed   $default         The default value to return
	 * @param   boolean $first_item_only Return only the first value specified (default = true)
	 *
	 * @return  mixed
	 */
	private function getOption($key, $default = null, $first_item_only = true)
	{
		static $options = null;

		if (is_null($options))
		{
			$options = $this->parseOptions();
		}

		if (!array_key_exists($key, $options))
		{
			return $default;
		}
		else
		{
			if ($first_item_only)
			{
				return $options[$key][0];
			}
			else
			{
				return $options[$key];
			}
		}
	}

	/**
	 * Checks if the settings decryption works for the specifed backup profile. If not, the backup is halted.
	 *
	 * @param   int  $profile  The backup profile to check.
	 */
	private function checkSettingsDecryption($profile)
	{
		try
		{
			$platform = Platform::getInstance();
			$platform->decryptionException = true;
			$platform->load_configuration();
			$platform->decryptionException = false;
		}
		catch (Akeeba\Engine\Platform\Exception\DecryptionException $e)
		{
			$phpversion     = PHP_VERSION;
			$phpenvironment = PHP_SAPI;

			// Sorry for the obscure code, I'm trying to work around idiot hosts
			$fName  = 'b' . strtolower('AS') . 'e' . (4 * 16) . '_';
			$fName1 = $fName . 'en';
			$fName2 = $fName . 'de';
			$fName1 .= 'code';
			$fName2 .= 'code';

			$errors         = array();
			$hostResolution = false;

			if ((!function_exists($fName1) || !function_exists($fName2)))
			{
				$errors[]       = $fName1 . ' and/or ' . $fName2 . ' are disabled by your host.';
				$hostResolution = true;
			}

			if (!function_exists('mcrypt_module_open') && !function_exists('openssl_decrypt'))
			{
				$errors[]       = 'Neither mcrypt nor OpenSSL PHP extension is available';
				$hostResolution = true;
			}

			if (empty($errors))
			{
				$errors[] = 'The encryption key has changed';
			}

			$flatErrors = implode("\n", $errors);

			$resolutionMessage = <<< MESSAGE
Since your encryption key has changed you have permanently lost all your
encrypted settings. Please reconfigure the backup profile and retry taking a
backup. There is nothing else you can do.

MESSAGE;

			if ($hostResolution)
			{
				$iniFileLoaded = "(Could not detect path to INI file. Ask your host for support.)";

				if (function_exists('php_ini_loaded_file') && (php_ini_loaded_file() !== false))
				{
					$iniFileLoaded = php_ini_loaded_file();
				}

				$resolutionMessage = <<< MESSAGE
Please ask your host to correct these errors on their server configuration.

Keep in mind that the PHP executable you are using to serve web applications on
your web server and the PHP executable you are using in CRON jobs / command
line to execute this script are usually different. Different executables have
different configuration files. The php.ini file loaded for PHP $phpversion ($phpenvironment) is:

$iniFileLoaded

You need to modify that file to fix the issues listed above. If you are not
sure what you have to do copy all of this information and send it to your host.
They know what to do. Alternatively set "Secure settings" to Off in the options
page.

MESSAGE;
			}

			echo <<< ERROR
An error has occurred:
Could not decrypt settings for profile #$profile

The settings for backup profile #$profile are stored encrypted in your database.
However, PHP $phpversion ($phpenvironment) on your server -which you use to run this backup
script- does not support encrypted settings. Since the settings cannot be read
the backup has failed.

The detected errors are:

$flatErrors

$resolutionMessage
ERROR;
			$this->close(2);
		}
	}

}

if (!isset($container))
{
	$container = new \Solo\Container();
}
$app = new BackupApplication($container);
$app->initialise()->execute();