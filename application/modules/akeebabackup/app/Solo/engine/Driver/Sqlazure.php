<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2006-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 */

namespace Akeeba\Engine\Driver;

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * SQL Azure database driver
 *
 * Based on Joomla! Platform 11.2
 */
class Sqlazure extends Sqlsrv
{
	/**
	 * The name of the database driver.
	 *
	 * @var    string
	 */
	public $name = 'sqlazure';

}
