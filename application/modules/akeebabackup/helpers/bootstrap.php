<?php
/**
 * @package		akeebabackupwp
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

// Bootstrap file for Akeeba Solo for WordPress
use Awf\Autoloader\Autoloader;
use Awf\Session;

/**
 * Make sure we are being called from WordPress itself
 */
defined('WPINC') or die;

defined('AKEEBASOLO') or define('AKEEBASOLO', 1);

// A trick to prevent raw views from rendering the entire WP back-end interface
if (defined('AKEEBA_SOLOWP_OBFLAG'))
{
	@ob_get_clean();
}

require_once 'integration.php';

try
{
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

	// Persist messages if they exist.
	if (count($application->messageQueue))
	{
		$application->getContainer()->segment->setFlash('application_queue', $this->messageQueue);
	}

	$application->getContainer()->session->commit();

	if (defined('AKEEBA_SOLOWP_OBFLAG'))
	{
		@ob_start();
	}
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