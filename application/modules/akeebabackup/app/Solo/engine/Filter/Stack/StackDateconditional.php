<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2006-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 *
 */

namespace Akeeba\Engine\Filter\Stack;

use Akeeba\Engine\Factory;
use Akeeba\Engine\Filter\Base;

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * Date conditional filter
 *
 * It will only backup files modified after a specific date and time
 */
class StackDateconditional extends Base
{
	function __construct()
	{
		$this->object = 'file';
		$this->subtype = 'all';
		$this->method = 'api';

		if (Factory::getKettenrad()->getTag() == 'restorepoint')
		{
			$this->enabled = false;
		}
	}

	protected function is_excluded_by_api($test, $root)
	{
		static $from_datetime;

		$config = Factory::getConfiguration();

		if (is_null($from_datetime))
		{
			$user_setting = $config->get('core.filters.dateconditional.start');
			$from_datetime = strtotime($user_setting);
		}

		// Get the filesystem path for $root
		$fsroot = $config->get('volatile.filesystem.current_root', '');
		$ds = ($fsroot == '') || ($fsroot == '/') ? '' : DIRECTORY_SEPARATOR;
		$filename = $fsroot . $ds . $test;

		// Get the timestamp of the file
		$timestamp = @filemtime($filename);

		// If we could not get this information, include the file in the archive
		if ($timestamp === false)
		{
			return false;
		}

		// Compare it with the user-defined minimum timestamp and exclude if it's older than that
		if ($timestamp <= $from_datetime)
		{
			return true;
		}

		// No match? Just include the file!
		return false;
	}

}