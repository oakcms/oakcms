<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2006-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 *
 */

namespace Akeeba\Engine\Filter;

// Protection against direct access
defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;

/**
 * Incremental file filter
 *
 * It will only backup files which are newer than the last backup taken with this profile
 */
class Incremental extends Base
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
		static $filter_switch = null;
		static $last_backup = null;

		if (is_null($filter_switch))
		{
			$config = Factory::getConfiguration();
			$filter_switch = Factory::getEngineParamsProvider()->getScriptingParameter('filter.incremental', 0);
			$filter_switch = ($filter_switch == 1);

			$last_backup = $config->get('volatile.filter.last_backup', null);

			if (is_null($last_backup) && $filter_switch)
			{
				// Get a list of backups on this profile
				$backups = Platform::getInstance()->get_statistics_list(array(
					   'filters' => array(
						   array(
							   'field' => 'profile_id',
							   'value' => Platform::getInstance()->get_active_profile())
					   )
				  ));

				// Find this backup's ID
				$model = Factory::getStatistics();
				$id = $model->getId();

				if (is_null($id))
				{
					$id = -1;
				}

				// Initialise
				$last_backup = time();
				$now = $last_backup;

				// Find the last time a successful backup with this profile was made
				if (count($backups))
				{
					foreach ($backups as $backup)
					{
						// Skip the current backup
						if ($backup['id'] == $id)
						{
							continue;
						}

						// Skip non-complete backups
						if ($backup['status'] != 'complete')
						{
							continue;
						}

						$tzUTC = new \DateTimeZone('UTC');
						$dateTime = new \DateTime($backup['backupstart'], $tzUTC);
						$backuptime = $dateTime->getTimestamp();

						$last_backup = $backuptime;
						break;
					}
				}

				if ($last_backup == $now)
				{
					// No suitable backup found; disable this filter
					$config->set('volatile.scripting.incfile.filter.incremental', 0);
					$filter_switch = false;
				}
				else
				{
					// Cache the last backup timestamp
					$config->set('volatile.filter.last_backup', $last_backup);
				}
			}
		}

		if (!$filter_switch)
		{
			return false;
		}

		// Get the filesystem path for $root
		$config = Factory::getConfiguration();
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

		// Compare it with the last backup timestamp and exclude if it's older than the last backup
		if ($timestamp <= $last_backup)
		{
			//Factory::getLog()->log(LogLevel::DEBUG, "Excluding $filename due to incremental backup restrictions");
			return true;
		}

		// No match? Just include the file!
		return false;
	}

}