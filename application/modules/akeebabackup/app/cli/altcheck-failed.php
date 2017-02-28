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
		$profile = $this->getContainer()->input->get('profile', 1, 'int');

		$debugmessage = '';

		if ($this->getContainer()->input->get('debug', -1, 'int') != -1)
		{
			if (!defined('AKEEBADEBUG'))
			{
				define('AKEEBADEBUG', 1);
			}

			$debugmessage = "*** DEBUG MODE ENABLED ***\n";
		}

		$version = AKEEBABACKUP_VERSION;
		$date = AKEEBABACKUP_DATE;
		$start_backup = time();

		$phpversion = PHP_VERSION;
		$phpenvironment = PHP_SAPI;

		if ($this->getContainer()->input->get('quiet', -1, 'int') == -1)
		{
			$year = gmdate('Y');
			echo <<<ENDBLOCK
Akeeba Solo Alternate CLI Backup Script version $version ($date)
Copyright (C) 2014-$year Nicholas K. Dionysopoulos / Akeeba Ltd
-------------------------------------------------------------------------------
Akeeba Solo is Free Software, distributed under the terms of the GNU General
Public License version 3 or, at your option, any later version.
This program comes with ABSOLUTELY NO WARRANTY as per sections 15 & 16 of the
license. See http://www.gnu.org/licenses/gpl-3.0.html for details.
-------------------------------------------------------------------------------
You are using PHP $phpversion ($phpenvironment)
$debugmessage

ENDBLOCK;
		}

		// Log some paths
		if ($this->getContainer()->input->get('quiet', -1, 'int') == -1)
		{
			echo "Site paths determined by this script:\n";
			echo "APATH_BASE : " . APATH_BASE . "\n";
		}

		$startup_check = true;

		$url = Platform::getInstance()->get_platform_configuration_option('siteurl', '');
		if (empty($url))
		{
			echo <<<ENDTEXT
ERROR:
	This script could not detect your Akeeba Solo installation's URL. Please
	visit Akeeba Solo's main page at least once before running this script.
	When you do that, Akeeba Solo will record the URL to itself and make it
	available to this script.

ENDTEXT;
			$startup_check = false;
		}

		// Get the front-end backup settings
		$frontend_enabled = Platform::getInstance()->get_platform_configuration_option('frontend_enable', '');
		$secret = Platform::getInstance()->get_platform_configuration_option('frontend_secret_word', '');

		if (!$frontend_enabled)
		{
			echo <<<ENDTEXT
ERROR:
	Your Akeeba Solo installation's front-end backup feature is currently
	disabled. Please log in to Akeeba Solo, click on the System Configuration
	icon in the system management pane towards the bottom of the page and
	enable the front-end backup feature. Do not forget to also set a Secret
	Word!

ENDTEXT;
			$startup_check = false;
		}
		elseif (empty($secret))
		{
			echo <<<ENDTEXT
ERROR:
	You have enabled the front-end backup feature, but you forgot to set a
	Secret Word. This script can not continue with an empty Secret Word.
	Please log in to Akeeba Solo, click on the System Configuration
	icon in the system management pane towards the bottom of the page and
	set a Secret Word.

ENDTEXT;
			$startup_check = false;
		}

		// Detect cURL or fopen URL
		$method = null;
		if (function_exists('curl_init'))
		{
			$method = 'curl';
		}
		elseif (function_exists('fsockopen'))
		{
			$method = 'fsockopen';
		}

		if (empty($method))
		{
			if (function_exists('ini_get'))
			{
				if (ini_get('allow_url_fopen'))
				{
					$method = 'fopen';
				}
			}
		}

		$overridemethod = $this->getContainer()->input->get('method', '', 'cmd');

		if (!empty($overridemethod))
		{
			$method = $overridemethod;
		}

		if (empty($method))
		{
			echo <<<ENDTEXT
ERROR:
	Could not find any supported method for running the front-end backup
	feature of Akeeba Solo. Please check with your host that at least
	one of the following features are supported in your PHP configuration:

	1. The cURL extension
	2. The fsockopen() function
	3. The fopen() URL wrappers, i.e. allow_url_fopen is enabled

	If neither method is available you will not be able to run a backup using
	this CRON helper script.

ENDTEXT;
			$startup_check = false;
		}

		if (!$startup_check)
		{
			echo "\n\nCHECK FOR FAILURES ABORTED DUE TO CONFIGURATION ERRORS\n\n";
			$this->close(255);
		}

		// Perform the backup
		$url = rtrim($url, '/');
		$secret = urlencode($secret);
		$url .= "/index.php?view=check&key={$secret}";

		$timestamp = date('Y-m-d H:i:s');

		$result = $this->fetchURL($url, $method);

		echo "[{$timestamp}] Got $result\n";

		if (empty($result) || ($result === false))
		{
			echo "[{$timestamp}] No message received\n";
			echo <<<ENDTEXT
ERROR:
Your check for failures attempt has timed out, or a fatal PHP error has occurred.

ENDTEXT;
		}
		elseif (strpos($result, '200 ') !== false)
		{
			echo "[{$timestamp}] Checks finalization message received\n";
			echo <<<ENDTEXT

Checks are finished successfully.

ENDTEXT;
		}
		elseif (strpos($result, '500 ') !== false)
		{
			// Backup error
			echo "[{$timestamp}] Error signal received\n";
			echo <<<ENDTEXT
ERROR:
A backup error has occurred. The server's response was:

$result

ENDTEXT;
		}
		elseif (strpos($result, '403 ') !== false)
		{
			// This should never happen: invalid authentication or front-end backup disabled
			echo "[{$timestamp}] Connection denied (403) message received\n";
			echo <<<ENDTEXT
ERROR:
The server denied the connection. Please make sure that the front-end
backup feature is enabled and a valid secret word is in place.

Server response: $result

Backup failed.

ENDTEXT;
		}
		else
		{
			// Unknown result?!
			echo "[{$timestamp}] Could not parse the server response.\n";
			echo <<<ENDTEXT
ERROR:
We could not understand the server's response. Most likely an error
has occurred. The server's response was:

$result

If you do not see "200 OK" at the end of this output, checks failed.

ENDTEXT;
		}
	}

	/**
	 * Fetches a remote URL using curl, fsockopen or fopen
	 *
	 * @param  string $url    The remote URL to fetch
	 * @param  string $method The method to use: curl, fsockopen or fopen (optional)
	 *
	 * @return string The contents of the URL which was fetched
	 */
	private function fetchURL($url, $method = 'curl')
	{
		switch ($method)
		{
			default:
			case 'curl':
				$ch = curl_init($url);
				$cacertPath = APATH_BASE . '/Solo/engine/assets/cacert.pem';

				if (file_exists($cacertPath))
				{
					@curl_setopt($ch, CURLOPT_CAINFO, $cacertPath);
				}
				@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				@curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
				@curl_setopt($ch, CURLOPT_HEADER, false);
				@curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				@curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				@curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 180);
				@curl_setopt($ch, CURLOPT_TIMEOUT, 180);
				$result = curl_exec($ch);
				curl_close($ch);

				return $result;
				break;

			case 'fsockopen':
				$pos = strpos($url, '://');
				$protocol = strtolower(substr($url, 0, $pos));
				$req = substr($url, $pos + 3);
				$pos = strpos($req, '/');
				if ($pos === false)
				{
					$pos = strlen($req);
				}
				$host = substr($req, 0, $pos);

				if (strpos($host, ':') !== false)
				{
					list($host, $port) = explode(':', $host);
				}
				else
				{
					$port = ($protocol == 'https') ? 443 : 80;
				}

				$uri = substr($req, $pos);
				if ($uri == '')
				{
					$uri = '/';
				}

				$crlf = "\r\n";
				$req = 'GET ' . $uri . ' HTTP/1.0' . $crlf
					. 'Host: ' . $host . $crlf
					. $crlf;

				$fp = fsockopen(($protocol == 'https' ? 'ssl://' : '') . $host, $port);
				fwrite($fp, $req);
				$response = '';
				while (is_resource($fp) && $fp && !feof($fp))
				{
					$response .= fread($fp, 1024);
				}
				fclose($fp);

				// split header and body
				$pos = strpos($response, $crlf . $crlf);
				if ($pos === false)
				{
					return ($response);
				}
				$header = substr($response, 0, $pos);
				$body = substr($response, $pos + 2 * strlen($crlf));

				// parse headers
				$headers = array();
				$lines = explode($crlf, $header);
				foreach ($lines as $line)
				{
					if (($pos = strpos($line, ':')) !== false)
					{
						$headers[strtolower(trim(substr($line, 0, $pos)))] = trim(substr($line, $pos + 1));
					}
				}

				//redirection?
				if (isset($headers['location']))
				{
					return $this->fetchURL($headers['location'], $method);
				}
				else
				{
					return ($body);
				}

				break;

			case 'fopen':
				$opts = array(
					'http' => array(
						'method' => "GET",
						'header' => "Accept-language: en\r\n"
					)
				);

				$context = stream_context_create($opts);
				$result = @file_get_contents($url, false, $context);
				break;
		}

		return $result;
	}
}

if (!isset($container))
{
	$container = new \Solo\Container();
}
$app = new BackupApplication($container);
$app->initialise()->execute();