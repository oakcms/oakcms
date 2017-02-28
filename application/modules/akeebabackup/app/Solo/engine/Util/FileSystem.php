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

namespace Akeeba\Engine\Util;

// Protection against direct access
defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;

/**
 * Utility functions related to filesystem objects, e.g. path translation
 */
class FileSystem
{
	/** @var bool Are we running under Windows? */
	private $isWindows = false;

	/**
	 * Initialise the object
	 */
	public function __construct()
	{
		$this->isWindows = (DIRECTORY_SEPARATOR == '\\');
	}

	/**
	 * Makes a Windows path more UNIX-like, by turning backslashes to forward slashes.
	 * It takes into account UNC paths, e.g. \\myserver\some\folder becomes
	 * \\myserver/some/folder.
	 *
	 * This function will also fix paths with multiple slashes, e.g. convert /var//www////html to /var/www/html
	 *
	 * @param   string  $p_path  The path to transform
	 *
	 * @return  string
	 */
	public function TranslateWinPath($p_path)
	{
		$is_unc = false;

		if ($this->isWindows)
		{
			// Is this a UNC path?
			$is_unc = (substr($p_path, 0, 2) == '\\\\') || (substr($p_path, 0, 2) == '//');

			// Change potential windows directory separator
			if ((strpos($p_path, '\\') > 0) || (substr($p_path, 0, 1) == '\\'))
			{
				$p_path = strtr($p_path, '\\', '/');
			}
		}

		// Remove multiple slashes
		$p_path = str_replace('///', '/', $p_path);
		$p_path = str_replace('//', '/', $p_path);

		// Fix UNC paths
		if ($is_unc)
		{
			$p_path = '//' . ltrim($p_path, '/');
		}

		return $p_path;
	}

	/**
	 * Removes trailing slash or backslash from a pathname
	 *
	 * @param   string  $path  The path to treat
	 *
	 * @return  string  The path without the trailing slash/backslash
	 */
	public function TrimTrailingSlash($path)
	{
		$newpath = $path;

		if (substr($path, strlen($path) - 1, 1) == '\\')
		{
			$newpath = substr($path, 0, strlen($path) - 1);
		}

		if (substr($path, strlen($path) - 1, 1) == '/')
		{
			$newpath = substr($path, 0, strlen($path) - 1);
		}

		return $newpath;
	}

	/**
	 * Returns an array with the archive name variables and their values. This is used to replace variables in archive
	 * and directory names, etc.
	 *
	 * If there is a non-empty configuration value called volatile.core.archivenamevars with a serialised array it will
	 * be unserialised and used. Otherwise the name variables will be calculated on-the-fly.
	 *
	 * IMPORTANT: These variables do NOT include paths such as [SITEROOT]
	 *
	 * @return  array
	 */
	public function get_archive_name_variables()
	{
		$variables = array();

		$registry = Factory::getConfiguration();
		$serialized = $registry->get('volatile.core.archivenamevars', null);

		if (!empty($serialized))
		{
			$variables = @unserialize($serialized);
		}

		if (empty($variables) || !is_array($variables))
		{
			$host = Platform::getInstance()->get_host();
			$version = defined('AKEEBA_VERSION') ? AKEEBA_VERSION : 'svn';
			$version = defined('AKEEBABACKUP_VERSION') ? AKEEBABACKUP_VERSION : $version;
			$platformVars = Platform::getInstance()->getPlatformVersion();

			$siteName = Platform::getInstance()->get_site_name();
			$siteName = htmlentities(utf8_decode($siteName));
			$siteName = preg_replace(
				array('/&szlig;/', '/&(..)lig;/', '/&([aouAOU])uml;/', '/&(.)[^;]*;/'),
				array('ss', "$1", "$1" . 'e', "$1"),
				$siteName);
			$siteName = trim(strtolower($siteName));
			$siteName = preg_replace(array('/\s+/', '/[^A-Za-z0-9\-]/'), array('-', ''), $siteName);

			if (strlen($siteName) > 50)
			{
				$siteName = substr($siteName, 0, 50);
			}

			$variables = array(
				'[DATE]'             => Platform::getInstance()->get_local_timestamp("Ymd"),
				'[YEAR]'             => Platform::getInstance()->get_local_timestamp("Y"),
				'[MONTH]'            => Platform::getInstance()->get_local_timestamp("m"),
				'[DAY]'              => Platform::getInstance()->get_local_timestamp("d"),
				'[TIME]'             => Platform::getInstance()->get_local_timestamp("His"),
				'[WEEK]'             => Platform::getInstance()->get_local_timestamp("W"),
				'[WEEKDAY]'          => Platform::getInstance()->get_local_timestamp("l"),
				'[HOST]'             => empty($host) ? 'unknown_host' : $host,
				'[RANDOM]'           => md5(microtime()),
				'[VERSION]'          => $version,
				'[PLATFORM_NAME]'    => $platformVars['name'],
				'[PLATFORM_VERSION]' => $platformVars['version'],
				'[SITENAME]'         => $siteName
			);
		}

		return $variables;
	}

	/**
	 * Expands the archive name variables in $source. For example "[DATE]-foobar" would be expanded to something
	 * like "141101-foobar". IMPORTANT: These variables do NOT include paths.
	 *
	 * @param   string  $source  The input string, possibly containing variables in the form of [VARIABLE]
	 *
	 * @return  string  The expanded string
	 */
	public function replace_archive_name_variables($source)
	{
		$tagReplacements = $this->get_archive_name_variables();

		return str_replace(array_keys($tagReplacements), array_values($tagReplacements), $source);
	}

	/**
	 * Expand the platform-specific stock directories variables in the input string. For example "[SITEROOT]/foobar"
	 * would be expanded to something like "/var/www/html/mysite/foobar"
	 *
	 * @param   string  $folder                The input string to expand
	 * @param   bool    $translate_win_dirs    Should I translate Windows path separators to UNIX path separators? (default: false)
	 * @param   bool    $trim_trailing_slash   Should I remove the trailing slash (default: false)
	 *
	 * @return  string  The expanded string
	 */
	function translateStockDirs($folder, $translate_win_dirs = false, $trim_trailing_slash = false)
	{
		static $stock_dirs;

		if (empty($stock_dirs))
		{
			$stock_dirs = Platform::getInstance()->get_stock_directories();
		}

		$temp = $folder;

		foreach ($stock_dirs as $find => $replace)
		{
			$temp = str_replace($find, $replace, $temp);
		}

		if ($translate_win_dirs)
		{
			$temp = $this->TranslateWinPath($temp);
		}

		if ($trim_trailing_slash)
		{
			$temp = $this->TrimTrailingSlash($temp);
		}

		return $temp;
	}
}

