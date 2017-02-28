<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model;

use Awf\Database\Driver;
use Awf\Mvc\Model;
use Awf\Pagination\Pagination;
use Awf\Text\Text;
use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;

class Manage extends Model
{
	/** @var   Pagination  The pagination model for this data */
	protected $pagination = null;

	public function __construct(\Awf\Container\Container $container = null)
	{
		parent::__construct($container);

		$limit = $this->getUserStateFromRequest('solo_limit', 'limit', 10, 'int');
		$limitStart = $this->getUserStateFromRequest('solo_manage_start', 'limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitStart', $limitStart);
	}

	/**
	 * Returns the same list as getStatisticsList(), but includes an extra field
	 * named 'meta' which categorises attempts based on their backup archive status
	 *
	 * @param   boolean $overrideLimits Should I override all list limits?
	 * @param   array   $filters        Filters to apply, see PlatformInterface::get_statistics_list
	 * @param   array   $order          Record ordering information (By and Ordering)
	 *
	 * @return  array  An array of backup attempt objects
	 */
	public function &getStatisticsListWithMeta($overrideLimits = false, $filters = null, $order = null)
	{
		$limitstart = $this->getState('limitstart');
		$limit = $this->getState('limit');

		if ($overrideLimits)
		{
			$limitstart = 0;
			$limit = 0;
			$filters = null;
		}

		$allStats = Platform::getInstance()->get_statistics_list(array(
																		 'limitstart' => $limitstart,
																		 'limit'      => $limit,
																		 'filters'    => $filters,
																		 'order'      => $order
																	));
		$valid = Platform::getInstance()->get_valid_backup_records();

		if (empty($valid))
		{
			$valid = array();
		}

		// This will hold the entries whose files are no longer present and are
		// not already marked as such in the database
		$updateNonExistent = array();

		$new_stats = array();

		if (!empty($allStats))
		{
			foreach ($allStats as $stat)
			{
				$total_size = 0;

				if (in_array($stat['id'], $valid))
				{
					$archives = Factory::getStatistics()->get_all_filenames($stat);
					$stat['meta'] = (count($archives) > 0) ? 'ok' : 'obsolete';

					if ($stat['meta'] == 'ok')
					{
						if ($stat['total_size'])
						{
							$total_size = $stat['total_size'];
						}
						else
						{
							$total_size = 0;

							foreach ($archives as $filename)
							{
								$total_size += @filesize($filename);
							}
						}

					}
					else
					{
						if ($stat['total_size'])
						{
							$total_size = $stat['total_size'];
						}

						if ($stat['filesexist'])
						{
							$updateNonExistent[] = $stat['id'];
						}

						// If there is a "remote_filename", the record is "remote", not "obsolete"
						if ($stat['remote_filename'])
						{
							$stat['meta'] = 'remote';
						}
					}

					$stat['size'] = $total_size;
				}
				else
				{
					switch ($stat['status'])
					{
						case 'run':
							$stat['meta'] = 'pending';
							break;

						case 'fail':
							$stat['meta'] = 'fail';
							break;

						default:
							if ($stat['remote_filename'])
							{
								// If there is a "remote_filename", the record is "remote", not "obsolete"
								$stat['meta'] = 'remote';
							}
							else
							{
								// Else, it's "obsolete"
								$stat['meta'] = 'obsolete';
							}
							break;
					}
				}
				$new_stats[] = $stat;
			}
		}

		// Update records found as not having files any more
		if (count($updateNonExistent))
		{
			Platform::getInstance()->invalidate_backup_records($updateNonExistent);
		}

		unset($valid);

		return $new_stats;
	}

	/**
	 * Delete the stats record whose ID is set in the model
	 *
	 * @return  boolean  True on success
	 *
	 * @throws  \RuntimeException
	 */
	public function delete()
	{
		$id = $this->getState('id', 0);

		if ((!is_numeric($id)) || ($id <= 0))
		{
			throw new \RuntimeException(Text::_('COM_AKEEBA_BUADMIN_ERROR_INVALIDID'), 500);
		}

		// Try to delete files
		$this->deleteFile();

		Platform::getInstance()->delete_statistics($id);

		return true;
	}

	/**
	 * Delete the backup file of the stats record whose ID is set in the model
	 *
	 * @return  boolean  True on success
	 *
	 * @throws  \RuntimeException
	 */
	public function deleteFile()
	{
		$id = $this->getState('id', 0);

		if ((!is_numeric($id)) || ($id <= 0))
		{
			throw new \RuntimeException(Text::_('COM_AKEEBA_BUADMIN_ERROR_INVALIDID'), 500);
		}

		$stat = Platform::getInstance()->get_statistics($id);
		$allFiles = Factory::getStatistics()->get_all_filenames($stat, false);

		// Remove the custom log file if necessary
		if (!is_null($stat))
		{
			$this->_deleteLogs($stat);
		}

		// Make sure we have some files
		if (empty($allFiles))
		{
			return true;
		}

		// Get a reference to the filesystem abstraction
		$fs = $this->container->fileSystem;

		// Set the default status
		$status = true;

		// Delete all archive files
		foreach ($allFiles as $filename)
		{
			try
			{
				$fs->delete($filename);
			}
			catch (\Exception $e)
			{
				// Ignore file deletion failure
				$status = false;
			}
		}

		return $status;
	}

	/**
	 * Deletes the backup-specific log files of a stats record
	 *
	 * @param   array   $stat  The array holding the backup stats record
	 *
	 * @return  void
	 */
	protected function _deleteLogs(array $stat)
	{
		// We can't delete logs if there is no backup ID in the record
		if (!isset($stat['backupid']) || empty($stat['backupid']))
		{
			return;
		}

		$logFileName = 'akeeba.' . $stat['tag'] . '.' . $stat['backupid'] . '.log';

		$logPath = dirname($stat['absolute_path']) . '/' . $logFileName;

		$fs = $this->container->fileSystem;

		try
		{
			$fs->delete($logPath);
		}
		catch (\Exception $e)
		{
			// Ignore file deletion failure
		}
	}

	/**
	 * Get a pagination object
	 *
	 * @param   array  $filters  Any filters to use
	 *
	 * @return  Pagination
	 */
	public function &getPagination($filters = null)
	{
		if (!is_object($this->pagination))
		{
			// Prepare pagination values
			$total = Platform::getInstance()->get_statistics_count($filters);
			$limitStart = $this->getState('limitStart');
			$limit = $this->getState('limit');

			// Create the pagination object
			$this->pagination = new Pagination($total, $limitStart, $limit, 10, $this->container->application);
		}

		return $this->pagination;
	}

	/**
	 * Gets the post-processing engine for each backup profile
	 *
	 * @return  array  Key/value where key=profile ID, value=post-processing engine
	 */
	public function getPostProcessingEnginePerProfile()
	{
		// Cache the current profile
		$currentProfileID = Platform::getInstance()->get_active_profile();

		$db = $this->container->db;

		$query = $db->getQuery(true)
			->select($db->qn('id'))
			->from($db->qn('#__ak_profiles'));
		$db->setQuery($query);
		$profiles = $db->loadColumn();

		$engines = array();

		foreach($profiles as $profileID)
		{
			Platform::getInstance()->load_configuration($profileID);
			$pConf = Factory::getConfiguration();
			$engines[$profileID] = $pConf->get('akeeba.advanced.proc_engine');
		}

		Platform::getInstance()->load_configuration($currentProfileID);

		return $engines;
	}

	public function hideRestorationInstructionsModal()
	{
		$config = $this->container->appConfig;
		$config->set('options.show_howtorestoremodal', 0);
		$config->saveConfiguration();
	}
}