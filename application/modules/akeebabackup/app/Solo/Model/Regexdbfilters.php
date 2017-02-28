<?php
/**
 * @package        solo
 * @copyright      2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license        GNU GPL version 3 or later
 */

namespace Solo\Model;

use Awf\Mvc\Model;
use Awf\Text\Text;
use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;

class Regexdbfilters extends Model
{
	/**
	 * Returns an array containing a mapping of db root names and their human-readable representation
	 *
	 * @return  array  Array of objects; "value" contains the root name, "text" the human-readable text
	 */
	public function get_roots()
	{
		// Get database inclusion filters
		$filters = Factory::getFilters();
		$database_list = $filters->getInclusions('db');

		$ret = array();

		foreach ($database_list as $name => $definition)
		{
			$root = $definition['host'];
			if (!empty($definition['port']))
			{
				$root .= ':' . $definition['port'];
			}
			$root .= '/' . $definition['database'];

			if ($name == '[SITEDB]')
			{
				$root = Text::_('COM_AKEEBA_DBFILTER_LABEL_SITEDB');
			}

			$ret[] = (object)array(
				'value'		=> $name,
				'text'		=> $root
			);
		}

		return $ret;
	}

	/**
	 * Returns an array containing a list of regex filters and their respective type for a given root
	 *
	 * @param   string  The root to get the filters for
	 *
	 * @return  array  Array of definitions
	 */
	public function get_regex_filters($root)
	{
		// These are the regex filters I know of
		$known_filters = array(
			'regextables',
			'regextabledata'
		);

		// Filters already set
		$set_filters = array();

		// Loop all filter types
		foreach ($known_filters as $filter_name)
		{
			// Get this filter type's set filters
			$filter = Factory::getFilterObject($filter_name);
			$temp_filters = $filter->getFilters($root);

			// Merge this filter type's regular expressions to the list
			if (count($temp_filters))
			{
				foreach ($temp_filters as $new_regex)
				{
					$set_filters[] = array(
						'type' => $filter_name,
						'item' => $new_regex
					);
				}
			}

		}

		return $set_filters;
	}

	/**
	 * Delete a regex filter
	 *
	 * @param   string  $type    Filter type
	 * @param   string  $root    The filter's root
	 * @param   string  $string  The filter string to remove
	 *
	 * @return  boolean  True on success
	 */
	public function remove($type, $root, $string)
	{
		$filter = Factory::getFilterObject($type);
		$success = $filter->remove($root, $string);

		if ($success)
		{
			$filters = Factory::getFilters();
			$filters->save();
		}

		return $success;
	}

	/**
	 * Creates a new regex filter
	 *
	 * @param   string  $type    Filter type
	 * @param   string  $root    The filter's root
	 * @param   string  $string  The filter string to remove
	 *
	 * @return  boolean  True on success
	 */
	public function setFilter($type, $root, $string)
	{
		$knownFilters = $this->get_regex_filters($root);
		$item = array('type' => $type, 'item' => $string);
		if (in_array($item, $knownFilters))
		{
			$success = true;
		}
		else
		{
			$filter = Factory::getFilterObject($type);
			$success = $filter->set($root, $string);

			if ($success)
			{
				$filters = Factory::getFilters();
				$filters->save();
			}
		}

		return $success;
	}

	/**
	 * AJAX proxy
	 *
	 * @return  array
	 */
	public function doAjax()
	{
		$action = $this->getState('action');
		$verb = array_key_exists('verb', $action) ? $action['verb'] : null;

		$ret_array = array();

		switch ($verb)
		{
			// Produce a list of regex filters
			case 'list':
				$ret_array = $this->get_regex_filters($action['root']);
				break;

			// Set a filter (used by the editor)
			case 'set':
				$ret_array = array('success' => $this->setFilter($action['type'], $action['root'], $action['node']));
				break;

			// Remove a filter (used by the editor)
			case 'remove':
				$ret_array = array('success' => $this->remove($action['type'], $action['root'], $action['node']));
				break;
		}

		return $ret_array;
	}
} 