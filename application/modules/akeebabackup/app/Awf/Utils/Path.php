<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

namespace Awf\Utils;

use Awf\Application\Application;

/**
 * Class Path
 *
 * Filesystem path helper
 *
 * @package Awf\Utils
 */
abstract class Path
{
	/**
	 * Function to strip additional / or \ in a path name.
	 *
	 * @param   string  $path  The path to clean.
	 * @param   string  $ds    Directory separator (optional).
	 *
	 * @return  string  The cleaned path.
	 */
	public static function clean($path, $ds = DIRECTORY_SEPARATOR)
	{
		$path = trim($path);

		if (empty($path))
		{
			$application = Application::getInstance();
			$path = $application->getContainer()->filesystemBase;
		}
		else
		{
			// Detect UNC paths
			$prefix = (($ds == '\\') && substr($path, 0, 2) == '\\\\') ? '\\' : '';

			// Remove double slashes and backslashes and convert all slashes and backslashes to DIRECTORY_SEPARATOR
			$path = preg_replace('#[/\\\\]+#', $ds, $path);

			// Reapply the UNC prefix if necessary
			if ($prefix)
			{
				$path = $prefix . $path;
			}
		}

		return $path;
	}

	/**
	 * Checks for snooping outside of the file system root.
	 *
	 * @param   string  $path  A file system path to check.
	 * @param   string  $ds    Directory separator (optional).
	 *
	 * @return  string  A cleaned version of the path or exit on error.
	 *
	 * @throws  \Exception  When malicious activity is detected
	 */
	public static function check($path, $ds = DIRECTORY_SEPARATOR)
	{
		if (strpos($path, '..') !== false)
		{
			// Don't translate
			throw new \Exception(__CLASS__ . '::check Use of relative paths not permitted', 20);
		}

		$rootPath = Application::getInstance()->getContainer()->filesystemBase;

		$path = self::clean($path);
		if (($rootPath != '') && strpos($path, self::clean($rootPath)) !== 0)
		{
			// Don't translate
			throw new \Exception(__CLASS__ . '::check Snooping out of bounds @ ' . $path, 20);
		}

		return $path;
	}

	/**
	 * Searches the directory paths for a given file.
	 *
	 * @param   mixed   $paths  An path string or array of path strings to search in
	 * @param   string  $file   The file name to look for.
	 *
	 * @return  mixed   The full path and file name for the target file, or boolean false if the file is not found in any of the paths.
	 */
	public static function find($paths, $file)
	{
		settype($paths, 'array'); //force to array

		// Start looping through the path set
		foreach ($paths as $path)
		{
			// Get the path to the file
			$fullname = $path . '/' . $file;

			// Is the path based on a stream?
			if (strpos($path, '://') === false)
			{
				// Not a stream, so do a realpath() to avoid directory
				// traversal attempts on the local file system.
				$path = realpath($path); // needed for substr() later
				$fullname = realpath($fullname);
			}

			// The substr() check added to make sure that the realpath()
			// results in a directory registered so that
			// non-registered directories are not accessible via directory
			// traversal attempts.
			if (file_exists($fullname) && substr($fullname, 0, strlen($path)) == $path)
			{
				return $fullname;
			}
		}

		// Could not find the file in the set of paths
		return false;
	}
}