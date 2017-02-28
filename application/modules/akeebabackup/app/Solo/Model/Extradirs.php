<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model;

use Awf\Mvc\Model;
use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;

class Extradirs extends Model
{
	/**
	 * Returns an array containing a list of directories definitions
	 *
	 * @return  array  Array of definitions; The key contains the internal root name, the data is the directory path
	 */
	public function get_directories()
	{
		// Get database inclusion filters
		$filter = Factory::getFilterObject('extradirs');
		$directories_list = $filter->getInclusions('dir');

		return $directories_list;
	}

	/**
	 * Delete a database definition
	 *
	 * @param   string  $uuid  The name of the extradirs filter root key (UUID) to remove
	 *
	 * @return  boolean  True on success
	 */
	public function remove($uuid)
	{
		if (empty($uuid))
		{
			// Special case: New row is added, so the GUI tries to delete the default (empty) record
			$success = true;
		}
		else
		{
			// Normal delete
			$filter = Factory::getFilterObject('extradirs');
			$success = $filter->remove($uuid, null);
			$filters = Factory::getFilters();

			if ($success)
			{
				$filters->save();
			}
		}

		return array('success' => $success, 'newstate' => true);
	}

	/**
	 * Creates a new database definition
	 *
	 * @param   string  $uuid
	 * @param   array   $data
	 *
	 * @return  boolean
	 */
	public function setFilter($uuid, $data)
	{
		$filter = Factory::getFilterObject('extradirs');
		$success = $filter->set($uuid, $data);
		$filters = Factory::getFilters();

		if ($success)
		{
			$filters->save();
		}

		return array('success' => $success, 'newstate' => false);
	}

	public function doAjax()
	{
		$action = $this->getState('action');
		$verb = array_key_exists('verb', $action) ? $action['verb'] : null;

		$ret_array = array();

		switch ($verb)
		{
			// Set a filter (used by the editor)
			case 'set':
				$new_data = array(
					0 => $action['root'],
					1 => $action['data']
				);
				// Set the new root
				$ret_array = $this->setFilter($action['uuid'], $new_data);
				break;

			// Remove a filter (used by the editor)
			case 'remove':
				$ret_array = $this->remove($action['uuid']);
				break;
		}

		return $ret_array;
	}
} 