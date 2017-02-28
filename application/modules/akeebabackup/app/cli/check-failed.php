<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 *
 * Command line interface (CLI) backup script for use with CRON scheduling
 */

namespace Solo;

use Akeeba\Engine\Platform;
use Awf\Mvc\Model;
use Awf\Autoloader\Autoloader;
use Awf\Text\Text;

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
	$factoryPath = __DIR__ . '/../Solo/engine/factory.php';

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

class CheckFailedApplication extends \Awf\Application\Cli
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

		$phpversion		 = PHP_VERSION;
		$phpenvironment	 = PHP_SAPI;

		if ($this->getContainer()->input->get('quiet', -1, 'int') == -1)
		{
			$year = gmdate('Y');
			echo <<<ENDBLOCK
Akeeba Solo Check failed CLI $version ($date)
Copyright (C) 2014-$year Nicholas K. Dionysopoulos / Akeeba Ltd
-------------------------------------------------------------------------------
Akeeba Solo is Free Software, distributed under the terms of the GNU General
Public License version 3 or, at your option, any later version.
This program comes with ABSOLUTELY NO WARRANTY as per sections 15 & 16 of the
license. See http://www.gnu.org/licenses/gpl-3.0.html for details.
-------------------------------------------------------------------------------
You are using PHP $phpversion ($phpenvironment)
$debugmessage
Checking for failed backups

ENDBLOCK;
		}

		// Forced CLI mode settings
		define('AKEEBA_BACKUP_ORIGIN', 'cli');

		/** @var \Solo\Model\Main $cpanelModel */
		$cpanelModel = Model::getInstance('Solo', 'Main', $this->container);
        $result = $cpanelModel->notifyFailed();

        echo implode("\n", $result['message']);

		$this->close();
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
}

if (!isset($container))
{
	$container = new \Solo\Container();
}
$app = new CheckFailedApplication($container);
$app->initialise()->execute();