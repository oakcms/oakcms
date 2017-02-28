<?php
/**
 * @package        awf-miniblog
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 *
 * This is the AWF integration file for the Joomla! 2.x / 3.x CMS. It allows you to run an AWF application as a Joomla!
 * component. You need to provide the following variables before including this script:
 *
 * @var string $appName The application name, e.g. Foobar for com_foobar. It's also the name of your AWF Application's
 *                      namespace. The admin side in this case will have the namespace FoobarAdmin and component name
 *                      com_foobar
 * @var array $containerOverrides Any variables you want to push to the DI Container
 */

use Awf\Session;

/**
 * Make sure we are being called from Joomla!
 */
defined('_JEXEC') or die;

// Makes sure we have PHP 5.3.3 or later
if (version_compare(PHP_VERSION, '5.3.3', 'lt'))
{
	echo sprintf('This component requires PHP 5.3.3 or later but your server only has PHP %s.', PHP_VERSION);
}

// Include the autoloader
if (false == include_once JPATH_LIBRARIES . '/awf/Autoloader/Autoloader.php')
{
	echo 'ERROR: Autoloader not found' . PHP_EOL;

	exit(1);
}

// Add our app to the autoloader, if it's not already set
$componentName = 'com_' . strtolower($appName);
$prefixes = Awf\Autoloader\Autoloader::getInstance()->getPrefixes();
if (!array_key_exists($appName . '\\', $prefixes))
{
	\Awf\Autoloader\Autoloader::getInstance()
		->addMap($appName . '\\', JPATH_SITE . '/components/' . $componentName)
		->addMap($appName . 'Admin\\', JPATH_ADMINISTRATOR . '/components/' . $componentName)
		->addMap($appName . '\\', JPATH_SITE . '/components/' . $componentName . '/' . $appName)
		->addMap($appName . 'Admin\\', JPATH_ADMINISTRATOR . '/components/' . $componentName . '/' . $appName);
}

// Load Joomla!-specific translation files
\Awf\Platform\Joomla\Helper\Helper::loadTranslations($componentName);

// Find the name of the DI container class suitable for this component
$appName = \Awf\Platform\Joomla\Helper\Helper::isBackend() ? ($appName . 'Admin') : $appName;
$containerClass = "\\$appName\\Container\\Container";

if (!class_exists($containerClass, true))
{
	$containerClass = '\Awf\Platform\Joomla\Container\Container';
}

if (!isset($containerOverrides))
{
	$containerOverrides = array();
}

if (!isset($containerOverrides['application_name']))
{
	$containerOverrides['application_name'] = $appName;
}

// Try to create a new DI container
try
{
	$container = new $containerClass($containerOverrides);
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

// Finally, unset the temporary variables polluting your namespace
unset($prefixes);
unset($appName);
unset($containerClass);
unset($containerOverrides);
unset($componentName);