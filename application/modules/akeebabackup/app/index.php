<?php
/**
 * @package     Solo
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

// Minimum PHP version check
$minimumPHP = '5.3.3';

if (version_compare(PHP_VERSION, $minimumPHP, 'lt'))
{
	require_once 'wrongphp.php';
	return;
}

unset($minimumPHP);

// Include dependencies

use Akeeba\Engine\Platform;
use Awf\Autoloader\Autoloader;
use Awf\Session;

// Include the autoloader
if (false == include __DIR__ . '/Awf/Autoloader/Autoloader.php')
{
	echo 'ERROR: Autoloader not found' . PHP_EOL;

	exit(1);
}

// Load the integration script
define('AKEEBASOLO', 1);
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

if (count($dirParts) > 2)
{
	$dirParts = array_splice($dirParts, 0, -1);
	$myDir = implode(DIRECTORY_SEPARATOR, $dirParts);
}

if (@file_exists(__DIR__ . '/../helpers/integration.php'))
{
	require_once __DIR__ . '/../helpers/integration.php';
}
elseif (@file_exists('../helpers/integration.php'))
{
	require_once '../helpers/integration.php';
}
elseif (@file_exists($myDir . '/helpers/integration.php'))
{
	require_once $myDir . '/helpers/integration.php';
}

// Load the platform defines
if (!defined('APATH_BASE'))
{
	require_once __DIR__ . '/defines.php';
}

// Should I enable debug?
if (defined('AKEEBADEBUG'))
{
	error_reporting(E_ALL | E_NOTICE | E_DEPRECATED);
	ini_set('display_errors', 1);
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
	require_once __DIR__ . '/Solo/engine/Factory.php';

	if(file_exists(__DIR__.'/Solo/alice/factory.php'))
	{
		require_once __DIR__ . '/Solo/alice/factory.php';
	}

	Platform::addPlatform('Solo', __DIR__ . '/Solo/Platform/Solo');
	Platform::getInstance()->load_version_defines();
	Platform::getInstance()->apply_quirk_definitions();
}

try
{
	// Create the container if it doesn't already exist
	if (!isset($container))
	{
		$container = new \Solo\Container(array(
			'application_name'	=> 'Solo'
		));
	}

	// Create the application
	$application = $container->application;

	// Initialise the application
	$application->initialise();

	// Route the URL: parses the URL through routing rules, replacing the data in the app's input
	$application->route();

	// Dispatch the application
	$application->dispatch();

	// Render the output
	$application->render();

	// Clean-up and shut down
	$application->close();
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

	// An uncaught application error occurred
	/**
	echo "<h1>Application Error</h1>\n";
	echo "<p>Please submit the following error message and trace in its entirety when requesting support</p>\n";
	echo "<div class=\"alert alert-danger\">" . get_class($exc) . ' &mdash; ' . $exc->getMessage() . "</div>\n";
	echo "<pre class=\"well\">\n";
	echo $exc->getTraceAsString();
	echo "</pre>\n";
	/**/

	include $filename;
}