<?php
/**
 * @package     Awf
 * @copyright   2014-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 *
 * This class is adapted from the Joomla! Framework
 */

namespace Awf\Database;

/**
 * Database Interface
 *
 * @codeCoverageIgnore
 */
interface DatabaseInterface
{
	/**
	* Test to see if the connector is available.
	*
	* @return  boolean  True on success, false otherwise.
	*/
	public static function isSupported();
}