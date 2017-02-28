<?php
/**
 * @package		awf
 * @copyright	2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license		GNU GPL version 3 or later
 */

namespace Awf\Platform\Joomla\Helper;

use JFactory;

/**
 * Helper methods for the Joomla! platform
 */
abstract class Helper
{
	/** @var bool Are we running under CLI? */
	protected static $isCli = null;

	/** @var bool Are we running inside the administrator back-end? */
	protected static $isBackend = null;

	/** @var bool Are we running inside the public front-end? */
	protected static $isFrontend = null;

	/**
	 * Is this the administrative back-end section of the site?
	 *
	 * @return  boolean
	 */
	public static function isBackend()
	{
		if (is_null(self::$isBackend))
		{
			self::detectApplicationSide();
		}

		return self::$isBackend;
	}

	/**
	 * Is this the public front-end section of the site?
	 *
	 * @return  boolean
	 */
	public static function isFrontend()
	{
		if (is_null(self::$isFrontend))
		{
			self::detectApplicationSide();
		}

		return self::$isFrontend;
	}

	/**
	 * Is this a component running inside a CLI application?
	 *
	 * @return  boolean
	 */
	public static function isCli()
	{
		if (is_null(self::$isCli))
		{
			self::detectApplicationSide();
		}

		return self::$isCli;
	}

	/**
	 * Detects if we are in front-end, back-end or CLI
	 *
	 * @return  void
	 */
	protected static function detectApplicationSide()
	{
		try
		{
			if (is_null(JFactory::$application))
			{
				$isCLI = true;
			}
			else
			{
				$app = JFactory::getApplication();
				$isCLI = $app instanceof \JException || $app instanceof \JApplicationCli;
			}
		}
		catch (\Exception $e)
		{
			$isCLI = true;
		}

		if ($isCLI)
		{
			$isAdmin = false;
		}
		else
		{
			$isAdmin = !JFactory::$application ? false : JFactory::getApplication()->isAdmin();
		}

		self::$isBackend = $isAdmin && !$isCLI;
		self::$isFrontend = !$isAdmin && !$isCLI;
		self::$isCli = !$isAdmin && $isCLI;
	}

	/**
	 * Load plugins of a specific type.
	 *
	 * @param string $type      The type of the plugins to be loaded
	 * @param bool   $loadInCli Should I also try to load plugins in CLI mode (default: false)
	 *
	 * @return void
	 */
	public static function importPlugin($type, $loadInCli = false)
	{
		if ($loadInCli || !self::isCli())
		{
			\JLoader::import('joomla.plugin.helper');
			\JPluginHelper::importPlugin($type);
		}
	}

	/**
	 * Execute plugins and fetch back an array with their return values.
	 *
	 * @param string $event     The event (trigger) name, e.g. onDoSomethingOrAnother
	 * @param array  $data      A hash array of data sent to the plugins as part of the trigger
	 * @param bool   $loadInCli Should I also try to run plugins in CLI mode (default: false)
	 *
	 * @return array A simple array containing the results of the plugins triggered
	 */
	public static function runPlugins($event, $data, $loadInCli = false)
	{
		if ($loadInCli || !self::isCli())
		{
			// IMPORTANT: DO NOT REPLACE THIS INSTANCE OF JDispatcher WITH ANYTHING ELSE. WE NEED JOOMLA!'S PLUGIN EVENT
			// DISPATCHER HERE, NOT OUR GENERIC EVENTS DISPATCHER
			if (version_compare(JVERSION, '3.0', 'ge'))
			{
				$dispatcher = \JEventDispatcher::getInstance();
			}
			else
			{
				$dispatcher = \JDispatcher::getInstance();
			}

			return $dispatcher->trigger($event, $data);
		}
		else
		{
			return array();
		}
	}

	/**
	 * Perform an ACL check.
	 *
	 * @param   string $action    The ACL privilege to check, e.g. core.edit
	 * @param   string $assetname The asset name to check, typically the component's name
	 *
	 * @return  boolean  True if the user is allowed this action
	 */
	public static function authorise($action, $assetname)
	{
		if (self::isCli())
		{
			return true;
		}

		return JFactory::getUser()->authorise($action, $assetname);
	}

	/**
	 * Throw an error in a platform-friendly manner
	 *
	 * @param int    $code    The error code
	 * @param string $message The error message
	 *
	 * @return \JError on Joomla! 2.5 (exception thrown on Joomla! 3+)
	 *
	 * @throws \Exception Thrown on Joomla! 3+
	 */
	public static function raiseError($code, $message)
	{
		if (version_compare(JVERSION, '3.0', 'ge'))
		{
			throw new \Exception($message, $code);
		}
		else
		{
			return \JError::raiseError($code, $message);
		}
	}

	/**
	 * Set the error Handling, if possible
	 *
	 * @param integer $level     PHP error level (E_ALL)
	 * @param string  $log_level What to do with the error (ignore, callback)
	 * @param array   $options   Options for the error handler
	 *
	 * @return  void
	 */
	public static function setErrorHandling($level, $log_level, $options = array())
	{
		if (version_compare(JVERSION, '3.0', 'lt'))
		{
			\JError::setErrorHandling($level, $log_level, $options);
		}
	}

	/**
	 * Return the absolute path to the application's template overrides
	 * directory for a specific component. We will use it to look for template
	 * files instead of the regular component directories. If the application
	 * does not have such a thing as template overrides return an empty string.
	 *
	 * @param string  $component The name of the component for which to fetch the overrides
	 * @param boolean $absolute  Should I return an absolute or relative path?
	 *
	 * @return string The path to the template overrides directory
	 */
	public static function getTemplateOverridePath($component, $absolute = true)
	{
		if (!self::isCli())
		{
			if ($absolute)
			{
				$path = JPATH_THEMES . '/';
			}
			else
			{
				$path = self::isBackend() ? 'administrator/templates/' : 'templates/';
			}

			if (substr($component, 0, 7) == 'media:/')
			{
				$directory = 'media/' . substr($component, 7);
			}
			else
			{
				$directory = 'html/' . $component;
			}

			$path .= JFactory::getApplication()->getTemplate() . '/' . $directory;
		}
		else
		{
			$path = '';
		}

		return $path;
	}

	/**
	 * Load the translation files for a given component.
	 *
	 * @param string $component The name of the component, e.g. com_example
	 *
	 * @return void
	 */
	public static function loadTranslations($component)
	{
		if (self::isBackend())
		{
			$paths = array(JPATH_ROOT, JPATH_ADMINISTRATOR);
		}
		else
		{
			$paths = array(JPATH_ADMINISTRATOR, JPATH_ROOT);
		}

		$jlang = JFactory::getLanguage();

		$jlang->load($component, $paths[0], 'en-GB', true);
		$jlang->load($component, $paths[0], null, true);
		$jlang->load($component, $paths[1], 'en-GB', true);
		$jlang->load($component, $paths[1], null, true);
	}
} 