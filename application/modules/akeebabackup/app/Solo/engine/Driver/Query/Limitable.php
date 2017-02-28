<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2006-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 *
 */

namespace Akeeba\Engine\Driver\Query;

// Protection against direct access
defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Driver\Query\Base as BaseQuery;

/**
 * Query Limitable Interface.
 * Adds bind/unbind methods as well as a getBounded() method
 * to retrieve the stored bounded variables on demand prior to
 * query execution.
 *
 * Based on Joomla! Platform 11.2
 */
interface Limitable
{
	/**
	 * Method to modify a query already in string format with the needed
	 * additions to make the query limited to a particular number of
	 * results, or start at a particular offset. This method is used
	 * automatically by the __toString() method if it detects that the
	 * query implements the Limitable interface.
	 *
	 * @param   string  $query  The query in string format
	 * @param   integer $limit  The limit for the result set
	 * @param   integer $offset The offset for the result set
	 *
	 * @return  string
	 *
	 * @since   12.1
	 */
	public function processLimit($query, $limit, $offset = 0);

	/**
	 * Sets the offset and limit for the result set, if the database driver supports it.
	 *
	 * Usage:
	 * $query->setLimit(100, 0); (retrieve 100 rows, starting at first record)
	 * $query->setLimit(50, 50); (retrieve 50 rows, starting at 50th record)
	 *
	 * @param   integer $limit  The limit for the result set
	 * @param   integer $offset The offset for the result set
	 *
	 * @return  BaseQuery  Returns this object to allow chaining.
	 *
	 * @since   12.1
	 */
	public function setLimit($limit = 0, $offset = 0);
}
