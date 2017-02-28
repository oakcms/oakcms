<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 * @copyright Copyright (c)2006-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 *
 */

namespace Akeeba\Engine\Scan;

use Akeeba\Engine\Base\Object;

// Protection against direct access
defined('AKEEBAENGINE') or die();

abstract class Base extends Object
{
	/**
	 * Gets all the files of a given folder
	 *
	 * @param   string   $folder    The absolute path to the folder to scan for files
	 * @param   integer  $position  The position in the file list to seek to. Use null for the start of list.
	 *
	 * @return  array  A simple array of files
	 */
	abstract public function &getFiles($folder, &$position);

	/**
	 * Gets all the folders (subdirectories) of a given folder
	 *
	 * @param   string   $folder    The absolute path to the folder to scan for files
	 * @param   integer  $position  The position in the file list to seek to. Use null for the start of list.
	 *
	 * @return  array  A simple array of folders
	 */
	abstract public function &getFolders($folder, &$position);
}