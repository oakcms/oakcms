<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1-alpha.0.4
 */

use Awf\Autoloader\Autoloader;
use Awf\Session;


defined('AKEEBASOLO') or define('AKEEBASOLO', 1);

if (defined('AKEEBA_SOLOYII_OBFLAG'))
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

	if (defined('AKEEBA_SOLOYII_OBFLAG'))
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
