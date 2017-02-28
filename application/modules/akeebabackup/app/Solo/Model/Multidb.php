<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model;

use Awf\Database\Driver;
use Awf\Html\Select;
use Awf\Mvc\Model;
use Awf\Text\Text;
use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;

class Multidb extends Model
{
	/**
	 * Returns an array containing a list of database definitions
	 *
	 * @return  array  Array of definitions; The key contains the internal root name, the data is the database
	 *                 configuration data.
	 */
	public function get_databases()
	{
		// Get database inclusion filters
		$filter = Factory::getFilterObject('multidb');
		$database_list = $filter->getInclusions('db');

		return $database_list;
	}

    /**
     * Checks if a filter is already applied
     *
     * @param   array   $newFilter  Indexed array containing the filter data
     *
     * @return  bool    True if the filter already exists
     */
    public function filterExists($newFilter)
    {
        // Sanity checks
        if(!isset($newFilter['host']) || !isset($newFilter['database']) || !isset($newFilter['prefix']))
        {
            return false;
        }

        $filters = $this->get_databases();

        foreach($filters as $filter)
        {
            // If I have a filter with the same host, db name and table prefix, it means that they're the same
            if(
                ($newFilter['host'] == $filter['host']) &&
                ($newFilter['database'] == $filter['database']) &&
                ($newFilter['prefix'] == $filter['prefix'])
            )
            {
                return true;
            }
        }

        return false;
    }

	/**
	 * Delete a database definition
	 *
	 * @param   string  $root  The name of the database root key to remove
	 *
	 * @return  boolean  True on success
	 */
	public function remove($root)
	{
		$filter = Factory::getFilterObject('multidb');
		$success = $filter->remove($root, null);
		$filters = Factory::getFilters();

		if ($success)
		{
			$filters->save();
		}

		return $success;
	}

	/**
	 * Creates a new database definition
	 *
	 * @param   string  $root
	 * @param   array   $data
	 *
	 * @return  boolean
	 */
	public function setFilter($root, $data)
	{
		$filter = Factory::getFilterObject('multidb');
		$success = $filter->set($root, $data);
		$filters = Factory::getFilters();

		if ($success)
		{
			$filters->save();
		}

		return $success;
	}

	/**
	 * Tests the connectivity to a database
	 *
	 * @param   array  $data
	 *
	 * @return  array  Status array: 'status' is true on success, 'message' contains any error message while connecting
	 *                 to the database
	 */
	public function test($data)
	{
		$error = '';

		try
		{
			$db = Factory::getDatabase($data);
			if ($db->getErrorNum() > 0)
			{
				$error = $db->getErrorMsg();
			}
		}
		catch (\Exception $e)
		{
			$error = $e->getMessage();
		}

		if (
			empty($data['driver']) || empty($data['host']) || empty($data['user']) || empty($data['password'])
			|| empty($data['database'])
		)
		{
			return array(
				'status'  => false,
				'message' => Text::_('COM_AKEEBA_MULTIDB_ERR_MISSINGINFO'),
			);
		}

		return array(
			'status'  => empty($error),
			'message' => $error
		);
	}

	/**
	 * AJAX request proxy
	 *
	 * @return   array|boolean
	 */
	public function doAjax()
	{
		$action = $this->getState('action');
		$verb = array_key_exists('verb', $action) ? $action['verb'] : null;

		$ret_array = array();

		switch ($verb)
		{
			// Set a filter (used by the editor)
			case 'set':
				$ret_array = $this->setFilter($action['root'], $action['data']);
				break;

			// Remove a filter (used by the editor)
			case 'remove':
				$ret_array = array('success' => $this->remove($action['root']));
				break;

			// Test connection (used by the editor)
			case 'test':
				$ret_array = $this->test($action['data']);
				break;
		}

		return $ret_array;
	}

	public function getDatabaseDriverOptions()
	{
		$connectors = Driver::getConnectors();
		$options = array();

		foreach ($connectors as $connector)
		{
			$options[] = Select::option(strtolower($connector), Text::_('SOLO_SETUP_LBL_DATABASE_DRIVER_' . $connector));
		}

		return $options;
	}
}