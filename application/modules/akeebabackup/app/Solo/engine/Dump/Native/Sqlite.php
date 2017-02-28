<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 *
 * @copyright Copyright (c)2006-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 *
 */

namespace Akeeba\Engine\Dump\Native;

// Protection against direct access
defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Dump\Reverse\Sqlite as ReverseDumpEngine;
use Akeeba\Engine\Factory;
use Psr\Log\LogLevel;


/**
 * Dump class for the "None" database driver (ie no database used by the application)
 *
 */
class Sqlite extends ReverseDumpEngine
{
	public function __construct()
	{
		parent::__construct();

		Factory::getLog()->log(LogLevel::INFO, "There is no native engine for backing up SQLite databases. Using the Reverse Engineering class instead.");
	}

}