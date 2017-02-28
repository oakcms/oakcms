<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2006-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 *
 */

namespace Akeeba\Engine\Scan;

// Protection against direct access
defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Factory;

/* Windows system detection */
if (!defined('_AKEEBA_IS_WINDOWS'))
{
	if (function_exists('php_uname'))
	{
		define('_AKEEBA_IS_WINDOWS', stristr(php_uname(), 'windows'));
	}
	else
	{
		define('_AKEEBA_IS_WINDOWS', DIRECTORY_SEPARATOR == '\\');
	}
}

/**
 * A filesystem scanner which uses opendir() and is smart enough to make large directories
 * be scanned inside a step of their own.
 *
 * The idea is that if it's not the first operation of this step and the number of contained
 * directories AND files is more than double the number of allowed files per fragment, we should
 * break the step immediately.
 *
 */
class Smart extends Base
{
	public function &getFiles($folder, &$position)
	{
		$registry = Factory::getConfiguration();
		// Was the breakflag set BEFORE starting? -- This workaround is required due to PHP5 defaulting to assigning variables by reference
		$breakflag_before_process = $registry->get('volatile.breakflag', false);

		// Reset break flag before continuing
		$breakflag = false;

		// Initialize variables
		$arr = array();
		$false = false;

		if (!@is_dir($folder) && !@is_dir($folder . '/'))
		{
			return $false;
		}

		$counter = 0;
		$registry = Factory::getConfiguration();
		$maxCounter = $registry->get('engine.scan.smart.large_dir_threshold', 100);

		$allowBreakflag = ($registry->get('volatile.operation_counter', 0) != 0) && !$breakflag_before_process;

		if (!@is_dir($folder))
		{
			$this->setWarning('Unreadable directory ' . $folder);

			return $false;
		}

		if (!@is_readable($folder))
		{
			$this->setWarning('Unreadable directory ' . $folder);

			return $false;
		}

		$di = new \DirectoryIterator($folder);
		$ds = ($folder == '') || ($folder == '/') || (@substr($folder, -1) == '/') || (@substr($folder, -1) == DIRECTORY_SEPARATOR) ? '' : DIRECTORY_SEPARATOR;

		/** @var \DirectoryIterator $file */
		foreach ($di as $file)
		{
			if ($file->isDot())
			{
				continue;
			}

			if ($breakflag)
			{
				break;
			}

			if ($file->isDir())
			{
				continue;
			}

			$dir = $folder . $ds . $file->getFilename();
			$data = $dir;

			if (_AKEEBA_IS_WINDOWS)
			{
				$data = Factory::getFilesystemTools()->TranslateWinPath($dir);
			}

			if ($data)
			{
				$arr[] = $data;
			}

			$counter++;

			if ($counter >= $maxCounter)
			{
				$breakflag = $allowBreakflag;
			}
		}

		// Save break flag status
		$registry->set('volatile.breakflag', $breakflag);

		return $arr;
	}

	public function &getFolders($folder, &$position)
	{
		// Was the breakflag set BEFORE starting? -- This workaround is required due to PHP5 defaulting to assigning variables by reference
		$registry = Factory::getConfiguration();
		$breakflag_before_process = $registry->get('volatile.breakflag', false);

		// Reset break flag before continuing
		$breakflag = false;

		// Initialize variables
		$arr = array();
		$false = false;

		if (!is_dir($folder) && !is_dir($folder . '/'))
		{
			return $false;
		}

		$counter = 0;
		$registry = Factory::getConfiguration();
		$maxCounter = $registry->get('engine.scan.smart.large_dir_threshold', 100);

		$allowBreakflag = ($registry->get('volatile.operation_counter', 0) != 0) && !$breakflag_before_process;

		if (!@is_readable($folder))
		{
			$this->setWarning('Unreadable directory ' . $folder);

			return $false;
		}

		$di = new \DirectoryIterator($folder);
		$ds = ($folder == '') || ($folder == '/') || (@substr($folder, -1) == '/') || (@substr($folder, -1) == DIRECTORY_SEPARATOR) ? '' : DIRECTORY_SEPARATOR;

		/** @var \DirectoryIterator $file */
		foreach ($di as $file)
		{
			if ($breakflag)
			{
				break;
			}

			if ($file->isDot())
			{
				continue;
			}

			if (!$file->isDir())
			{
				continue;
			}

			$dir = $folder . $ds . $file->getFilename();
			$data = $dir;

			if (_AKEEBA_IS_WINDOWS)
			{
				$data = Factory::getFilesystemTools()->TranslateWinPath($dir);
			}

			if ($data)
			{
				$arr[] = $data;
			}

			$counter++;

			if ($counter >= $maxCounter)
			{
				$breakflag = $allowBreakflag;
			}
		}

		// Save break flag status
		$registry->set('volatile.breakflag', $breakflag);

		return $arr;
	}
}
