<?php
/**
 * @package		akeebabackupwp
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

// Bootstrap file for Akeeba Solo for WordPress
use Awf\Session;
use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;

/**
 * Make sure we are being called from Akeeba Solo
 */
defined('AKEEBASOLO') or die;

// Makes sure we have PHP 5.4.0 or later
if (version_compare(PHP_VERSION, '5.3.3', 'lt'))
{
	echo sprintf('Akeeba Backup for WordPress requires PHP 5.3.3 or later but your server only has PHP %s.', PHP_VERSION);
}

// Get the root to the Solo app itself
$akeebaBackupWpRoot = __DIR__ . '/../app/';

// Include the autoloader
if (false == include_once $akeebaBackupWpRoot . 'Awf/Autoloader/Autoloader.php')
{
	echo 'ERROR: Autoloader not found' . PHP_EOL;

	exit(1);
}

// Add our app to the autoloader
Awf\Autoloader\Autoloader::getInstance()->addMap('Solo\\', array(
	__DIR__ . '/Solo',
	__DIR__ . '/../app/Solo',
));

// If we are not called from inside WordPress itself we will need to import its configuration
if (!defined('WPINC'))
{
	$foundWpConfig = false;

	$dirParts = explode(DIRECTORY_SEPARATOR, __DIR__);
	$dirParts = array_splice($dirParts, 0, -4);
	$filePath = implode(DIRECTORY_SEPARATOR, $dirParts);
	$foundWpConfig = file_exists($filePath . '/wp-config.php');

	if (!$foundWpConfig)
	{
		$dirParts = array_splice($dirParts, 0, -1);
		$altFilePath = implode(DIRECTORY_SEPARATOR, $dirParts);
		$foundWpConfig = file_exists($altFilePath . '/wp-config.php');
	}

	if (!$foundWpConfig)
	{
		$dirParts = array();

		if (isset($_SERVER['SCRIPT_FILENAME']))
		{
			$scriptFilename = $_SERVER['SCRIPT_FILENAME'];

			if (substr(PHP_OS, 0, 3) == 'WIN')
			{
				$scriptFilename = str_replace('\\', '/', $scriptFilename);

				if (substr($scriptFilename, 0, 2) == '//')
				{
					$scriptFilename = '\\' . substr($scriptFilename, 2);
				}
			}

			$dirParts = explode('/', $_SERVER['SCRIPT_FILENAME']);
		}

		if (count($dirParts) > 5)
		{
			$dirParts = array_splice($dirParts, 0, -5);
			$filePath = implode(DIRECTORY_SEPARATOR, $dirParts);

			$foundWpConfig = file_exists($filePath . '/wp-config.php');

			if (!$foundWpConfig)
			{
				$dirParts = array_splice($dirParts, 0, -1);
				$altFilePath = implode(DIRECTORY_SEPARATOR, $dirParts);
				$foundWpConfig = file_exists($altFilePath . '/wp-config.php');
			}
		}
	}

	$oracle = new \Solo\Pythia\Oracle\Wordpress($filePath);

	if (!$oracle->isRecognised())
	{
		$filePath = realpath($filePath . '/..');
		$oracle = new \Solo\Pythia\Oracle\Wordpress($filePath);
	}

	if (!$oracle->isRecognised())
	{
		$curDir = __DIR__;
		echo <<< ENDTEXT
ERROR: Could not find wp-config.php

Technical information
--
integration.php directory	$curDir
filePath					$filePath
isRecognised				false
--

ENDTEXT;
		exit(1);
	}

	define('ABSPATH', $filePath);

	if (!defined('AKEEBA_SOLOWP_PATH'))
	{
		define('AKEEBA_SOLOWP_PATH', ABSPATH . '/wp-content/plugins/akeebabackupwp');
	}

	$dbInfo = $oracle->getDbInformation();

	define('DB_NAME', $dbInfo['name']);
	define('DB_USER', $dbInfo['username']);
	define('DB_PASSWORD', $dbInfo['password']);
	define('DB_HOST', $dbInfo['host']);

	global $table_prefix;
	$table_prefix = $dbInfo['prefix'];
}

// Load the platform defines
require_once __DIR__ . '/defines.php';

// Add our app to the autoloader
Awf\Autoloader\Autoloader::getInstance()->addMap('Solo\\', array(
	__DIR__ . '/Solo',
	APATH_BASE . '/Solo'
));

// Should I enable debug?
if (defined('AKEEBADEBUG'))
{
	error_reporting(E_ALL | E_NOTICE | E_DEPRECATED);
	ini_set('display_errors', 1);
}

// Include the Akeeba Engine and ALICE factories
define('AKEEBAENGINE', 1);
$factoryPath = $akeebaBackupWpRoot . 'Solo/engine/Factory.php';
$alicePath = $akeebaBackupWpRoot . 'Solo/alice/factory.php';

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

if(file_exists($alicePath))
{
	require_once $alicePath;
}

Platform::addPlatform('Wordpress', __DIR__ . '/Platform/Wordpress');
Platform::getInstance()->load_version_defines();
Platform::getInstance()->apply_quirk_definitions();

try
{
	// Create objects
	$container = new \Solo\Container(array(
		'appConfig'			=> function (\Awf\Container\Container $c)
			{
				return new \Solo\Application\AppConfig($c);
			},
		'userManager'		=> function (\Awf\Container\Container $c)
			{
				return new \Solo\Application\UserManager($c);
			},
		'input'				=> function (\Awf\Container\Container $c)
			{
				// WordPress is always escaping the input. WTF!
				// See http://stackoverflow.com/questions/8949768/with-magic-quotes-disabled-why-does-php-wordpress-continue-to-auto-escape-my

				global $_REAL_REQUEST;

				if (isset($_REAL_REQUEST))
				{
					return new \Awf\Input\Input($_REAL_REQUEST, array('magicQuotesWorkaround' => true));
				}
				elseif (defined('WPINC'))
				{
					$fakeRequest   = array_map('stripslashes_deep', $_REQUEST);

					return new \Awf\Input\Input($fakeRequest, array('magicQuotesWorkaround' => true));
				}
				else
				{
					return new \Awf\Input\Input();
				}
			},
		'application_name'	=> 'Solo',
		'filesystemBase' => AKEEBA_SOLOWP_PATH . '/app',
		'updateStreamURL' => 'http://cdn.akeebabackup.com/updates/backupwpcore.ini',
		'changelogPath'	=> AKEEBA_SOLOWP_PATH . 'CHANGELOG.php',
	));

	$downloadId = $container->appConfig->get('options.update_dlid', '');
	$hasPro = AKEEBABACKUP_PRO ? true : !empty($downloadId);
	unset($downloadId);
	if ($hasPro)
	{
		$container['updateStreamURL'] = 'http://cdn.akeebabackup.com/updates/backupwppro.ini';
	}
	unset($hasPro);
}
catch (Exception $exc)
{
	$filename = null;

	if (isset($application))
	{
		if ($application instanceof \Awf\Application\Application)
		{
			$template = $application->getTemplate();

			if (file_exists(APATH_THEMES . '/' . $template . '/error.php'))
			{
				$filename = APATH_THEMES . '/' . $template . '/error.php';
			}
		}
	}

	if (is_null($filename))
	{
		die($exc->getMessage());
	}

	include $filename;
}