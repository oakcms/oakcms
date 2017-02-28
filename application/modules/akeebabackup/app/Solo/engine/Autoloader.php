<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 *
 * @copyright Copyright (c)2006-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 *
 */

namespace Akeeba\Engine;

// Protection against direct access
defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Platform;

/**
 * The main class autoloader for AkeebaEngine
 */
class Autoloader
{
	/**
	 * An instance of this autoloader
	 *
	 * @var   Autoloader
	 */
	public static $autoloader = null;

	/**
	 * The path to the Akeeba Engine root directory
	 *
	 * @var   string
	 */
	public static $enginePath = null;

	/**
	 * The directories where Akeeba Engine platforms are stored
	 *
	 * @var	  array
	 */
	public static $platformDirs = null;

	/**
	 * Initialise this autoloader
	 *
	 * @return  Autoloader
	 */
	public static function init()
	{
		if (self::$autoloader == null)
		{
			self::$autoloader = new self;
		}

		return self::$autoloader;
	}

	/**
	 * Public constructor. Registers the autoloader with PHP.
	 */
	public function __construct()
	{
		self::$enginePath = __DIR__;

		spl_autoload_register(array($this, 'autoload_akeeba_engine'));
		spl_autoload_register(array($this, 'autoload_psr3'));
	}

	/**
	 * The actual autoloader
	 *
	 * @param   string  $className  The name of the class to load
	 *
	 * @return  void
	 */
	public function autoload_akeeba_engine($className)
	{
		// Trim the trailing backslash
		$className = ltrim($className, '\\');

		// Make sure the class has an Akeeba\Engine prefix
		if (substr($className, 0, 13) != 'Akeeba\\Engine')
		{
			return;
		}

		// Remove the prefix and explode on backslashes
		$className = substr($className, 14);
		$class = explode('\\', $className);

		// Do we have a list of platform directories?
		if (is_null(self::$platformDirs) && class_exists('\\Akeeba\\Engine\\Platform', false))
		{
			self::$platformDirs = Platform::getPlatformDirectories();

			if (!is_array(self::$platformDirs))
			{
				self::$platformDirs = array();
			}
		}

		$rootPaths = array(self::$enginePath);

		if (is_array(self::$platformDirs))
		{
			$rootPaths = array_merge(
				self::$platformDirs, array(self::$enginePath)
			);
		}

		foreach ($rootPaths as $rootPath)
		{
			// First try finding in structured directory format (preferred)
			$path = $rootPath . '/' . implode('/', $class) . '.php';

			if (@file_exists($path))
			{
				include_once $path;
			}

			// Then try the duplicate last name structured directory format (not recommended)
			if (!class_exists($className, false))
			{
				reset($class);
				$lastPart = end($class);
				$path = $rootPath . '/' . implode('/', $class) . '/' . $lastPart . '.php';

				if (@file_exists($path))
				{
					include_once $path;
				}
			}
		}
	}

	/**
	 * An autoloader for our copy of PSR-3
	 *
	 * @param   string  $className  The name of the class to load
	 *
	 * @return  void
	 */
	public function autoload_psr3($className)
	{
		// Trim the trailing backslash
		$className = ltrim($className, '\\');

		// Make sure the class has an Akeeba\Engine prefix
		if (substr($className, 0, 7) != 'Psr\\Log')
		{
			return;
		}

		// Remove the prefix and explode on backslashes
		$className = substr($className, 7);
		$class = explode('\\', $className);

		$rootPath = self::$enginePath . '/Psr/Log';

		// First try finding in structured directory format (preferred)
		$path = $rootPath . '/' . implode('/', $class) . '.php';

		if (@file_exists($path))
		{
			include_once $path;
		}

		// Then try the duplicate last name structured directory format (not recommended)
		if (!class_exists($className, false))
		{
			reset($class);
			$lastPart = end($class);
			$path = $rootPath . '/' . implode('/', $class) . '/' . $lastPart . '.php';

			if (@file_exists($path))
			{
				include_once $path;
			}
		}
	}
}

// Register the Akeeba Engine autoloader
Autoloader::init();