<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2006-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 */

namespace Akeeba\Engine\Driver\Query;

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * Query Building Class.
 *
 * Based on Joomla! Platform 11.3
 */
class Pdomysql extends Mysqli implements Limitable
{
}
